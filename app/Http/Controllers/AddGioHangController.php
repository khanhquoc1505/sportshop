<?php

namespace App\Http\Controllers;

use App\Models\{SanPham, DonHang, DonHangSanPham, Voucher, ColorImage};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Auth;

class AddGioHangController extends Controller
{
  

    /** Thêm vào giỏ */
    public function themgiohang(Request $request, SanPham $product)
    {
        $user = auth()->user() ?? abort(401);

        /* 1. tìm / tạo đơn “đang mở” (trangthai = 1) */
        $donHang = DonHang::firstOrCreate(
            ['nguoidung_id' => $user->id, 'trangthai' => 1],
            [
                'madon'            => 'DH'.now()->format('YmdHis').Str::random(3),
                'trangthaidonhang' => 'chuadathang',
                'tongtien'         => 0,
            ]
        );

        /* 2. xác định màu / size */
        $size     = $request->size;
        $colorId  = $request->mausac;
        $qty      = max(1, (int)$request->quantity);

        $colorRec = ColorImage::with('mauSac')
                    ->where('sanpham_id', $product->id)
                    ->where('mausac_id',  $colorId)
                    ->first();

        $colorName = optional($colorRec?->mauSac)->mausac ?: 'Mặc định';
        $imagePath = $colorRec?->image_path ?: $product->thumbnail ?: 'default.jpg';

        /* 3. đã có item? tăng số lượng – chưa có thì tạo */
        $detail = DonHangSanPham::firstOrNew([
            'donhang_id' => $donHang->id,
            'sanpham_id' => $product->id,
            'size'       => $size,
            'mausac'     => $colorName,
        ]);
        $detail->hinh_anh = $imagePath;
        $detail->dongia   = $product->gia_ban;
        $detail->soluong  = ($detail->exists ? $detail->soluong : 0) + $qty;
        $detail->save();

        return back()->with('success', 'Đã thêm vào giỏ hàng');
    }

    /** Trang giỏ */
    public function showgiohang()
    {
        $user = auth()->user() ?? abort(401);

        $donHang = DonHang::where('nguoidung_id', $user->id)
                  ->where('trangthai', 1)
                  ->with(['chiTiet.sanPham','chiTiet.mauSac'])
                  ->first();

        $summary = $this->buildSummaryFromCart($donHang?->chiTiet ?? collect(), null);

        return view('layouts.giohang', [
            'donhang'     => $donHang,
            'user'        => $user,
            'tongSau'     => $summary['tongSau'],
            'phiGiaoHang' => $summary['phiShip'],
            'tongCuoi'    => $summary['tongCuoi'],
        ]);
    }

    /** + / – trong giỏ */
     public function update(Request $req, $id)
    {
        $row    = DonHangSanPham::findOrFail($id);
        $action = $req->action;

        // Lấy stock theo variant đang có
        $sizeId  = DB::table('kichco')->where('size', $row->size)->value('id');
        $colorId = DB::table('mausac')->where('mausac', $row->mausac)->value('id');
        $stock   = DB::table('sanpham_kichco_mausac')
            ->where('sanpham_id', $row->sanpham_id)
            ->where('kichco_id', $sizeId)
            ->where('mausac_id', $colorId)
            ->value('sl');

        if ($action === 'increase') {
            if (is_null($stock) || $row->soluong < $stock) {
                $row->increment('soluong');
            } else {
                return back()->with('error', "Không thể tăng, tồn kho chỉ còn {$stock} món.");
            }
        } elseif ($action === 'decrease') {
            $row->update(['soluong' => max(1, $row->soluong - 1)]);
        }

        return back();
    }

    /** Xoá trong giỏ */
    public function remove(Request $request, int $id)
{
    // Xóa item
    DonHangSanPham::findOrFail($id)->delete();
    // Nếu đây là luồng mua-ngay, gọi nội bộ buynow() để render lại view
    if ($request->filled('product_id')) {
        return $this->buynow($request);
    }

    // Ngược lại (luồng giỏ), gọi lại checkout()
    return redirect()->route('cart.index');
}

    /*  CHECKOUT (chỉ giỏ)*/
    public function checkout(Request $request)
    {
        $user = auth()->user() ?? abort(401);

        $donHang = DonHang::with(['chiTiet.sanPham','chiTiet.mauSac'])
                  ->where('nguoidung_id', $user->id)
                  ->where('trangthai', 1)
                  ->firstOrFail();

        $voucherId = $request->order_voucher;
        $summary   = $this->buildSummaryFromCart($donHang->chiTiet, $voucherId);

        return view('layouts.thanhtoan', [
            'user'              => $user,
            'order'             => $summary['order'],
            'availableVouchers' => $summary['availableVouchers'],
            'mode'              => 'cart',
            'buyNowData'        => [],
        ]);
    }
     public function __construct()
    {
        // Chỉ bảo vệ các action thay đổi DB
        $this->middleware('auth')->only([
          'themgiohang',
          'update',
          'remove',
          'thanhtoan',
          'vnpay_payment',
          'vnpayReturn',
        ]);
    }

    /* LUỒNG “MUA NGAY”*/
   public function buynow(Request $request)
    {
        //dd($request->only(['product_id','size','mausac','quantity','action']));
        // 1) Lấy user
        $user   = auth()->user();
        $action = $request->input('action');
        

        // 2) Nếu thao tác trên DB-item (user đã login)
        if ($user && $request->filled('donhangsp_id')) {
            $row = DonHangSanPham::findOrFail($request->donhangsp_id);
            match ($action) {
                'increase' => $row->increment('soluong'),
                'decrease' => $row->update(['soluong' => max(1, $row->soluong - 1)]),
                'remove'   => $row->delete(),
            };
            if (! $request->filled('product_id')) {
                return redirect()->route('cart.checkout');
            }
        }

        // 3) Đọc trực tiếp từ request
        $buyNow = [
            'product_id'    => $request->input('product_id'),
            'size'          => $request->input('size'),
            'mausac'        => $request->input('mausac'),
            //'mausac' => $request->input('color_id'),
            'quantity'      => max(1, (int)$request->input('quantity', 1)),
            'order_voucher' => $request->input('order_voucher'),
            'color_name'    => $request->input('color_name'),
            'image_path'    => $request->input('image_path'),
        ];

        // 4) Lấy color_name, image_path
        $product = SanPham::findOrFail($buyNow['product_id']);
        if (! $buyNow['image_path']) {
            $rec = ColorImage::with('mauSac')
                ->where('sanpham_id', $product->id)
                ->where('mausac_id', $buyNow['mausac'])
                ->first();
        $buyNow['color_name'] = optional($rec?->mauSac)->mausac ?: 'Mặc định';
        $buyNow['image_path'] = $rec?->image_path ?: $product->thumbnail;
        }
        // 5) Tính stock
        $sizeId  = DB::table('kichco')->where('size', $buyNow['size'])->value('id');
        $colorId = $buyNow['mausac']; // đây là ID màu bạn đã đọc ở bước 3
       $buyNow['stock'] = DB::table('sanpham_kichco_mausac')
            ->where('sanpham_id', $product->id)
            ->where('kichco_id',  $sizeId)
            ->where('mausac_id',  $colorId)
            ->value('sl')
            ?? 0;

        // 6) Nếu guest mua-ngay, chỉ điều chỉnh số lượng tại đây
        if (! $request->filled('donhangsp_id')) {
        // đây là luồng MUA NGAY, cho phép tăng/giảm bất kể login hay không
        if ($action === 'increase' && $buyNow['quantity'] < $buyNow['stock']) {
            $buyNow['quantity']++;
        }
        if ($action === 'decrease') {
            $buyNow['quantity'] = max(1, $buyNow['quantity'] - 1);
        }
        if ($action === 'remove') {
            return redirect()->route('cart.index');
        }
    }

        // 7) Load DB-items nếu có user
        $cartItems = [];
        if ($user) {
            $donHang = DonHang::firstOrCreate(
                ['nguoidung_id'=>$user->id,'trangthai'=>1],
                ['madon'=>'DH'.now()->format('YmdHis').Str::random(3),'tongtien'=>0,'trangthaidonhang'=>'chuadathang']
            );
            $donHang->load('chiTiet.sanPham','chiTiet.mauSac');
            foreach ($donHang->chiTiet as $it) {
                $stockDb = DB::table('sanpham_kichco_mausac')
                    ->where('sanpham_id',$it->sanpham_id)
                    ->where('kichco_id', DB::table('kichco')->where('size',$it->size)->value('id'))
                    ->where('mausac_id', DB::table('mausac')->where('mausac',$it->mausac)->value('id'))
                    ->value('sl');
                $cartItems[] = [
                    'sanpham_id'=>$it->sanpham_id,
                    'image_url'=>asset('images/'.$it->hinh_anh),
                    'name'=>$it->sanPham->ten,
                    'quantity'=>$it->soluong,
                    'price'=>$it->dongia,
                    'size'=>$it->size,
                    'mausac'=>$it->mausac,
                    'total'=>$it->dongia*$it->soluong,
                    'stock'=>$stockDb,
                    'donhangsp_id'=>$it->id,
                    'is_buynow'=>false,
                ];
            }
        }

        // 8) Đẩy mua-ngay lên đầu
        array_unshift($cartItems, [
            'sanpham_id'=>$product->id,
            'image_url'=>asset("images/{$buyNow['image_path']}"),
            'name'=>$product->ten,
            'quantity'=>$buyNow['quantity'],
            'price'=>$product->gia_ban,
            'size'=>$buyNow['size'],
            'mausac'=>$buyNow['color_name'],
            'total'=>$product->gia_ban*$buyNow['quantity'],
            'stock'=>$buyNow['stock'],
            'donhangsp_id'=>null,
            'is_buynow'=>true,
        ]);

        // 9) Summary + render
        $summary = $this->buildSummaryFromArray($cartItems, $buyNow['order_voucher']);
        return view('layouts.thanhtoan', [
            'user'=>$user,
            'order'=>$summary['order'],
            'availableVouchers'=>$summary['availableVouchers'],
            'mode'=>'buynow',
            'buyNowData'=>$buyNow,
        ]);
    }



    /* D. THANH TOÁN */
    public function thanhtoan(Request $request)
{
    $user = auth()->user() ?? abort(401);
    $request->validate([
        'payment_method' => 'required|in:cod,vnpay',
        'order_voucher'  => 'nullable|integer',
        'buyNowData'     => 'nullable|array',
    ]);
    $products = $request->input('products', []);

    // 1) Lấy hoặc tạo đơn “mở” (trangthai = 1)
    $donHang = DonHang::firstOrCreate(
        ['nguoidung_id' => $user->id, 'trangthai' => 1],
        [
            'madon'            => 'DH'.now()->format('YmdHis').Str::random(3),
            'trangthaidonhang' => 'chuadathang',
            'tongtien'         => 0,
        ]
    );
    // 2a) Nếu có mua ngay thì thêm trước
    $bn = $request->input('buyNowData', []);
    if (!empty($bn['product_id'])) {
        $items[] = [
            'sanpham_id' => $bn['product_id'],
            'quantity'   => $bn['quantity'],
            'price'      => SanPham::find($bn['product_id'])->gia_ban,
            'size'       => $bn['size'],
            'mausac'     => $bn['color_name'],
            'hinh_anh'   => $bn['image_path'],
        ];
    }

    // 2b) Lấy các item đang có trong giỏ (DB)
    $cartDetails = DonHangSanPham::where('donhang_id', $donHang->id)->get();
    foreach ($cartDetails as $d) {
        $items[] = [
            'sanpham_id' => $d->sanpham_id,
            'quantity'   => $d->soluong,
            'price'      => $d->dongia,
            'size'       => $d->size,
            'mausac'     => $d->mausac,
            'hinh_anh'   => $d->hinh_anh,
        ];
    }

    // 3) Xóa hết chi tiết cũ, rồi insert lại phân biệt size+mausac
    DonHangSanPham::where('donhang_id', $donHang->id)->delete();
    foreach ($products as $item) {
        DonHangSanPham::create([
            'donhang_id' => $donHang->id,
            'sanpham_id' => $item['id'],
            'dongia'     => $item['price'],
            'soluong'    => $item['quantity'],
            'size'       => $item['size'],
            'mausac'     => $item['mausac'],
            'hinh_anh'   => $item['image'],
        ]);
        // 2) Lookup mã kích cỡ
    $sizeId = DB::table('kichco')
               ->where('size', $item['size'])
               ->value('id');
    // Lookup mã màu sắc
    $colorId = DB::table('mausac')
                 ->where('mausac', $item['mausac'])
                 ->value('id');

    // 3) Nếu tìm ra cả 2 thì trừ sl
    if ($sizeId && $colorId) {
      DB::table('sanpham_kichco_mausac')
        ->where('sanpham_id', $item['id'])
        ->where('kichco_id',  $sizeId)
        ->where('mausac_id',  $colorId)
        ->decrement('sl',     $item['quantity']);
    }
    }
    
    // 4) Tính lại summary
    $donHang->load('chiTiet');
    $summary = $this->buildSummaryFromCart($donHang->chiTiet, $request->order_voucher);

    // 5) Cập nhật đơn thành “đã đặt” (trangthai = 2)
    $donHang->update([
        'tongtien'               => $summary['order']['tongCuoi'],
        'gia_giam'               => $summary['order']['voucherGiam'],
        'phuong_thuc_thanh_toan' => $request->payment_method,
        'trangthaidonhang'       =>'chuathanhtoan',
        'trangthai'              => 2,
        'thoigianthem'           => now(),
    ]);

    // 6) Nếu COD, redirect về chi tiết, nếu VNPAY thì redirect sang VNPAY
        return redirect()
            ->route('donhang.show', ['id' => $donHang->id, 'madon' => $donHang->madon])
            ->with('success', 'Đặt hàng thành công (Thanh toán khi nhận hàng)!');
}

    /*  API ĐỔI ĐỊA CHỈ  */
    public function updateAddress(Request $request)
    {
        $user = auth()->user() ?? abort(401);

        $request->validate([
            'ten_nguoi_dung' => 'required|string|max:255',
            'sdt'            => 'required|string|max:20',
            'dia_chi'        => 'required|string|max:255',
        ]);

        $user->update([
            'ten_nguoi_dung'=> $request->ten_nguoi_dung,
            'sdt'           => $request->sdt,
            'dia_chi'       => $request->dia_chi,
        ]);

        return response()->json(['success'=>true]);
    }

    /* HÀM TRỢ GIÚP */

    /** Tính tổng cho luồng giỏ */
    protected function buildSummaryFromCart($collection, $voucherId = null): array
    {
        $items = $collection->map(function ($it) {
            // lookup stock
        $sizeId  = DB::table('kichco')->where('size', $it->size)->value('id');
        $colorId = DB::table('mausac')->where('mausac', $it->mausac)->value('id');
        $stock   = DB::table('sanpham_kichco_mausac')
            ->where('sanpham_id', $it->sanpham_id)
            ->where('kichco_id', $sizeId)
            ->where('mausac_id', $colorId)
            ->value('sl');
            return [
                'sanpham_id'   => $it->sanpham_id,
                'image_url'    => asset('images/'.$it->hinh_anh),
                'name'         => $it->sanPham->ten,
                'quantity'     => $it->soluong,
                'price'        => $it->dongia,
                'size'         => $it->size,
                'mausac'       => optional($it->mauSac)->mausac ?: $it->mausac,
                'total'        => $it->dongia * $it->soluong,
                'stock'        => $stock,
                'donhangsp_id' => $it->id,
                'is_buynow'    => false,
            ];
        })->values()->all();

        return $this->buildSummaryFromArray($items, $voucherId);
    }

    /** Tính tổng cho 1 mảng item tuỳ ý (dùng lại cho buy-now) */
    protected function buildSummaryFromArray(array $items, $voucherId = null): array
    {
        $totalBefore = collect($items)->sum('total');
        $voucherGiam = 0;

        if ($voucherId) {
            $v = Voucher::where('id',$voucherId)
                 ->where('ngay_bat_dau','<=',now())
                 ->where('ngay_ket_thuc','>=',now())
                 ->where('min_order_value','<=',$totalBefore)
                 ->first();
            if ($v) {
                $voucherGiam = $v->loai==='percent'
                    ? $totalBefore * $v->soluong / 100
                    : min($v->soluong, $totalBefore);
            }
        }

        $tongSau  = $totalBefore - $voucherGiam;
        $phiShip  = $tongSau >= 300000 ? 0 : 19000;
        $tongCuoi = $tongSau + $phiShip;

        return [
            'order' => [
                'items'        => $items,
                'voucherGiam'  => $voucherGiam,
                'phiGiaoHang'  => $phiShip,
                'tongSau'      => $tongSau,
                'tongCuoi'     => $tongCuoi,
            ],
            'availableVouchers' => Voucher::where('ngay_bat_dau','<=',now())
                                   ->where('ngay_ket_thuc','>=',now())
                                   ->where('min_order_value','<=',$totalBefore)
                                   ->get(),
            'tongSau'      => $tongSau,
            'phiShip'      => $phiShip,
            'tongCuoi'     => $tongCuoi,
        ];
    }

    /** (tuỳ chọn) build URL VNPay */
     public function vnpay_payment(Request $request)
{
    // 1. Lấy type và items
    $user = auth()->user() ?? abort(401);
    $items = $request->input('products', []);
    $total = $request->input('total_vnpay', 0);
    $type  = count($items) === 1 && isset($items[0]['buy_now']) 
           ? 'buynow' 
           : 'cart';
    // (hoặc: $type = $request->has('products.0.id') ? 'cart':'buynow';)

    // 2. Lưu tạm vào DB hoặc session
    Session::put('pending_order', [
    'user_id'       => $user->id,
    'purchase_type' => $type,
    'items'         => $items,
    'total_bill'    => $total,      // ← đổi tên thành total_bill
]);
    Session::save();

    // 3. Build URL VNPAY
    $vnp_TmnCode    = "AZCBP9RR";
    $vnp_HashSecret = "KQQHZPW0XEKVF77IKLJ2D1HCBI42142Z";
    $vnp_Url        = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    $vnp_Returnurl  = route('vnpay.return');
    $vnp_TxnRef     = strtoupper(Str::random(8)).time();

    $inputData = [
    "vnp_Version"    => "2.1.0",
    "vnp_TmnCode"    => $vnp_TmnCode,
    "vnp_Amount"     => $total * 100,
    "vnp_Command"    => "pay",
    "vnp_CreateDate" => now()->format('YmdHis'),
    "vnp_CurrCode"   => "VND",
    "vnp_IpAddr"     => $request->ip(),
    "vnp_Locale"     => "vn",
    "vnp_OrderInfo"  => "GD:{$vnp_TxnRef}",
    "vnp_OrderType"  => "other",       // ← thêm dòng này
    "vnp_ReturnUrl"  => $vnp_Returnurl,
    "vnp_TxnRef"     => $vnp_TxnRef,
];
    // 1. Sort tham số
ksort($inputData);

$hashParts = [];
foreach ($inputData as $key => $value) {
    $hashParts[] = $key . '=' . rawurlencode($value);
}
$hashString = implode('&', $hashParts);

$secureHash = hash_hmac('sha512', $hashString, $vnp_HashSecret);

$queryParts = [];
foreach ($inputData as $key => $value) {
    $queryParts[] = $key . '=' . rawurlencode($value);
}
$queryParts[] = 'vnp_SecureHash=' . $secureHash;

$redirectUrl = $vnp_Url . '?' . implode('&', $queryParts);

return redirect()->away($redirectUrl);
}

    // 2. Callback VNPAY về, verify và lưu đơn
    public function vnpayReturn(Request $request)
{
    
    Log::info('VNPAY Return Callback', $request->all());

    // 2.1. Verify hash
    $rawQuery = $request->getQueryString();
    $plainData = preg_replace(
        ['/(&?vnp_SecureHashType=[^&]*)/i', '/(&?vnp_SecureHash=[^&]*)/i'],
        '', $rawQuery
    );
    $plainData = ltrim($plainData, '&');

    $computedHash = hash_hmac('sha512', $plainData, "KQQHZPW0XEKVF77IKLJ2D1HCBI42142Z");
    $returnedHash = $request->get('vnp_SecureHash', '');
    if (strtoupper($computedHash) !== strtoupper($returnedHash)) {
        Log::error('VNPAY Signature Mismatch', compact('returnedHash','computedHash','plainData'));
        return redirect()->route('cart.index')
                         ->with('error', 'Xác thực chữ ký VNPAY thất bại.');
    }

    if ($request->get('vnp_ResponseCode') !== '00') {
        return redirect()->route('cart.index')
                         ->with('error', 'Thanh toán không thành công: '.$request->get('vnp_ResponseCode'));
    }

    // 2.2. Lấy dữ liệu pending
    $pending = Session::get('pending_order', []);
    $type    = $pending['purchase_type'] ?? 'cart';
    $items   = $pending['items']         ?? [];
    $total   = $pending['total_bill']    ?? 0;
    
    if (empty($items)) {
        Log::error('Pending order missing or empty', ['pending'=>$pending]);
        return redirect()->route('cart.index')
                         ->with('error', 'Không tìm thấy đơn tạm.');
    }

    // 2.3. Lưu vào DB trong transaction
    DB::transaction(function () use ($pending, $type, $items, $total, $request, &$order) {
        $userId = $pending['user_id'];
        $txRef  = $request->get('vnp_TxnRef');

        if ($type === 'cart') {
            // đơn giỏ hàng cũ
            $order = DonHang::where('nguoidung_id', $userId)
                            ->where('trangthai', 1)
                            ->first();
        } else {
            $order = null;
        }

        if ($order) {
            // cập nhật đơn chờ
            $order->update([
                'tongtien'               => $total,
                'phuong_thuc_thanh_toan' => 'vnpay',
                'trangthai'              => 3,
                'trangthaidonhang'       => 'dathanhtoan',
                'thoigianthem'           => now(),
            ]);
        } else {
            // tạo đơn mới
            $order = DonHang::create([
                'nguoidung_id'           => $userId,
                'madon'                  => 'DH'.now()->format('YmdHis').Str::random(4),
                'tongtien'               => $total,
                'phuong_thuc_thanh_toan' => 'vnpay',
                'trangthai'              => 3,
                'trangthaidonhang'       => 'dathanhtoan',
                'thoigianthem'           => now(),
            ]);
        }

        // xóa chi tiết cũ và insert mới
        DonHangSanPham::where('donhang_id', $order->id)->delete();
        foreach ($items as $item) {
            DonHangSanPham::create([
                'donhang_id' => $order->id,
                'sanpham_id' => $item['id'],
                'dongia'     => $item['price'],
                'soluong'    => $item['quantity'],
                'size'       => $item['size']  ?? null,
                'mausac'     => $item['mausac']?? null,
                'hinh_anh'   => $item['image'] ?? null,
            ]);
           // 2) Lookup mã kích cỡ
            $sizeId = DB::table('kichco')
               ->where('size', $item['size'])
               ->value('id');
            // Lookup mã màu sắc
            $colorId = DB::table('mausac')
                 ->where('mausac', $item['mausac'])
                 ->value('id');

            // 3) Nếu tìm ra cả 2 thì trừ sl
            if ($sizeId && $colorId) {
                DB::table('sanpham_kichco_mausac')
                ->where('sanpham_id', $item['id'])
                ->where('kichco_id',  $sizeId)
                ->where('mausac_id',  $colorId)
                ->decrement('sl',     $item['quantity']);
            }
        }
    });
    
    Auth::loginUsingId($pending['user_id']);
    // 2.4. Clear session và show view success
    Session::forget('pending_order');
    // return view('donhang.show', [
    //     'order' => $order,
    //     'madon' => $order->madon,
    // ]);
    return redirect()
            ->route('donhang.show', ['id' => $order->id, 'madon' => $order->madon])
            ->with('success', 'Đặt hàng thành công (Thanh toán khi nhận hàng)!');
}
}
