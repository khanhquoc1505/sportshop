<?php

namespace App\Http\Controllers;
use App\Models\Loai;
use App\Models\Bomon;
use App\Models\SanPham;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;


class TiemKiemController extends Controller
{
    public function boot()
{
    View::share('loais', Loai::where('status',1)->get());
    View::share('bomons', Bomon::all());
}


public function search(Request $request)
    {
        $q     = $request->query('q');
        $loai  = $request->query('loai');
        $bomon = $request->query('bomon');

        $query = SanPham::query();

        if ($q) {
            $query->where('ten', 'like', "%{$q}%");
        }

        // Lọc theo loại qua relation loais
        if ($loai) {
            $query->whereHas('loais', function ($q2) use ($loai) {
                $q2->where('loai.id', $loai);
            });
        }

        // Lọc theo bộ môn qua relation bomons
        if ($bomon) {
            $query->whereHas('bomons', function ($q2) use ($bomon) {
                $q2->where('bomon.id', $bomon);
            });
        }

        $products = $query->paginate(12)->withQueryString();

        return view('layouts.timkiemSP', compact('products', 'q', 'loai', 'bomon'));
    }
}
