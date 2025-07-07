@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Thêm danh mục</h1>

  <form action="{{ route('admin.categories.store') }}"
        method="POST"
        class="bg-white rounded-xl shadow p-6 space-y-6">
    @csrf

    {{-- Tên danh mục --}}
   <div>
    <label class="block mb-1 font-medium">Tên danh mục</label>
    <input type="text"
           name="loai"
           value="{{ old('loai') }}"
           class="w-full border px-4 py-2 rounded focus:ring-2 focus:ring-primary"
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
        <option value="1" {{ old('status')==='1' ? 'selected':'' }}>Active</option>
        <option value="0" {{ old('status')==='0' ? 'selected':'' }}>Inactive</option>
      </select>
      @error('status')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Ngày tạo --}}
    <div>
      <label class="block mb-1 font-medium">Ngày tạo</label>
      <input type="date" name="created_at"
             value="{{ old('created_at', now()->format('Y-m-d')) }}"
             class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary"
             required>
      @error('created_at')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Nút Thêm --}}
    <div class="flex justify-between">
    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
      ← Quay lại
    </a>
    <button type="submit" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
      Thêm Danh Mục
    </button>
    </div>
  </form>
@endsection
