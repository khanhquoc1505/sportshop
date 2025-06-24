{{-- resources/views/admin/variants-edit.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Chỉnh sửa biến thể sản phẩm {{ $v->product->ten }}</h1>

  <form method="POST"
        action="{{ route('admin.variant.update', $v->id) }}"
        enctype="multipart/form-data"
        class="bg-white p-6 rounded-xl shadow space-y-4 border border-gray-300"
  >
    @csrf
    @method('PATCH')

    {{-- Sản phẩm gốc --}}
    <div>
      <label class="block mb-1">Sản phẩm</label>
      <input type="text"
             value="{{ $v->product->ten}}"
             class="w-full border px-3 py-2 rounded bg-gray-100"
             disabled
      />
    </div>

    {{-- Số lượng --}}
    <div>
      <label class="block mb-1">Số lượng</label>
      <input type="number"
             name="sl"
             value="{{ old('sl', $v->sl) }}"
             class="w-full border px-3 py-2 rounded"
      />
    </div>

    {{-- Size --}}
    <div>
      <label class="block mb-1">Size</label>
      <select name="kichco_id" class="w-full border px-3 py-2 rounded">
        @foreach(\App\Models\KichCo::all() as $kc)
          <option value="{{ $kc->id }}"
                  {{ $v->kichco_id == $kc->id ? 'selected' : '' }}>
            {{ $kc->size }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Màu sắc --}}
    <div>
      <label class="block mb-1">Màu sắc</label>
      <select name="mausac_id" class="w-full border px-3 py-2 rounded">
        @foreach(\App\Models\MauSac::all() as $m)
          <option value="{{ $m->id }}"
                  {{ $v->mausac_id == $m->id ? 'selected' : '' }}>
            {{ $m->mausac }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Hình ảnh --}}
    <div>
      <label class="block mb-1">Hình ảnh (nếu đổi)</label>
      <input type="file" name="hinh_anh" class="block w-full" />
      @if($v->hinh_anh)
        <img src="{{ asset('storage/'.$v->hinh_anh) }}"
             class="h-24 mt-2 object-contain rounded border" />
      @endif
    </div>

    {{-- Trạng thái--}}
     <div>
        <label for="trang_thai" class="block mb-1">Trạng thái</label>
        <select name="trang_thai" id="trang_thai" class="w-full border px-3 py-2 rounded">
            <option value="1" {{ $v->trang_thai == '1' ? 'selected' : '' }}>Hiển thị</option>
            <option value="0" {{ $v->trang_thai == '0' ? 'selected' : '' }}>Ẩn</option>
        </select>
    </div>

    {{-- Ngày tạo (chỉ xem) --}}
    <div>
      <label class="block mb-1">Thời gian thêm</label>
      <input type="date"
             value="{{ $v->created_at->format('Y-m-d') }}"
             class="w-full border px-3 py-2 rounded bg-gray-100"
             disabled
      />
    </div>

    {{-- Nút hành động --}}
    <div class="flex space-x-2 mt-6">
      <button type="submit"
              onclick="return confirm('Bạn có chắc muốn lưu thay đổi không?')"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
      >
        Lưu thay đổi
      </button>
      <a href="{{ route('admin.product.index') }}"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition"
      >
        Hủy
      </a>
    </div>
  </form>
@endsection
