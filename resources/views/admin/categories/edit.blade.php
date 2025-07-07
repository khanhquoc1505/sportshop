@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Chỉnh sửa danh mục</h1>

  <form action="{{ route('admin.categories.update', $category->id) }}"
        method="POST"
        class="bg-white rounded-lg shadow p-6 space-y-6">
    @csrf
    @method('PATCH')

    {{-- Hiển thị ID --}}
    <div>
      <label class="block mb-1 font-medium">ID</label>
      <input type="text" value="{{ $category->id }}"
             class="w-24 border px-4 py-2 rounded bg-gray-100" disabled>
    </div>

    {{-- Tên danh mục --}}
    <div>
      <label class="block mb-1 font-medium">Tên danh mục</label>
      {{-- Đổi name thành "loai" cho khớp bảng --}}
      <input type="text" name="loai"
             value="{{ old('loai', $category->loai) }}"
             class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary"
             placeholder="Nhập tên danh mục" required>
      @error('loai')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Trạng thái --}}
    <div>
      <label class="block mb-1 font-medium">Trạng thái</label>
      <select name="status"
              class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary"
              required>
        <option value="1" {{ old('status', $category->status) == 1 ? 'selected':'' }}>Active</option>
        <option value="0" {{ old('status', $category->status) == 0 ? 'selected':'' }}>Inactive</option>
      </select>
      @error('status')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Ngày tạo (chỉ ngày) --}}
    <div>
      <label class="block mb-1 font-medium">Ngày tạo</label>
      <input type="date" name="created_at"
             {{-- Eloquent timestamp sẽ ở dạng Y-m-d H:i:s, ta chỉ lấy phần Y-m-d --}}
             value="{{ old('created_at', \Carbon\Carbon::parse($category->created_at)->format('Y-m-d')) }}"
             class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary"
             required>
      @error('created_at')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Nút Lưu & Hủy --}}
    <div class="flex justify-between">
    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
      ← Quay lại
    </a>
    <button type="submit" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
      Cập nhật
    </button>
    </div>
  </form>
@endsection
