<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Exports\RevenueExport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\SanPham;
use Illuminate\Http\RedirectResponse;
use App\Models\DonHangChiTiet;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\Loai;
use App\Models\DonHang;
use App\Models\Voucher;
use Illuminate\Support\Facades\Log;
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
    public function dashboard(Request $request)
    {
        // 1) Lấy input
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $period = $request->input('period', 'day'); // 'day'|'month'|'year'

        // (nếu dùng dd/mm/YYYY) parse về Y-m-d
        if ($start && preg_match('#\d{2}/\d{2}/\d{4}#', $start)) {
            $start = Carbon::createFromFormat('d/m/Y', $start)->toDateString();
        }
        if ($end && preg_match('#\d{2}/\d{2}/\d{4}#', $end)) {
            $end = Carbon::createFromFormat('d/m/Y', $end)->toDateString();
        }

        // 2) Top 5 SP theo số lượng (pie chart A)
        $base = DB::table('donhang_sanpham')
            ->join('donhang', 'donhang_sanpham.donhang_id', 'donhang.id')
            ->join('sanpham', 'donhang_sanpham.sanpham_id', 'sanpham.id')
            ->whereIn('donhang.trangthaidonhang', ['dathanhtoan', 'hoanthanh'])
            ->when($start, fn($q) => $q->whereDate('donhang.ngaydat', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('donhang.ngaydat', '<=', $end));

        $topQty = (clone $base)
            ->select('sanpham.ten as label', DB::raw('SUM(soluong) as value'))
            ->groupBy('sanpham.id', 'sanpham.ten')
            ->orderByDesc('value')
            ->limit(5)->get();

        $labels = $topQty->pluck('label');
        $qtyData = $topQty->pluck('value');

        // 3) Top 5 SP theo doanh thu (pie chart B)
        $topRev = (clone $base)
            ->select('sanpham.ten as label', DB::raw('SUM(soluong * dongia) as value'))
            ->groupBy('sanpham.id', 'sanpham.ten')
            ->orderByDesc('value')
            ->limit(5)->get();

        $revenueData = $topRev->pluck('value');

        // 4) Tổng số đơn hoàn thành theo period (new chart)
        switch ($period) {
            case 'month':
                $expr = "DATE_FORMAT(ngaydat,'%Y-%m')";
                break;
            case 'year':
                $expr = "YEAR(ngaydat)";
                break;
            default:
                $expr = "DATE(ngaydat)";
        }

        $ordersOverTime = DB::table('donhang')
            ->whereIn('trangthaidonhang', ['dathanhtoan', 'hoanthanh'])
            ->when($start, fn($q) => $q->whereDate('ngaydat', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('ngaydat', '<=', $end))
            ->selectRaw("$expr as label")
            ->selectRaw('COUNT(*) as value')
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $orderLabels = $ordersOverTime->pluck('label');
        $orderData = $ordersOverTime->pluck('value');

        // 5) Lượt truy cập (line chart, nếu có)
        $visitLabels = collect();
        $visitData = collect();
        if (DB::getSchemaBuilder()->hasTable('page_views')) {
            switch ($period) {
                case 'month':
                    $vpExpr = "DATE_FORMAT(visited_at,'%Y-%m')";
                    break;
                case 'year':
                    $vpExpr = "YEAR(visited_at)";
                    break;
                default:
                    $vpExpr = "DATE(visited_at)";
            }
            $pv = DB::table('page_views')
                ->selectRaw("$vpExpr as label")
                ->selectRaw('SUM(`count`) as visits')
                ->when($start, fn($q) => $q->whereDate('visited_at', '>=', $start))
                ->when($end, fn($q) => $q->whereDate('visited_at', '<=', $end))
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            $visitLabels = $pv->pluck('label');
            $visitData = $pv->pluck('visits');
        }

        // 6) Trả về view
        return view('admin.dashboard', compact(
            'labels',
            'qtyData',
            'revenueData',
            'orderLabels',
            'orderData',
            'visitLabels',
            'visitData',
            'start',
            'end',
            'period'
        ));
    }

    public function product(Request $request)
    {
        $query = SanPhamKichCoMauSac::with(['product.colorImages', 'kichCo', 'mauSac'])
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
        // 1) Validate chung
        $data = $request->validate([
            'ten' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'gia_buon' => 'nullable|numeric|min:0',
            'size' => 'required|string|max:10',
            'color' => 'required|string|max:50',
            'trang_thai' => 'required|in:0,1',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // 2) Lấy SP và cập nhật thông tin cơ bản
        $product = SanPham::findOrFail($id);
        $product->ten = $data['ten'];
        $product->gia_ban = $data['price'];
        $product->gia_buon = $data['gia_buon'] ?? $product->gia_buon;
        $product->trang_thai = $data['trang_thai'];
        $product->save();

        // 3) Tìm variant tương ứng
        $kc = KichCo::firstOrCreate(['size' => $data['size']]);
        $ms = MauSac::firstOrCreate(['mausac' => $data['color']]);
        $variant = SanPhamKichCoMauSac::firstOrFail([
            'sanpham_id' => $product->id,
            'kichco_id' => $kc->id,
            'mausac_id' => $ms->id,
        ]);

        // 4) Nếu có upload images mới: xóa cũ + lưu
        if ($request->hasFile('images')) {
            $product->colorImages()
                ->where('mausac_id', $ms->id)
                ->delete();

            foreach ($request->file('images') as $img) {
                $path = $img->store('variants', 'public');
                $product->colorImages()->create([
                    'mausac_id' => $ms->id,
                    'image_path' => $path,
                    'is_main' => 1,
                ]);
            }
        }

        // 5) Cập nhật số lượng nếu muốn (hoặc giữ nguyên)
        // $variant->sl = ...; $variant->save();

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Cập nhật biến thể sản phẩm thành công');
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

        return view('admin.product.variant-edit', compact('v', 'categories', 'giaNhapCu'));
    }

    public function variantUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'sl' => 'required|integer|min:0',
            'kichco_id' => 'required|exists:kichco,id',
            'mausac_id' => 'required|exists:mausac,id',
            'trang_thai' => 'required|in:0,1',
            'gia_nhap' => 'nullable|numeric|min:0',
            'gia_ban' => 'nullable|numeric|min:0',
            'gia_buon' => 'nullable|numeric|min:0',
            'bo_mon' => 'nullable|string|max:100',
            'loai_id' => 'required|exists:loai,id',
            'images.*' => 'nullable|image|max:2048',
        ], [
            'images.*.image' => 'Mỗi file phải là ảnh hợp lệ.',
            'images.*.max' => 'Ảnh không được vượt quá 2MB mỗi file.',
        ]);

        // 1) Lấy variant & sản phẩm
        $variant = SanPhamKichCoMauSac::with('product')->findOrFail($id);
        $product = $variant->product;

        // 2) Sync danh mục (loai)
        $product->loais()->sync([$data['loai_id']]);

        // 3) Cập nhật giá & bộ môn trên bảng sản phẩm
        $product->update([
            'gia_nhap' => $data['gia_nhap'] ?? $product->gia_nhap,
            'gia_ban' => $data['gia_ban'] ?? $product->gia_ban,
            'gia_buon' => $data['gia_buon'] ?? $product->gia_buon,
            'bo_mon' => $data['bo_mon'] ?? $product->bo_mon,
        ]);

        // 4) Cập nhật số lượng, kích cỡ, màu sắc, trạng thái của variant
        $variant->update([
            'sl' => $data['sl'],
            'kichco_id' => $data['kichco_id'],
            'mausac_id' => $data['mausac_id'],
            'trang_thai' => $data['trang_thai'],
        ]);

        // 5) Nếu upload ảnh mới, xóa ảnh cũ của biến thể đó rồi lưu ảnh mới
        if ($request->hasFile('images')) {
            $product->colorImages()
                ->where('mausac_id', $variant->mausac_id)
                ->where('kichco_id', $variant->kichco_id)
                ->each(function ($img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                });

            foreach ($request->file('images') as $file) {
                $path = $file->store('variants', 'public');
                $product->colorImages()->create([
                    'mausac_id' => $variant->mausac_id,
                    'kichco_id' => $variant->kichco_id,
                    'image_path' => $path,
                    'is_main' => 1,
                ]);
            }
        }

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Cập nhật biến thể thành công!');
    }

    // (Nếu cần) xóa biến thể
    public function variantDestroy($id)
    {
        SanPhamKichCoMauSac::destroy($id);
        return back()->with('success', 'Đã xóa biến thể');
    }
    public function productCreate()
    {
        return view('admin.product.create', [

            'kichCoList' => KichCo::all(),
            'mausacList' => MauSac::all(),
            /* 'dsLoai'     => Loai::pluck('loai'), */
            'dsLoai' => Loai::all(),
            'existingNames' => SanPham::pluck('ten'),
        ]);
    }
    public function getImportPrice(Request $request)
    {
        $name = $request->query('name');
        // tìm record sản phẩm theo tên chính xác hoặc bắt đầu bằng...
        $product = SanPham::where('ten', 'like', "{$name}%")->first();

        if (!$product) {
            return response()->json(['importPrice' => null, 'sellPrice' => null]);
        }

        // lấy giá nhập mới nhất từ bảng nhapkho
        $lastNhap = NhapKho::where('sanpham_id', $product->id)
            ->latest('id')
            ->first();

        return response()->json([
            'importPrice' => $lastNhap?->gianhap,
            'sellPrice' => $product->gia_ban,
            'wholesalePrice' => $product->gia_buon,
        ]);
    }
    public function productStore(Request $request)
    {
        $data = $request->validate([
            'ten' => 'required|string|max:255',
            'loai_id' => 'required|exists:loai,id',
            'price' => 'required|numeric|min:0',
            'gia_buon' => 'nullable|numeric|min:0',
            'gia_nhap' => 'required|numeric|min:0',
            'import_date' => 'required|date',
            'qty' => 'required|integer|min:1',
            'size' => 'required|string|max:10',
            'color' => 'required|string|max:50',
            'trang_thai' => 'required|in:0,1',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // 0) Prefix mã SP theo tên Loại
        $loai = Loai::find($data['loai_id']);
        $prefixMap = [
            'Bóng đá' => 'BD',
            'Bóng rổ' => 'BR',
            'Cầu lông' => 'CL',
            'Váy cầu lông' => 'VCL',
            'Áo' => 'AO',
            'Quần' => 'QU',
            'Phụ kiện' => 'PK',
        ];
        $pre = $prefixMap[$loai->loai] ?? 'SP';

        // 1) Lấy hoặc tạo mới sản phẩm
        $product = SanPham::firstOrNew(['ten' => $data['ten']]);

        // mỗi lần (tạo hoặc đổi tên) đều phải sinh lại slug
        $slug = Str::slug($data['ten']);

        if (!$product->exists) {
            // sinh mã mới
            $last = SanPham::where('masanpham', 'like', $pre . '%')
                ->orderByDesc('id')->first();

            $num = $last
                ? intval(preg_replace('/\D/', '', $last->masanpham)) + 1
                : 1;

            $product->masanpham = $pre . str_pad($num, 5, '0', STR_PAD_LEFT);
            $product->slug = $slug;
            $product->gia_ban = $data['price'];
            $product->gia_buon = $data['gia_buon'] ?? 0;
            $product->thoi_gian_them = now();
            $product->trang_thai = 1;
            $product->save();
        } else {
            // chỉ cập nhật giá nếu đã tồn tại
            $product->update([
                'ten' => $data['ten'],
                'slug' => $slug,
                'gia_ban' => $data['price'],
                'gia_buon' => $data['gia_buon'] ?? $product->gia_buon,
            ]);
        }

        // 2) Sync pivot loại
        $product->loais()->sync([$data['loai_id']]);

        // 3) Tìm hoặc tạo size & color
        $kc = KichCo::firstOrCreate(['size' => $data['size']]);
        $ms = MauSac::firstOrCreate(['mausac' => $data['color']]);

        // 4) Xử lý ảnh variant (nếu có):
        if ($request->hasFile('images')) {
            // a) xóa hết ảnh cũ của variant này
            $product->colorImages()
                ->where('mausac_id', $ms->id)
                ->where('kichco_id', $kc->id)
                ->each(function ($img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                });

            // b) lưu mới
            foreach ($request->file('images') as $file) {
                $path = $file->store('variants', 'public');
                $product->colorImages()->create([
                    'mausac_id' => $ms->id,
                    'kichco_id' => $kc->id,
                    'image_path' => $path,
                    'is_main' => 1,
                ]);
            }
        }

        // 5) Tạo hoặc cập nhật variant
        SanPhamKichCoMauSac::updateOrCreate(
            [
                'sanpham_id' => $product->id,
                'kichco_id' => $kc->id,
                'mausac_id' => $ms->id,
            ],
            [
                'sl' => DB::raw("IFNULL(sl,0)+{$data['qty']}"),
                'trang_thai' => $data['trang_thai'],
            ]
        );

        // 6) Ghi nhận nhập kho
        $userId = auth()->id() ?: NguoiDung::first()->id;
        $product->nhapKho()->create([
            'nguoidung_id' => $userId,
            'soluongnhap' => $data['qty'],
            'gianhap' => $data['gia_nhap'],
            'ngaynhap' => $data['import_date'],
        ]);

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Thêm sản phẩm/variant/nhập kho thành công');
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
            // Hash mật khẩu mới
            $user->mat_khau = Hash::make($data['mat_khau']);
            // (tuỳ chọn) lưu reversible-encryption chỉ để hiển thị
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

        // 0) Kiểm tra nếu category có products
        if ($category->sanphams()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Không thể xóa danh mục vì vẫn còn sản phẩm liên kết.');
        }

        // 1) Nếu không có, tiến hành xóa
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công!');
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
            ->withSum([
                'items as tongtien' => function ($q) {
                    $q->select(DB::raw('SUM(soluong * dongia)'));
                }
            ], 'soluong')
            ->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }
    public function ordersShow($id)
    {
        $o = DonHang::with(['user', 'items.product.colorImages'])
            ->findOrFail($id);

        // build items array
        $items = $o->items->map(function ($item) {
            $img = optional($item->product->colorImages->first())->image_path;
            return [
                'image_url' => $img
                    ? asset('storage/' . $img)
                    : 'https://via.placeholder.com/50',
                'name' => $item->product->ten,
                'sku' => $item->product->masanpham,
                'quantity' => $item->soluong,
                'price' => $item->dongia,
                'subtotal' => $item->dongia * $item->soluong,
            ];
        })->toArray();
        // tính các tổng
        $sumItems = collect($items)->sum('subtotal');           // tổng tiền hàng
        $discount = $o->discount ?? 0;                     // giảm giá
        $shipping = $o->shipping_fee ?? 0;                     // phí vận chuyển
        $totalOrder = $sumItems - $discount + $shipping;         // tổng giá trị đơn
        $paid = $o->paid_amount ?? 0;                     // đã thanh toán
        $refunded = $o->refunded_amount ?? 0;                   // đã hoàn trả
        $received = $o->received_amount ?? 0;                   // thực nhận

        // build array đưa xuống view
        $order = [
            'id' => $o->madon,
            'created_at' => $o->ngaydat,
            'delivery_status' => $o->delivery_status,
            'trangthai' => $o->trangthai,
            'shipping_method_label' => $o->shipping_method_label,
            'notes' => $o->notes ?? '',
            'items' => $items,
            // những giá trị tính ra
            'sum_items' => $sumItems,
            'discount' => $discount,
            'shipping_fee' => $shipping,
            'total_order_value' => $totalOrder,
            'paid_amount' => $paid,
            'refunded_amount' => $refunded,
            'received_amount' => $received,
            'customer_name' => $o->user->ten_nguoi_dung ?? '',
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
    public function updateDeliveryStatus(Request $request, $madon)
    {
        $request->validate([
            'delivery_status' => 'required|in:pending,waiting_pickup,shipping,delivered,returned,canceled,incomplete'
        ]);

        // Tìm theo mã đơn
        $order = DonHang::where('madon', $madon)->firstOrFail();

        $order->delivery_status = $request->delivery_status;
        $order->save();

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Đã cập nhật trạng thái giao hàng!');
    }
    public function ordersRefund($madon)
    {
        $order = DonHang::where('madon', $madon)->firstOrFail();

        // Chỉ cho phép refund nếu chưa delivered và đang ở trạng thái thanh toán phù hợp
        if ($order->delivery_status === 'delivered') {
            return back()->withErrors('Không thể hoàn tiền: đơn đã giao hàng.');
        }

        if (in_array($order->trangthai, ['waiting_refund', 'shipping'])) {
            return back()->withErrors('Không thể hoàn tiền trong trạng thái hiện tại.');
        }

        // Cập nhật trạng thái
        $order->trangthai = 5;               // Giả sử 5 = Đã hoàn tiền
        $order->save();

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Hoàn tiền thành công!');
    }

    public function inventoryIndex(Request $request)
    {
        $perPage = $request->get('perPage', 10);

        $query = SanPham::withSum(['activeVariants as tong_ton'], 'sl')
            ->orderBy('ten');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('masanpham', 'like', "%{$search}%")
                    ->orWhere('ten', 'like', "%{$search}%");
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

        $dsLoai = Loai::pluck('loai');

        return view('admin.inventory.index', compact(
            'products',
            'dsLoai',
            'perPage'
        ) + [
            'search' => $request->search,
            'type' => $request->type,
        ]);
    }
    public function lastImportPrice($productId)
    {
        $nhap = NhapKho::where('sanpham_id', $productId)
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
            ->whereIn('delivery_status', ['dathanhtoan', 'delivered']);

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
            $query->whereHas(
                'items',
                fn($q) =>
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

        $chartLabels = $overview->pluck('label');
        $orderCounts = $overview->pluck('so_don');
        // 6) Lấy danh sách sản phẩm cho dropdown
        $products = SanPham::orderBy('ten')->get(['id', 'ten']);

        // 7) Trả về view
        return view('admin.report.revenue', compact(
            'overview',
            'products',
            'period',
            'productId',
            'chartLabels',
            'orderCounts'
        ));
    }

    public function exportRevenue(Request $request)
    {
        $fileName = 'revenue_' . now()->format('Ymd_His') . '.xlsx';
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
    $start = Carbon::parse($request->start_date)->startOfDay();
$end   = Carbon::parse($request->end_date)  ->endOfDay();

    $query = \DB::table('donhang')
        ->join('donhang_sanpham', 'donhang.id','=', 'donhang_sanpham.donhang_id')
        ->join('sanpham', 'donhang_sanpham.sanpham_id','=', 'sanpham.id')
        ->whereBetween('donhang.created_at', [$start, $end])
        ->when($request->product && $request->product !== 'all', 
               fn($q) => $q->where('sanpham.id', $request->product))
        ->selectRaw("
            DATE(donhang.created_at)            AS Date,
            sanpham.ten                AS Product,
            SUM(donhang_sanpham.soluong)       AS Quantity,
            SUM(donhang_sanpham.dongia)     AS Total
        ")
        ->groupBy('Date','Product')
        ->orderBy('Date', 'asc');

    return $query->get()
                 ->map(fn($r) => (array)$r)
                 ->toArray();
}
    public function usersCreate()
    {
        return view('admin.users.create');
    }
    public function usersStore(Request $request)
{
    $data = $request->validate([
        'ten_nguoi_dung' => 'required|unique:nguoidung,ten_nguoi_dung',
        'email'          => 'required|email|unique:nguoidung,email',
        'sdt'            => 'nullable|string',
        'dia_chi'        => 'nullable|string',
        'mat_khau'       => 'required|string|min:3',
        'vai_tro'        => 'required|in:admin,customer',
    ], [
        'ten_nguoi_dung.unique' => 'Tên người dùng đã tồn tại, hãy nhập tên khác.',
        'email.unique'          => 'Email đã được sử dụng, hãy chọn email khác.',
    ]);

    // 1) Hash mật khẩu một chiều
    $data['mat_khau']     = Hash::make($data['mat_khau']);

    // 2) Encrypt reversible để hiển thị lại nếu cần
    $data['password_enc'] = Crypt::encryptString($request->mat_khau);

    // 3) Tạo user (nhớ thêm 'password_enc' vào $fillable của model)
    NguoiDung::create($data);

    return redirect()
        ->route('admin.users.index')
        ->with('success', 'Thêm người dùng thành công!');
}

    public function usersDestroy($id): RedirectResponse
    {
        // 0) Không cho xóa tài khoản đang đăng nhập
        if (Auth::id() == $id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Không thể xóa tài khoản đang đăng nhập.');
        }

        try {
            // 1) Lấy user ra để gọi delete() (nếu có event deleting)
            $user = NguoiDung::findOrFail($id);

            // 2) Kiểm tra user còn đơn hàng nào chưa giao không
            $hasUndelivered = $user->donHangs()
                ->where('delivery_status', '!=', 'delivered')
                ->exists();

            if ($hasUndelivered) {
                return redirect()
                    ->route('admin.users.index')
                    ->with('error', 'Không thể xóa vì người dùng còn đơn hàng chưa giao.');
            }

            // 3) Xóa bình thường
            $user->delete();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Xóa người dùng thành công.');
        } catch (\Throwable $e) {
            Log::error("Xóa user #{$id} lỗi: " . $e->getMessage());

            return redirect()
                ->route('admin.users.index')
                ->withErrors('Có lỗi xảy ra khi xóa người dùng, vui lòng thử lại.');
        }
    }
    public function membersIndex(Request $request)
    {
        $query = Member::with('user');

        // filter trạng thái (active/inactive)
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // filter bậc (Silver/Gold/Platinum)
        if ($request->filled('tier')) {
            $query->where('membership_tier', $request->tier);
        }

        $members = $query
            ->orderBy('id')
            ->paginate(10)
            ->appends($request->only('status', 'tier'));

        return view('admin.members.index', [
            'members' => $members,
            'filter' => $request->only('status', 'tier'),
        ]);
    }

    // SHOW form tạo
    public function membersCreate()
    {
        $users = NguoiDung::where('vai_tro', 'customer')
            ->doesntHave('member')->get(['id', 'ten_nguoi_dung', 'email']);
        return view('admin.members.create', compact('users'));
    }

    // STORE
    public function membersStore(Request $request)
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('nguoidung', 'id')
                    ->where('vai_tro', 'customer'),
            ],
            'membership_tier' => 'required|in:Silver,Gold,Platinum',
            'is_active' => 'required|boolean',
            'created_at' => 'required|date',
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

        // 1) Chỉ validate hai trường được phép chỉnh
        $data = $request->validate([
            'membership_tier' => ['required', Rule::in(['Silver', 'Gold', 'Platinum'])],
            'is_active' => ['required', 'boolean'],
        ]);

        // 2) Cập nhật
        $member->update($data);

        // 4) Chuyển về index với flash popup
        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Cập nhật thành viên thành công');
    }

    // 3) Redirect với flash message


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