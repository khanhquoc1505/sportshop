@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6 text-dark">Chỉnh sửa sản phẩm</h1>

    <form method="POST" action="{{ route('admin.product.update', $item->id) }}" enctype="multipart/form-data"
        class="bg-white p-6 rounded-xl shadow space-y-4 border border-gray-300">
        @csrf
        @method('PATCH')

        <div>
            <label class="block mb-1">Tên sản phẩm</label>
            <input type="text" name="ten" value="{{ old('ten', $item->ten) }}"
                class="w-full border px-3 py-2 rounded" required />
        </div>

        <div>
            <label class="block mb-1">Mô tả</label>
            <textarea name="mo_ta" rows="4" class="w-full border px-3 py-2 rounded">{{ old('mo_ta', $item->mo_ta) }}</textarea>
        </div>

        <div>
            <label class="block mb-1">Giá bán</label>
            <input type="number" name="gia_ban" value="{{ old('gia_ban', $item->gia_ban) }}"
                class="w-full border px-3 py-2 rounded" required />
        </div>

        <div>
            <label class="block mb-1">Giá nhập</label>
            <input type="number" name="gia_nhap" value="{{ old('gia_nhap', $item->gia_nhap) }}"
                class="w-full border px-3 py-2 rounded" required />
        </div>

        <div>
            <label class="block mb-1">Kích cỡ</label>
            <select name="kichco_id" class="w-full border px-3 py-2 rounded">
                @foreach($kichcoList as $kc)
                    <option value="{{ $kc->id }}" {{ $item->kichco_id == $kc->id ? 'selected' : '' }}>{{ $kc->size }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Màu sắc</label>
            <select name="mausac_id" class="w-full border px-3 py-2 rounded">
                @foreach($mausacList as $mau)
                    <option value="{{ $mau->id }}" {{ $item->mausac_id == $mau->id ? 'selected' : '' }}>{{ $mau->mausac }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Số lượng</label>
            <input type="number" name="sl" value="{{ old('sl', $tongSoLuong) }}" class="border px-3 py-2 rounded bg-gray-100" />
        </div>

        <div>
            <label class="block mb-1">Hình ảnh</label>
            <input type="file" name="hinh_anh" class="block" />
            @if($item->hinh_anh)
                <img src="{{ asset('images/' . $item->hinh_anh) }}" class="h-24 mt-2 object-contain rounded" />
            @endif
        </div>

        <div>
            <label class="block mb-1">Thời gian thêm</label>
            <input type="date" name="thoi_gian_them"
                value="{{ old('thoi_gian_them', \Carbon\Carbon::parse($item->thoi_gian_them)->format('Y-m-d')) }}"
                class="border px-3 py-2 rounded" required />
        </div>

        <div>
            <label class="block mb-1">Trạng thái</label>
            <select name="trang_thai" class="w-full border px-3 py-2 rounded">
                <option value="1" {{ $item->trang_thai == '1' ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ $item->trang_thai == '0' ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <div class="flex space-x-2 mt-6">
            <button type="submit" onclick="return confirm('Bạn có chắc muốn lưu thay đổi không?')"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                Lưu
            </button>
            <a href="{{ route('admin.product.index') }}"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                Hủy
            </a>
        </div>
    </form>
@endsection
