<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\RevenueExport;
use Illuminate\Support\Facades\DB;
use App\Models\SanPham;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use App\Models\Loai;
use App\Models\DonHang;
use App\Models\Voucher;
use App\Models\Comment;
use App\Models\NhapKho;
use App\Models\ImgSanPham;
use App\Models\KichCo;
use App\Models\MauSac;
use App\Models\Feedback;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function product()
{
    $items = SanPham::with(['variants.kichCo', 'variants.mauSac', 'images'])->get();

    foreach ($items as $item) {
        $item->so_luong = $item->variants->sum('sl');  // tổng số lượng
    }

    return view('admin.product.index', compact('items'));
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

    public function users(Request $request)
    {
        $query = NguoiDung::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%$q%")
                    ->orWhere('ten_nguoi_dung', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }

        $users = $query->paginate(10);
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
            'mat_khau' => 'nullable|string|min:4', // <- Cho phép bỏ trống nếu không đổi
        ]);

        $user = NguoiDung::findOrFail($id);

        // Gán các trường cập nhật
        $user->ten_nguoi_dung = $data['ten_nguoi_dung'];
        $user->email = $data['email'];
        $user->sdt = $data['sdt'] ?? '';
        $user->dia_chi = $data['dia_chi'] ?? '';
        $user->vai_tro = $data['vai_tro'];

        // Nếu người dùng nhập mật khẩu mới
        if (!empty($data['mat_khau'])) {
            $user->mat_khau = Hash::make($data['mat_khau']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công');
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
        'loai'           => 'required|in:fixed,percent',
        'soluong_value'  => 'required|numeric|min:0',
        'soluong'        => 'required|integer|min:1',
        'ngay_bat_dau'   => 'required|date_format:Y-m-d\TH:i',
        'ngay_ket_thuc'  => 'required|date|after_or_equal:ngay_bat_dau',
        'sanphams'       => 'required|array',
        'sanphams.'      => 'exists:sanpham,id',
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
        'ma_voucher'    => $code,
        'loai'          => $data['loai'],
        'soluong'       => $data['soluong_value'],
        'quantity'      => $data['soluong'],       // nếu bạn dùng cột tên khác
        'ngay_bat_dau'  => $data['ngay_bat_dau'],
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
        'loai'       => 'required|string|max:255|unique:loai,loai',
        'status'     => 'required|in:1,0',
        'created_at' => 'required|date',
    ], [
        'loai.unique' => 'Tên danh mục đã tồn tại',
    ]);

    Loai::create($data);

    return redirect()
      ->route('admin.categories.index')
      ->with('success','Thêm danh mục thành công!');
}

public function categoryEdit($id)
{
    $category = Loai::findOrFail($id);
    return view('admin.categories.edit', compact('category'));
}

public function categoryUpdate(Request $request, $id)
{
    $data = $request->validate([
        'loai'       => "required|string|max:255|unique:loai,loai,{$id}",
        'status'     => 'required|in:0,1',
        'created_at' => 'required|date',
    ]);

    Loai::where('id', $id)->update($data);

    return redirect()
        ->route('admin.categories.index')
        ->with('success','Cập nhật danh mục thành công!');
}

public function categoryDestroy($id)
{
    $category = Loai::findOrFail($id);
    $category->delete();
    return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công!');
}


    public function ordersIndex(Request $request)
{
    $query = DonHang::with(['user','sanPham']);
    $query->where('trangthai','>', 1);

    if ($request->filled('search')) {
        $q = $request->search;
        $query->where('madon','like',"%{$q}%")
              ->orWhereHas('user', fn($qb) => 
                  $qb->where('ten_nguoi_dung','like',"%{$q}%")
              );
    }

    $orders = $query
        ->orderBy('created_at','desc')
        ->paginate(15)
        ->appends($request->only('search'));

    // Thêm `madon` vào mảng truyền qua view
    $orders->getCollection()->transform(function(DonHang $o) {
        return [
            'id'               => $o->id,
            'madon'            => $o->madon,              // <-- đây
            'created_at'       => $o->created_at,
            'customer'         => $o->user->ten_nguoi_dung ?? '',
            'trangthai' => $o->trangthai,
            'order_status'     => $o->trangthaidonhang,
            'shipping_method'  => $o->shipping_method,
            'shipping_method_label'=> $o->shipping_method_label,
            'delivery_status'  => $o->delivery_status,
            'total_amount'     => $o->tongtien,
        ];
    });

    return view('admin.orders.index', compact('orders'));
}
public function ordersShow($id)
{
    $o = DonHang::with([
            'user',
            'items.product.colorImages'  // load luôn ảnh
        ])
        ->findOrFail($id);

    // Build mảng truyền xuống view
    $order = [
      'id'              => $o->madon,         // giờ dùng mã đơn
      'created_at'      => $o->ngaydat,
      'delivery_status' => $o->trang_thaigia ?? 'pending',
      'trangthai' => $o->trangthai,
      'shipping_method_label'=> $o->shipping_method_label,
      'notes'           => $o->notes ?? '',
      'discount'        => $o->discount ?? 0,
      'shipping_fee'    => $o->shipping_fee ?? 0,
      'total_amount'    => $o->tongtien,
      'paid_amount'     => $o->paid_amount ?? 0,
      'refunded_amount' => $o->refunded_amount ?? 0,
      'received_amount' => $o->received_amount ?? 0,
      'customer'        => $o->user->ten_nguoi_dung ?? '',
      'items' => $o->items->map(function($item) {
          // tìm ảnh đầu tiên (is_main) hoặc collection đầu tiên
          $img = optional($item->product->colorImages->first())->image_path;
          return [
            'image_url' => $img
              ? asset('storage/'.$img)
              : 'https://via.placeholder.com/50',
            'name'     => $item->product->ten,
            'sku'      => $item->product->masanpham,
            'quantity' => $item->soluong,
            'price'    => $item->dongia,
          ];
      })->toArray(),
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
        $query = NhapKho::with('sanPham.loais');

        // Tìm kiếm theo tên/mã sản phẩm
        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('sanPham', function ($q2) use ($q) {
                $q2->where('ten', 'like', "%$q%")
                    ->orWhere('masanpham', 'like', "%$q%");
            });
        }

        // Lọc theo loại sản phẩm từ bảng loai
        if ($request->filled('type')) {
            $query->whereHas('sanPham.loai', function ($q2) use ($request) {
                $q2->where('loai', $request->type);
            });
        }

        // Lọc ngày
        if ($request->filled('from_date')) {
            $query->whereDate('ngaynhap', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('ngaynhap', '<=', $request->to_date);
        }

        // Phân trang
        $perPage = $request->get('perPage', 10);
        $items = $query->paginate($perPage)->withQueryString();

        // Lấy danh sách loại sản phẩm để lọc (nếu cần)
        $dsLoai = Loai::pluck('loai');

        return view('admin.inventory.index', [
            'items' => $items,
            'search' => $request->search,
            'type' => $request->type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'perPage' => $perPage,
            'dsLoai' => $dsLoai,
        ]);
    }

    public function feedbackIndex(Request $request)
    {
        // 1) Build query với eager-load quan hệ
        $query = Feedback::with(['product','customer']);

        // 2) Server-side filter nếu có
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function($qb) use ($q) {
                $qb->where('id', 'like', "%{$q}%")
                   ->orWhereHas('product', fn($q2)=> $q2->where('masanpham','like',"%{$q}%"))
                   ->orWhereHas('customer', fn($q3)=> $q3->where('name','like',"%{$q}%"));
            });
        }
        if ($request->filled('status')) {
            // status bạn lưu ở cột `trang_thai`
            $query->where('trang_thai', $request->status);
        }

        // 3) Phân trang
        $paginator = $query
            ->orderBy('ngaydanhgia','desc')
            ->paginate(10)
            ->appends($request->only('search','status'));

        // 4) Chuyển từng item thành mảng đúng key view cần
        $paginator->getCollection()->transform(function(Feedback $f) {
            return [
                'id'         => $f->id,
                'product'    => $f->product->masanpham   ?? '',
                'customer'   => $f->customer->ten_nguoi_dung        ?? '',
                'rating'     => $f->sosao,
                'comment'    => $f->noi_dung,
                'reply'      => $f->reply,
                'created_at' => $f->ngaydanhgia,
                'is_replied' => (bool)$f->is_replied,
            ];
        });

        // 5) Đưa về view
        return view('admin.feedback.index', [
            'feedbacks' => $paginator,
            // nếu view có dùng $search, $status thì truyền thêm
            'search'    => $request->search,
            'status'    => $request->status,
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
        'success'    => true,
        'reply'      => $fb->reply,
        'is_replied' => $fb->is_replied,
        'updated'    => $isUpdate, // true nếu là sửa lại phản hồi
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
    $voucher  = Voucher::with('sanphams')->findOrFail($id);
    $sanphams = SanPham::all();
    $voucher = Voucher::findOrFail($id);
    return view('admin.vouchers.edit', compact('voucher', 'sanphams'));
}

 public function vouchersUpdate(Request $request, Voucher $voucher)
    {
        $data = $request->validate([
            'loai'           => 'required|in:fixed,percent',
            'soluong_value'  => 'required|numeric|min:0',
            'soluong'        => 'required|integer|min:1',
            'ngay_bat_dau'   => 'required|date_format:Y-m-d\TH:i',
            'ngay_ket_thuc'  => 'required|date_format:Y-m-d\TH:i|after_or_equal:ngay_bat_dau',
            'sanphams'       => 'required|array',
            'sanphams.*'     => 'exists:sanpham,id',
        ]);

        // Cập nhật các trường cơ bản trên đối tượng $voucher được inject
        $voucher->update([
            'loai'          => $data['loai'],
            'soluong'       => $data['soluong_value'],
            'quantity'      => $data['soluong'],
            'ngay_bat_dau'  => $data['ngay_bat_dau'],
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

    public function reportrevenue()
{
    $data = \DB::table('donhang')
        ->selectRaw('DATE(created_at) as ngay, COUNT(id) as so_don, SUM(tongtien) as tong_doanhthu')
        ->where('trangthai', 3) // ví dụ: trạng thái 3 là đã hoàn tất
        ->groupByRaw('DATE(created_at)')
        ->orderByDesc('ngay')
        ->get();

    return view('admin.report.revenue', compact('data'));
}

    public function exportRevenue(Request $request)
    {
        $data = $this->getRevenueData($request);
        $fileName = 'revenue_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new RevenueExport($data), $fileName);
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
    public function productCreate()
    {
        return view('admin.product.create');
    }
    public function productStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'gia_nhap' => 'nullable|numeric',
            'price' => 'required|numeric',
            'qty' => 'required|integer',
            'size' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
            'brand' => 'nullable|string|max:100',
            'category' => 'required|string|max:100',
            'import_date' => 'nullable|date',
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // Map loại sản phẩm sang tiền tố mã
        $prefixMap = [
            'Bóng đá' => 'BD',
            'Bóng rổ' => 'BR',
            'Cầu lông' => 'CL',
            'Váy cầu lông' => 'VCL',
            'Áo' => 'AO',
            'Quần' => 'QU',
            'Phụ kiện' => 'PK',
        ];

        $category = $data['category'];
        $prefix = $prefixMap[$category] ?? 'SP';

        // Tìm mã sản phẩm cuối cùng
        $last = SanPham::where('masanpham', 'like', $prefix . '%')
            ->orderByDesc('masanpham')
            ->first();

        $number = $last ? ((int) filter_var($last->masanpham, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;
        $masanpham = $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);

        // Tạo sản phẩm
        $sanPham = SanPham::create([
            'masanpham' => $masanpham,
            'ten' => $data['name'],
            'gia_nhap' => $data['gia_nhap'] ?? 0,
            'gia_ban' => $data['price'],
            'so_luong' => $data['qty'],
            'kich_thuoc' => $data['size'] ?? null,
            'mau_sac' => $data['color'] ?? null,
            'bo_mon' => $data['brand'] ?? null,
            'loai' => $category,
            'ngay_nhap' => $data['import_date'] ?? now(),
            'mo_ta' => $data['description'] ?? null,
            'trang_thai' => 1, // ✅ thêm dòng này
        ]);

        // Lưu nhiều ảnh nếu có
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('public/images');
                ImgSanPham::create([
                    'sanpham_id' => $sanPham->id,
                    'image_path' => str_replace('public/', 'storage/', $path),
                ]);
            }
        }

        return redirect()->route('admin.product.index')->with('success', 'Thêm sản phẩm thành công');
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

        NguoiDung::create([
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
        NguoiDung::destroy($id);

        return redirect()->route('admin.users.index')
            ->with('success', 'Xóa người dùng thành công');
    }
    
    
}
