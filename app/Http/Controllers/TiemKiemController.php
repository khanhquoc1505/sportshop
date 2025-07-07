<?php

namespace App\Http\Controllers;

use App\Models\Loai;
use App\Models\Bomon;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TiemKiemController extends Controller
{
    public function __construct()
    {
        // Share dữ liệu cho sidebar filter ở mọi view
        View::share('loais', Loai::where('status', 1)->get());
        View::share('bomons', Bomon::all());
    }

    public function search(Request $request)
    {
        // Lấy giá trị input
        $q     = $request->query('q');
        $loai  = $request->query('loai');
        $bomon = $request->query('bomon');
        $sort  = $request->query('sort', 'default');

        // Khởi tạo Query Builder
        $query = SanPham::query();

        // Áp filter từ khóa, loại, bộ môn
        if ($q) {
            $query->where('ten', 'like', "%{$q}%");
        }
        if ($loai) {
            $query->whereHas('loais', fn($sub) => $sub->where('loai.id', $loai));
        }
        if ($bomon) {
            $query->whereHas('bomons', fn($sub) => $sub->where('bomon.id', $bomon));
        }

        // Áp sorting
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('gia_ban', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('gia_ban', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('ten', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('ten', 'desc');
                break;
            default:
                // Mặc định: mới nhất hoặc theo ID
                $query->orderBy('id', 'desc');
        }

        // Phân trang & giữ nguyên mọi query string (q, loai, bomon, sort)
        $products = $query
            ->paginate(12)
            ->appends($request->only(['q', 'loai', 'bomon', 'sort']));

        // Trả về view cùng data
        return view('layouts.timkiemSP', compact('products', 'q', 'loai', 'bomon', 'sort'));
    }
}
