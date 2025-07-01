<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\RevenueExport;
use Illuminate\Support\Facades\DB;
use App\Models\SanPham;
use App\Models\DonHangChiTiet;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\Loai;
use App\Models\DonHang;
use App\Models\Voucher;
use App\Models\Member;
use App\Models\Comment;
use App\Models\NhapKho;
use App\Models\ImgSanPham;
use App\Models\KichCo;
use App\Models\MauSac;
use App\Models\Feedback;
use App\Models\SanPhamKichCoMauSac;

class AdminController extends Controller
{
    public function dashboard()
    {
        //
        // 1) Top 5 sản phẩm theo số lượng
        //
        $topQty = DB::table('donhang_chitiets')           // đổi thành tên bảng chi tiết đơn hàng của bạn
            ->join('donhang', 'donhang_chitiets.donhang_id', '=', 'donhang.id')
            ->join('sanpham', 'donhang_chitiets.sanpham_id', '=', 'sanpham.id')
            ->whereIn('donhang.trangthaidonhang', ['dathanhtoan','hoanthanh'])
            ->select('sanpham.ten as label', DB::raw('SUM(donhang_chitiets.soluong) as value'))
            ->groupBy('sanpham.id','sanpham.ten')
            ->orderByDesc('value')
            ->limit(5)
            ->get();

        //
        // 2) Top 5 sản phẩm theo doanh thu
        //
        $topRev = DB::table('donhang_chitiets')
            ->join('donhang', 'donhang_chitiets.donhang_id', '=', 'donhang.id')
            ->join('sanpham', 'donhang_chitiets.sanpham_id', '=', 'sanpham.id')
            ->whereIn('donhang.trangthaidonhang', ['dathanhtoan','hoanthanh'])
            ->select('sanpham.ten as label', DB::raw('SUM(donhang_chitiets.soluong * donhang_chitiets.dongia) as value'))
            ->groupBy('sanpham.id','sanpham.ten')
            ->orderByDesc('value')
            ->limit(5)
            ->get();

        // Tách label và data ra mảng riêng
        $labels      = $topQty->pluck('label');
        $qtyData     = $topQty->pluck('value');
        $revenueData = $topRev->pluck('value');

        return view('admin.dashboard', compact('labels','qtyData','revenueData'));
    }

    public function product(Request $request)
{
    $query = SanPhamKichCoMauSac::with(['product.images', 'kichCo', 'mauSac'])
     ->orderBy('sanpham_id')
     ->orderBy('id');

    if ($request->filled('search')) {
        $query->whereHas('product', function ($q) use ($request) {
            $q->where('ten', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('size')) {
        $query->where('kichco_id', $request->size);
    }

    if ($request->filled('color')) {
        $query->where('mausac_id', $request->color);
    }

    $variants = $query->paginate(10);

    if ($request->ajax()) {
        return view('admin.product._table', compact('variants'))->render();
    }

    return view('admin.product.index', compact('variants'));    
}

    public function productEdit($id)
    {
        $item = SanPham::findOrFail($id);
        // Lấy danh sách kích cỡ và màu sắc
        $kichcoList = KichCo::all();
        $mausacList = MauSac::all();
        $tongSoLuong = $item->variants->sum('sl');
        return view('admin.product.edit', compact('item', 'kichcoList', 'mausacList', 'tongSoLuong'));
    }
    public function productUpdate(Request $request, $id)
    {
        $product = SanPham::findOrFail($id);

        $product->ten = $request->ten;
        $product->mo_ta = $request->mo_ta;
        $product->gia_ban = $request->gia_ban;
        $product->thoi_gian_them = $request->thoi_gian_them;
        $product->trang_thai = $request->trang_thai;

        if ($request->hasFile('hinh_anh')) {
            $filename = time() . '.' . $request->hinh_anh->extension();
            $request->hinh_anh->move(public_path('images'), $filename);
            $product->hinh_anh = $filename;
        }

        $product->save();

        return redirect()->route('admin.product.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }
    // Hiển thị form chỉ sửa biến thể
public function variantEdit($id)
    {
        $v = SanPhamKichCoMauSac::with(['product'])
            ->findOrFail($id);

        $last = $v->product
              ->nhapKho()
              ->latest('ngaynhap')
              ->first();

        $giaNhapCu = $last->gianhap ?? null;

        // Danh sách loại để select trong form
        $categories = Loai::where('status', 1)
                      ->orderBy('loai')
                      ->get();

        return view('admin.product.variant-edit', compact('v','categories','giaNhapCu'));
    }

    public function variantUpdate(Request $request, $id)
    {
        $data = $request->validate([
            // variant fields
            'sl'        => 'required|integer|min:0',
            'kichco_id' => 'required|exists:kichco,id',
            'mausac_id' => 'required|exists:mausac,id',
            'hinh_anh'  => 'nullable|image|max:2048',
            'trang_thai'=> 'required|in:0,1',
            // product‐level fields
            'gia_nhap'  => 'nullable|numeric|min:0',
            'gia_ban'   => 'nullable|numeric|min:0',
            'gia_buon'  => 'nullable|numeric|min:0',
            'bo_mon'    => 'nullable|string|max:100',
            'loai_id'   => 'required|exists:loai,id',   // <-- mới
        ]);

        $variant = SanPhamKichCoMauSac::with('product')->findOrFail($id);
        $prod    = $variant->product;
        $prod->loais()->sync([$data['loai_id']]);

        // 1) Cập nhật bảng sanpham (tất cả variants dùng chung giá này)
        $prod = $variant->product;
        $prod->update([
            'gia_nhap' => $data['gia_nhap']  ?? $prod->gia_nhap,
            'gia_ban'  => $data['gia_ban']   ?? $prod->gia_ban,
            'gia_buon' => $data['gia_buon']  ?? $prod->gia_buon,
            'bo_mon'   => $data['bo_mon']    ?? $prod->bo_mon,
        ]);

        // 2) Cập nhật chính biến thể
        $variant->update([
            'sl'        => $data['sl'],
            'kichco_id' => $data['kichco_id'],
            'mausac_id' => $data['mausac_id'],
            'trang_thai'=> $data['trang_thai'],
        ]);

        // 3) Nếu đổi ảnh variant
        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('variants','public');
            $variant->hinh_anh = $path;
            $variant->save();
        }
        $userId = auth()->id() ?: \App\Models\NguoiDung::first()->id;
        if ($request->filled('gia_nhap')) {
        $variant->product->nhapKho()->create([
            'nguoidung_id'  => $userId, //auth()->id(),
            'soluongnhap'   => 0,             // hoặc lấy từ 1 ô qty nếu bạn muốn
            'gianhap'       => $request->gia_nhap,
            'ngaynhap'      => now(),
        ]);
    }

        return redirect()
            ->route('admin.product.index')
            ->with('success','Cập nhật biến thể thành công');
    }

// (Nếu cần) xóa biến thể
public function variantDestroy($id)
{
  SanPhamKichCoMauSac::destroy($id);
  return back()->with('success','Đã xóa biến thể');
}
public function productCreate()
    {
        return view('admin.product.create', [
            
      'kichCoList' => \App\Models\KichCo::all(),
      'mausacList' => \App\Models\MauSac::all(),
      'dsLoai'     => \App\Models\Loai::pluck('loai'),
      'existingNames'=> \App\Models\SanPham::pluck('ten'),
    ]);
    }
    public function getImportPrice(Request $request)
{
    $name = $request->query('name');
    // tìm record sản phẩm theo tên chính xác hoặc bắt đầu bằng...
    $product = SanPham::where('ten', 'like', "{$name}%")->first();

    if (! $product) {
        return response()->json(['importPrice' => null, 'sellPrice' => null]);
    }

    // lấy giá nhập mới nhất từ bảng nhapkho
    $lastNhap = NhapKho::where('sanpham_id', $product->id)
                       ->latest('id')
                       ->first();

    return response()->json([
        'importPrice' => $lastNhap?->gianhap,
        'sellPrice'   => $product->gia_ban,
        'wholesalePrice' => $product->gia_buon,
    ]);
}
    public function productStore(Request $request)
{
   $data = $request->validate([
            // product‐level
            'ten'         => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'price'       => 'required|numeric|min:0',
            'gia_buon'    => 'nullable|numeric|min:0',
            // nhập kho + variant
            'gia_nhap'    => 'required|numeric|min:0',
            'import_date' => 'required|date',
            'qty'         => 'required|integer|min:1',
            // variant
            'size'        => 'required|string|max:10',
            'color'       => 'required|string|max:50',
            'trang_thai'  => 'required|in:0,1',
            'images.*'    => 'nullable|image|max:2048',
        ]);

        // 0) Map category -> prefix
        $prefixMap = [
            'Bóng đá'=>'BD', 'Bóng rổ'=>'BR', 'Cầu lông'=>'CL',
            'Váy cầu lông'=>'VCL', 'Áo'=>'AO', 'Quần'=>'QU', 'Phụ kiện'=>'PK'
        ];
        $pre = $prefixMap[$data['category']] ?? 'SP';

        // 1) Lấy hoặc tạo mới sản phẩm theo tên
        $product = SanPham::firstOrNew(['ten' => $data['ten']]);

        if (!$product->exists) {
            // a) sinh mã mới
            $last = SanPham::where('masanpham','like',$pre.'%')
                           ->orderByDesc('id')->first();
            $num  = $last
                  ? intval(preg_replace('/\D/','',$last->masanpham)) + 1
                  : 1;
            $product->masanpham     = $pre . str_pad($num, 5, '0', STR_PAD_LEFT);
            // b) gán các field
            $product->loai           = $data['category'];
            $product->gia_ban        = $data['price'];
            $product->gia_buon       = $data['gia_buon'] ?? 0;
            $product->thoi_gian_them = now();
            $product->trang_thai     = 1;
            $product->save();
        } else {
            // Nếu muốn cập nhật lại giá bán / giá buôn khi thêm variant
            $product->update([
                'gia_ban'  => $data['price'],
                'gia_buon' => $data['gia_buon'] ?? $product->gia_buon,
                'loai'     => $data['category'],
            ]);
        }

        // 2) Lưu ảnh nếu có
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('public/images');
                $product->images()->create([
                    'image_path' => str_replace('public/','storage/',$path),
                ]);
            }
        }

        // 3) FindOrCreate size & color
        $kc = KichCo::firstOrCreate(
            ['size' => $data['size']],
            ['loai_size' => null] // hoặc giá trị mặc định
        );
        $ms = MauSac::firstOrCreate(
            ['mausac' => $data['color']]
        );

        // 4) Tạo hoặc cập nhật variant (cộng dồn số lượng nếu trùng)
        SanPhamKichCoMauSac::updateOrCreate(
            [
                'sanpham_id' => $product->id,
                'kichco_id'  => $kc->id,
                'mausac_id'  => $ms->id,
            ],
            [
                'sl'         => DB::raw("IFNULL(sl,0) + {$data['qty']}"),
                'trang_thai' => $data['trang_thai'],
            ]
        );

        // 5) Ghi nhận nhập kho
        $userId = auth()->id() ?: \App\Models\NguoiDung::first()->id;
        $product->nhapKho()->create([
            'nguoidung_id'  => $userId,
            'soluongnhap'   => $data['qty'],
            'gianhap'       => $data['gia_nhap'],
            'ngaynhap'      => $data['import_date'],
        ]);

        return redirect()
            ->route('admin.product.index')
            ->with('success','Thêm sản phẩm/variant/nhập kho thành công');
}
    public function productDestroy($id)
    {
        $product = SanPham::with('images')->findOrFail($id);


        // Xóa ảnh liên quan nếu có
        foreach ($product->images as $image) {
            if (\Storage::exists(str_replace('storage/', 'public/', $image->image_path))) {
                \Storage::delete(str_replace('storage/', 'public/', $image->image_path));
            }
            $image->delete();
        }

        $product->delete();

        return redirect()->route('admin.product.index')->with('success', 'Đã xóa sản phẩm');
    }

    public function users(Request $request)
    {
        $query = NguoiDung::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%{$q}%")
                    ->orWhere('ten_nguoi_dung', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function usersEdit($id)
    {
        $user = NguoiDung::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function usersUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'ten_nguoi_dung' => 'required|string|max:255',
            'email' => 'required|email|max:150',
            'sdt' => 'nullable|string|max:20',
            'dia_chi' => 'nullable|string|max:255',
            'vai_tro' => 'required|in:admin,customer',
            'mat_khau' => 'nullable|string|min:4',
        ]);

        $user = NguoiDung::findOrFail($id);
        $user->ten_nguoi_dung = $data['ten_nguoi_dung'];
        $user->email = $data['email'];
        $user->sdt = $data['sdt'] ?? '';
        $user->dia_chi = $data['dia_chi'] ?? '';
        $user->vai_tro = $data['vai_tro'];

        if (!empty($data['mat_khau'])) {
            // 1) Lưu vào mat_khau (hash)
            $user->mat_khau = Hash::make($data['mat_khau']);

            // 2) Lưu bản mã hóa đảo ngược để hiển thị được
            $user->password_enc = Crypt::encryptString($data['mat_khau']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Cập nhật người dùng thành công');
    }
    public function categoryIndex(Request $request)
    {
        $query = Loai::query();

        if ($request->filled('search')) {
            $query->where('loai', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(10);
        return view('admin.categories.index', [
            'categories' => $categories,
            'search' => $request->search ?? '',
        ]);
    }

    public function vouchersCreate()
    {
        $sanphams = SanPham::all();
        return view('admin.vouchers.create', compact('sanphams'));
    }

    public function vouchersStore(Request $request)
    {
        $data = $request->validate([
            'loai' => 'required|in:fixed,percent',
            'soluong_value' => 'required|numeric|min:0',
            'soluong' => 'required|integer|min:1',
            'ngay_bat_dau' => 'required|date_format:Y-m-d\TH:i',
            'ngay_ket_thuc' => 'required|date|after_or_equal:ngay_bat_dau',
            'sanphams' => 'required|array',
            'sanphams.' => 'exists:sanpham,id',
        ]);

        // 1. Lấy phần số lớn nhất hiện có, convert thành int
        $row = DB::table('vouchers')
            ->select(DB::raw('MAX(CAST(SUBSTRING(ma_voucher,3) AS UNSIGNED)) as max_num'))
            ->first();

        $next = (int) ($row->max_num ?? 0) + 1;

        // 2. Zero-pad về 4 chữ số, prefix 'VC'
        $code = 'VC' . str_pad($next, 4, '0', STR_PAD_LEFT);

        // Chuẩn bị dữ liệu
        $voucher = Voucher::create([
            'ma_voucher' => $code,
            'loai' => $data['loai'],
            'soluong' => $data['soluong_value'],
            'quantity' => $data['soluong'],       // nếu bạn dùng cột tên khác
            'ngay_bat_dau' => $data['ngay_bat_dau'],
            'ngay_ket_thuc' => $data['ngay_ket_thuc'],
            // created_at sẽ tự gán bởi Eloquent

        ]);

        // Gắn quan hệ sản phẩm
        $voucher->sanphams()->sync($request->input('sanphams'));

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', "Voucher {$code} đã được tạo thành công.");
    }

    public function categoryCreate()
    {
        return view('admin.categories.create');
    }

    public function categoryStore(Request $request)
    {
        $data = $request->validate([
            'loai' => 'required|string|max:255|unique:loai,loai',
            'status' => 'required|in:1,0',
            'created_at' => 'required|date',
        ], [
            'loai.unique' => 'Tên danh mục đã tồn tại',
        ]);

        Loai::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Thêm danh mục thành công!');
    }

    public function categoryEdit($id)
    {
        $category = Loai::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function categoryUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'loai' => "required|string|max:255|unique:loai,loai,{$id}",
            'status' => 'required|in:0,1',
            'created_at' => 'required|date',
        ]);

        Loai::where('id', $id)->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục thành công!');
    }

    public function categoryDestroy($id)
    {
        $category = Loai::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');
    }


    public function ordersIndex(Request $request)
    {
        $query = DonHang::with(['user', 'sanPham']);
        $query->where('trangthai', '>', 1);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where('madon', 'like', "%{$q}%")
                ->orWhereHas(
                    'user',
                    fn($qb) =>
                    $qb->where('ten_nguoi_dung', 'like', "%{$q}%")
                );
        }


        $orders = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->only('search'));

        // Thêm `madon` vào mảng truyền qua view
        $orders->getCollection()->transform(function (DonHang $o) {
            return [
                'id' => $o->id,
                'madon' => $o->madon,              // <-- đây
                'created_at' => $o->created_at,
                'customer' => $o->user->ten_nguoi_dung ?? '',
                'trangthai' => $o->trangthai,
                'order_status' => $o->trangthaidonhang,
                'shipping_method' => $o->shipping_method,
                'shipping_method_label' => $o->shipping_method_label,
                'delivery_status' => $o->delivery_status,
                'total_amount' => $o->tongtien,
            ];
        });
        $orders = DonHang::query()
    ->select('donhang.*')
    // join hoặc withSum tuỳ bạn đặt tên quan hệ items()
    ->withSum(['items as tongtien' => function($q) {
        $q->select(DB::raw('SUM(soluong * dongia)'));
    }], 'soluong')
    ->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }
    public function ordersShow($id)
{
    $o = DonHang::with(['user', 'items.product.colorImages'])
               ->findOrFail($id);

    // build items array
    $items = $o->items->map(function($item) {
        $img = optional($item->product->colorImages->first())->image_path;
        return [
            'image_url' => $img 
                ? asset('storage/' . $img) 
                : 'https://via.placeholder.com/50',
            'name'     => $item->product->ten,
            'sku'      => $item->product->masanpham,
            'quantity' => $item->soluong,
            'price'    => $item->dongia,
            'subtotal' => $item->dongia * $item->soluong,
        ];
    })->toArray();

    // tính các tổng
    $sumItems   = collect($items)->sum('subtotal');           // tổng tiền hàng
    $discount   = $o->discount      ?? 0;                     // giảm giá
    $shipping   = $o->shipping_fee  ?? 0;                     // phí vận chuyển
    $totalOrder = $sumItems - $discount + $shipping;         // tổng giá trị đơn
    $paid       = $o->paid_amount   ?? 0;                     // đã thanh toán
    $refunded   = $o->refunded_amount ?? 0;                   // đã hoàn trả
    $received   = $o->received_amount ?? 0;                   // thực nhận

    // build array đưa xuống view
    $order = [
        'id'                  => $o->madon,
        'created_at'          => $o->ngaydat,
        'delivery_status'  => $o->delivery_status,
        'trangthai'           => $o->trangthai,
        'shipping_method_label'     => $o->shipping_method_label,
        'notes'               => $o->notes ?? '',
        'items'               => $items,
        // những giá trị tính ra
        'sum_items'           => $sumItems,
        'discount'            => $discount,
        'shipping_fee'        => $shipping,
        'total_order_value'   => $totalOrder,
        'paid_amount'         => $paid,
        'refunded_amount'     => $refunded,
        'received_amount'     => $received,
        'customer_name'       => $o->user->ten_nguoi_dung ?? '',
    ];

    return view('admin.orders.show', compact('order'));
}
    public function ordersUpdateNotes(Request $request, $madon)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        // LẤY ĐÚNG 1 MODEL (chứ không phải get() => Collection)
        $order = DonHang::where('madon', $madon)->firstOrFail();

        // Gán và lưu
        $order->notes = $request->input('notes');
        $order->save();

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Ghi chú đã được cập nhật!');
    }


    public function inventoryIndex(Request $request)
{
    $perPage = $request->get('perPage', 10);

    $query = SanPham::withSum(['activeVariants as tong_ton'], 'sl')
                     ->orderBy('ten');

    if ($request->filled('search')) {
        $q = $request->search;
        $query->where(function ($sub) use ($q) {
            $sub->where('ten', 'like', "%{$q}%")
                ->orWhere('masanpham', 'like', "%{$q}%");
        });
    }

    if ($request->filled('type')) {
        $query->whereHas('loais', function ($sub) use ($request) {
            $sub->where('loai', $request->type);
        });
    }

    $products = $query
        ->paginate($perPage)
        ->withQueryString();

    $dsLoai = \App\Models\Loai::pluck('loai');

    return view('admin.inventory.index', compact(
        'products','dsLoai','perPage'
    ) + [
        'search' => $request->search,
        'type'   => $request->type,
    ]);
}
public function lastImportPrice($productId)
{
    $nhap = \App\Models\NhapKho::where('sanpham_id', $productId)
                               ->orderByDesc('ngaynhap')
                               ->first();

    return response()->json([
        'gianhap' => $nhap->gianhap ?? null,
    ]);
}
    public function feedbackIndex(Request $request)
    {
        // 1) Build query với eager-load quan hệ
        $query = Feedback::with(['product', 'customer']);

        // 2) Server-side filter nếu có
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('id', 'like', "%{$q}%")
                    ->orWhereHas('product', fn($q2) => $q2->where('masanpham', 'like', "%{$q}%"))
                    ->orWhereHas('customer', fn($q3) => $q3->where('name', 'like', "%{$q}%"));
            });
        }
        if ($request->filled('status')) {
            // status bạn lưu ở cột `trang_thai`
            $query->where('trang_thai', $request->status);
        }

        // 3) Phân trang
        $paginator = $query
            ->orderBy('ngaydanhgia', 'desc')
            ->paginate(10)
            ->appends($request->only('search', 'status'));

        // 4) Chuyển từng item thành mảng đúng key view cần
        $paginator->getCollection()->transform(function (Feedback $f) {
            return [
                'id' => $f->id,
                'product' => $f->product->masanpham ?? '',
                'customer' => $f->customer->ten_nguoi_dung ?? '',
                'rating' => $f->sosao,
                'comment' => $f->noi_dung,
                'reply' => $f->reply,
                'created_at' => $f->ngaydanhgia,
                'is_replied' => (bool) $f->is_replied,
            ];
        });

        // 5) Đưa về view
        return view('admin.feedback.index', [
            'feedbacks' => $paginator,
            // nếu view có dùng $search, $status thì truyền thêm
            'search' => $request->search,
            'status' => $request->status,
        ]);
    }
    public function feedbackReply(Request $request, $id)
    {
        $data = $request->validate([
            'reply' => 'required|string',
        ], [
            'reply.required' => 'Bạn chưa nhập nội dung trả lời.'
        ]);

        $fb = Feedback::findOrFail($id);
        $isUpdate = !empty($fb->reply);
        $fb->reply = $data['reply'];
        $fb->is_replied = 1;
        $fb->save();

        return response()->json([
            'success' => true,
            'reply' => $fb->reply,
            'is_replied' => $fb->is_replied,
            'updated' => $isUpdate, // true nếu là sửa lại phản hồi
        ]);
    }
    public function feedbackDestroy($id)
    {
        $fb = Feedback::findOrFail($id);
        $fb->delete();
        return redirect()->back()->with('success', 'Xóa feedback thành công!');
    }

    public function vouchersIndex(Request $request)
    {
        $query = Voucher::query()->with('sanphams');

        if ($request->filled('search')) {
            $query->where('ma_voucher', 'like', '%' . $request->search . '%');
        }

        $vouchers = $query
            ->with('sanphams')      // eager-load pivot
            ->orderBy('ma_voucher', 'asc')
            ->paginate(10);

        return view('admin.vouchers.index', [
            'vouchers' => $vouchers,
            'search' => $request->search,
            'status' => $request->status
        ]);
    }

    public function vouchersEdit($id)
    {
        $voucher = Voucher::with('sanphams')->findOrFail($id);
        $sanphams = SanPham::all();
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher', 'sanphams'));
    }

    public function vouchersUpdate(Request $request, Voucher $voucher)
    {
        $data = $request->validate([
            'loai' => 'required|in:fixed,percent',
            'soluong_value' => 'required|numeric|min:0',
            'soluong' => 'required|integer|min:1',
            'ngay_bat_dau' => 'required|date_format:Y-m-d\TH:i',
            'ngay_ket_thuc' => 'required|date_format:Y-m-d\TH:i|after_or_equal:ngay_bat_dau',
            'sanphams' => 'required|array',
            'sanphams.*' => 'exists:sanpham,id',
        ]);

        // Cập nhật các trường cơ bản trên đối tượng $voucher được inject
        $voucher->update([
            'loai' => $data['loai'],
            'soluong' => $data['soluong_value'],
            'quantity' => $data['soluong'],
            'ngay_bat_dau' => $data['ngay_bat_dau'],
            'ngay_ket_thuc' => $data['ngay_ket_thuc'],
        ]);

        // Sync pivot sản phẩm
        $voucher->sanphams()->sync($data['sanphams']);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Cập nhật voucher thành công.');
    }
    public function vouchersDestroy(Request $request, Voucher $voucher)
    {
        // Xoá quan hệ pivot nếu cần
        $voucher->sanphams()->detach();

        // Xoá voucher
        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Xoá voucher thành công.');
    }

    public function reportRevenue(Request $request)
    {
        // 1) Lấy query cơ bản: chỉ những đơn "đã thanh toán" hoặc "hoàn thành"
        $query = DonHang::query()
            ->whereIn('trangthaidonhang', ['dathanhtoan', 'hoanthanh']);

        // 2) Lọc theo ngày đặt
        if ($request->filled('start_date')) {
            $query->whereDate('ngaydat', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('ngaydat', '<=', $request->end_date);
        }

        // 3) Lọc theo sản phẩm (nếu chọn != all)
        $productId = $request->get('product_id', 'all');
        if ($productId !== 'all') {
            $query->whereHas('items', fn($q) =>
                $q->where('sanpham_id', $productId)
            );
        }

        // 4) Xác định period: day/month/year
        $period = $request->get('period', 'day');
        switch ($period) {
            case 'month':
                $dateExpr = "DATE_FORMAT(ngaydat, '%Y-%m')";
                break;
            case 'year':
                $dateExpr = "YEAR(ngaydat)";
                break;
            default:
                $dateExpr = "DATE(ngaydat)";
        }

        // 5) Gom nhóm để lấy data Tổng quan
        $overview = $query->clone()
            ->selectRaw("$dateExpr as label")
            ->selectRaw('COUNT(*) as so_don')
            ->selectRaw('SUM(tongtien) as tong_doanhthu')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        // 6) Lấy danh sách sản phẩm cho dropdown
        $products = SanPham::orderBy('ten')->get(['id','ten']);

        // 7) Trả về view
        return view('admin.report.revenue', compact(
            'overview',
            'products',
            'period',
            'productId'
        ));
    }

    public function exportRevenue(Request $request)
{
    $fileName = 'revenue_'.now()->format('Ymd_His').'.xlsx';
    return Excel::download(
        new RevenueExport($request->all()),
        $fileName
    );
}

    public function printRevenue(Request $request)
    {
        $data = $this->getRevenueData($request);
        return view('admin.report.revenue-print', ['reportData' => $data]);
    }

    private function getRevenueData(Request $request): array
    {
        return [
            ['Date' => '2025-05-01', 'Product' => 'A', 'Quantity' => 5, 'Total' => 500],
            ['Date' => '2025-05-02', 'Product' => 'B', 'Quantity' => 3, 'Total' => 300],
        ];
    }
    public function usersCreate()
    {
        return view('admin.users.create');
    }
    public function usersStore(Request $request)
    {
        $request->validate([
            'ten_nguoi_dung' => 'required|unique:nguoidung,ten_nguoi_dung',
            'email' => 'required|email|unique:nguoidung,email',
            'sdt' => 'nullable|string',
            'dia_chi' => 'nullable|string',
            'mat_khau' => 'required|string|min:3',
            'vai_tro' => 'required|in:admin,customer',
        ], [
            'ten_nguoi_dung.unique' => 'Tên người dùng đã tồn tại, hãy nhập tên khác.',
            'email.unique' => 'Email đã được sử dụng, hãy chọn email khác.',
        ]);

        \App\Models\NguoiDung::create([
            'ten_nguoi_dung' => $request->ten_nguoi_dung,
            'email' => $request->email,
            'sdt' => $request->sdt ?? '',
            'dia_chi' => $request->dia_chi,
            'mat_khau' => $request->mat_khau,
            'vai_tro' => $request->vai_tro,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công!');
    }
    public function usersDestroy($id)
    {
        // Xoá người dùng theo ID
        \App\Models\NguoiDung::destroy($id);

        return redirect()->route('admin.users.index')
            ->with('success', 'Xóa người dùng thành công');
    }
    public function membersIndex(Request $request)
    {
        $query = Member::query();

        // filter trạng thái
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // filter bậc
        if ($request->filled('tier')) {
            $query->where('membership_tier', $request->tier);
        }

        $members = $query
            ->orderBy('id')
            ->paginate(10)
            ->appends($request->only('status', 'tier'));

        return view('admin.members.index', compact('members', 'request'));
    }

    // SHOW form tạo
    public function membersCreate()
    {
        return view('admin.members.create');
    }

    // STORE
    public function membersStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'nullable|string|max:20',
            'membership_tier' => 'required|in:Silver,Gold,Platinum',
            'is_active' => 'sometimes|boolean',
        ]);

        Member::create($data);

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Thêm thành viên thành công');
    }

    // SHOW detail
    public function membersShow($id)
    {
        $member = Member::findOrFail($id);
        return view('admin.members.show', compact('member'));
    }

    // SHOW form edit
    public function membersEdit($id)
    {
        $member = Member::findOrFail($id);
        return view('admin.members.edit', compact('member'));
    }

    // UPDATE
    public function membersUpdate(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        // 1) Quy định validation và thông điệp tuỳ chỉnh
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                // rule unique, bỏ qua record hiện tại
                Rule::unique('members', 'email')->ignore($id),
            ],
            'phone' => 'nullable|string|max:20',
            'membership_tier' => 'required|in:Silver,Gold,Platinum',
            'is_active' => 'sometimes|boolean',
        ];

        $messages = [
            'email.unique' => 'Email này đã được đăng ký cho thành viên khác, hãy nhập một email mới.',
        ];

        // 2) Validate với messages tuỳ chỉnh
        $data = $request->validate($rules, $messages);

        // 3) Cập nhật
        $member->update($data);

        // 4) Chuyển về index với flash popup
        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Cập nhật thành viên thành công');
    }

    // DELETE
    public function membersDestroy($id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Xóa thành viên thành công');
    }
}
