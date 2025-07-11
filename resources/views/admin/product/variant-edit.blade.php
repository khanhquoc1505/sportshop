@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6">Chỉnh sửa biến thể sản phẩm “{{ $v->product->ten }}”</h1>

  <form method="POST" action="{{ route('admin.variant.update', $v->id) }}" enctype="multipart/form-data"
    class="bg-white p-6 rounded-xl shadow space-y-6 border border-gray-300">
    @csrf
    @method('PATCH')

    {{-- 1) Thông tin sản phẩm gốc --}}
    <div class="grid grid-cols-2 gap-6">
    <div>
      <label class="block mb-1 font-medium">Sản phẩm</label>
      <input type="text" value="{{ $v->product->ten }}" class="w-full border px-3 py-2 rounded bg-gray-100" disabled>
    </div>

    {{-- Loại sản phẩm --}}
    <div>
      <label class="block mb-1">Loại sản phẩm</label>
      <select name="loai_id" class="w-full border px-3 py-2 rounded">
      @foreach($categories as $cat)
      <option value="{{ $cat->id }}" {{ $v->product->loais->contains('id', $cat->id) ? 'selected':'' }}>
      {{ $cat->loai }}
      </option>
    @endforeach
      </select>
    </div>

    {{-- Giá nhập --}}
    <div>
      <label class="block mb-1 font-medium">Giá nhập</label>
      <input type="number" name="gia_nhap" value="{{ old('gia_nhap', $giaNhapCu) }}"
      class="w-full border px-3 py-2 rounded">
    </div>

    {{-- Giá bán --}}
    <div>
      <label class="block mb-1 font-medium">Giá bán</label>
      <input type="number" name="gia_ban" value="{{ old('gia_ban', $v->product->gia_ban) }}"
      class="w-full border px-3 py-2 rounded">
    </div>

    {{-- Giá bán buôn --}}
    <div>
      <label class="block mb-1 font-medium">Giá bán buôn</label>
      <input type="number" name="gia_buon" value="{{ old('gia_buon', $v->product->gia_buon) }}"
      class="w-full border px-3 py-2 rounded">
    </div>

    {{-- Bộ môn --}}
    <div>
      <label class="block mb-1 font-medium">Bộ môn</label>
      <input type="text" name="bo_mon" value="{{ old('bo_mon', $v->product->bo_mon) }}"
      class="w-full border px-3 py-2 rounded">
    </div>
    </div>

    {{-- 2) Thông tin biến thể --}}
    <div class="grid grid-cols-3 gap-6">
    {{-- Số lượng --}}
    <div>
      <label class="block mb-1 font-medium">Số lượng</label>
      <input type="number" name="sl" value="{{ old('sl', $v->sl) }}" class="w-full border px-3 py-2 rounded">
    </div>

    {{-- Size --}}
    <div>
      <label class="block mb-1 font-medium">Size</label>
      <select name="kichco_id" class="w-full border px-3 py-2 rounded">
      @foreach(\App\Models\KichCo::all() as $kc)
      <option value="{{ $kc->id }}" {{ old('kichco_id', $v->kichco_id) == $kc->id ? 'selected' : '' }}>
      {{ $kc->size }}
      </option>
    @endforeach
      </select>
    </div>

    {{-- Màu sắc --}}
    <div>
      <label class="block mb-1 font-medium">Màu sắc</label>
      <select name="mausac_id" class="w-full border px-3 py-2 rounded">
      @foreach(\App\Models\MauSac::all() as $m)
      <option value="{{ $m->id }}" {{ old('mausac_id', $v->mausac_id) == $m->id ? 'selected' : '' }}>
      {{ $m->mausac }}
      </option>
    @endforeach
      </select>
    </div>

    {{-- Ảnh biến thể --}}
    <div class="mb-4">
    <label for="images" class="block text-gray-700 mb-1">
      Hình ảnh (nếu đổi, có thể chọn nhiều)
    </label>
    <input
      type="file"
      name="images[]"
      id="images"
      multiple
      accept="image/*"
      class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
    />
    @error('images.*')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
  </div>

    {{-- Trạng thái --}}
    <div>
      <label class="block mb-1 font-medium">Trạng thái</label>
      <select name="trang_thai" class="w-full border px-3 py-2 rounded">
      <option value="1" {{ old('trang_thai', $v->trang_thai) == '1' ? 'selected' : '' }}>Hiển thị</option>
      <option value="0" {{ old('trang_thai', $v->trang_thai) == '0' ? 'selected' : '' }}>Ẩn</option>
      </select>
    </div>
    </div>

    {{-- 3) Nút lưu / hủy --}}
    <div class="flex justify-between">
    <a href="{{ route('admin.product.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
      ← Quay lại
    </a>
    <button type="submit" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
      Lưu thay đổi
    </button>
    </div>
  </form>
@endsection