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
    /* ╔═════════════════════ A. GIỎ HÀNG CƠ BẢN ═════════════════════╗ */

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
        $row = DonHangSanPham::findOrFail($id);

        match ($req->action) {
            'increase' => $row->increment('soluong'),
            'decrease' => $row->update(['soluong' => max(1, $row->soluong - 1)]),
            default    => null,
        };

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
    return $this->checkout($request);
}

    /* ╔═════════════════════ B. CHECKOUT (chỉ giỏ) ══════════════════╗ */
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

    /* ╔═════════════════════ C. LUỒNG “MUA NGAY” ═══════════════════╗ */
    public function buynow(Request $request)
    {
        $user = auth()->user() ?? abort(401);
        /* --- 1. Nếu submit chỉ để ++/--/xóa item giỏ thì xử luôn rồi hiển thị lại --- */
        if ($request->filled('donhangsp_id')) {
            $row = DonHangSanPham::findOrFail($request->donhangsp_id);
            match ($request->action) {
                'increase' => $row->increment('soluong'),
                'decrease' => $row->update(['soluong' => max(1, $row->soluong - 1)]),
                'remove'   => $row->delete(),
                default    => null,
            };
            if (!$request->filled('product_id')) {      // không có buy-now → quay lại cart/checkout
                return $this->checkout($request);
            }
        }

        /* --- 2. Lấy tham số buy-now --- */
        //$user       = auth()->user() ?? abort(401);
        $productId  = $request->product_id;
        $size       = $request->size;
        $colorId    = $request->color_id ?: $request->mausac;
        $quantity   = max(1, (int) $request->quantity);
        $action     = $request->action;
        $voucherId  = $request->order_voucher;

        if ($action === 'increase')       $quantity++;
        elseif ($action === 'decrease')   $quantity = max(1, $quantity-1);
        elseif ($action === 'remove')     return redirect()->route('layouts.chinh');

        $product    = SanPham::findOrFail($productId);
        $colorRec   = ColorImage::with('mauSac')
                       ->where('sanpham_id', $productId)->where('mausac_id', $colorId)->first();

        $colorName  = $request->color_name
                     ?: optional($colorRec?->mauSac)->mausac ?: 'Mặc định';
        $filename   = $request->image_path
                     ?: ($colorRec?->image_path ?: $product->thumbnail ?: 'default.jpg');
        $imageUrl   = asset("images/{$filename}");

        /* --- 3. gom toàn bộ item giỏ hiện tại --- */
        $cartItems = [];
        $donHang   = DonHang::where('nguoidung_id', $user->id)
                     ->where('trangthai', 1)
                     ->with(['chiTiet.sanPham','chiTiet.mauSac'])
                     ->first();

        if ($donHang) {
            foreach ($donHang->chiTiet as $it) {
                $cartItems[] = [
                    'sanpham_id'   => $it->sanpham_id,
                    'image_url'    => asset('images/'.$it->hinh_anh),
                    'name'         => $it->sanPham->ten,
                    'quantity'     => $it->soluong,
                    'price'        => $it->dongia,
                    'size'         => $it->size,
                    'mausac'       => optional($it->mauSac)->mausac ?: $it->mausac,
                    'total'        => $it->dongia * $it->soluong,
                    'donhangsp_id' => $it->id,
                    'is_buynow'    => false,
                ];
            }
        }

        /* --- 4. chèn item mua-ngay lên đầu --- */
        array_unshift($cartItems, [
            'sanpham_id'   => $productId,
            'image_url'    => $imageUrl,
            'name'         => $product->ten,
            'quantity'     => $quantity,
            'price'        => $product->gia_ban,
            'size'         => $size,
            'mausac'       => $colorName,
            'total'        => $product->gia_ban * $quantity,
            'donhangsp_id' => null,
            'is_buynow'    => true,
        ]);

        /* --- 5. tính tổng, voucher, ship --- */
        $summary = $this->buildSummaryFromArray($cartItems, $voucherId);

        /* --- 6. render view --- */
        return view('layouts.thanhtoan', [
            'user'              => $user,
            'order'             => $summary['order'],
            'availableVouchers' => $summary['availableVouchers'],
            'mode'              => 'buynow',
            'buyNowData'        => [
                'product_id'  => $productId,
                'quantity'    => $quantity,
                'size'        => $size,
                'color_id'    => $colorId,
                'color_name'  => $colorName,
                'image_path'  => $filename,
            ],
        ]);
    }

    /* ╔═════════════════════ D. THANH TOÁN ══════════════════════════╗ */
    public function thanhtoan(Request $request)
{
    $user = auth()->user() ?? abort(401);
    $request->validate([
        'payment_method' => 'required|in:cod,vnpay',
        'order_voucher'  => 'nullable|integer',
        'buyNowData'     => 'nullable|array',
    ]);

    // 1) Lấy hoặc tạo đơn “mở” (trangthai = 1)
    $donHang = DonHang::firstOrCreate(
        ['nguoidung_id' => $user->id, 'trangthai' => 1],
        [
            'madon'            => 'DH'.now()->format('YmdHis').Str::random(3),
            'trangthaidonhang' => 'chuadathang',
            'tongtien'         => 0,
        ]
    );

    // 2) Gom toàn bộ items: mua-ngay + giỏ hàng
    $items = [];

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
    foreach ($items as $item) {
        DonHangSanPham::create([
            'donhang_id' => $donHang->id,
            'sanpham_id' => $item['sanpham_id'],
            'dongia'     => $item['price'],
            'soluong'    => $item['quantity'],
            'size'       => $item['size'],
            'mausac'     => $item['mausac'],
            'hinh_anh'   => $item['hinh_anh'],
        ]);
    }

    // 4) Tính lại summary
    $donHang->load('chiTiet');
    $summary = $this->buildSummaryFromCart($donHang->chiTiet, $request->order_voucher);

    // 5) Cập nhật đơn thành “đã đặt” (trangthai = 2)
    $donHang->update([
        'tongtien'               => $summary['order']['tongCuoi'],
        'gia_giam'               => $summary['order']['voucherGiam'],
        'phuong_thuc_thanh_toan' => $request->payment_method,
        'trangthai'              => 2,
        'thoigianthem'           => now(),
    ]);

    // 6) Nếu COD, redirect về chi tiết, nếu VNPAY thì redirect sang VNPAY
        return redirect()
            ->route('donhang.show', $donHang->id)
            ->with('success', 'Đặt hàng thành công (Thanh toán khi nhận hàng)!');
}

    /* ╔═════════════════════ E. API ĐỔI ĐỊA CHỈ (AJAX) ══════════════╗ */
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

    /* ╔═════════════════════ F. HÀM TRỢ GIÚP ════════════════════════╗ */

    /** Tính tổng cho luồng giỏ */
    protected function buildSummaryFromCart($collection, $voucherId = null): array
    {
        $items = $collection->map(function ($it) {
            return [
                'sanpham_id'   => $it->sanpham_id,
                'image_url'    => asset('images/'.$it->hinh_anh),
                'name'         => $it->sanPham->ten,
                'quantity'     => $it->soluong,
                'price'        => $it->dongia,
                'size'         => $it->size,
                'mausac'       => optional($it->mauSac)->mausac ?: $it->mausac,
                'total'        => $it->dongia * $it->soluong,
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
        }
    });
    Auth::loginUsingId($pending['user_id']);
    // 2.4. Clear session và show view success
    Session::forget('pending_order');
    return view('payment.success', [
        'order' => $order,
    ]);
}
}
