@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Chỉnh sửa danh mục</h1>

  <form action="{{ route('admin.categories.update', $current['id']) }}"
        method="POST"
        class="bg-white rounded-xl shadow p-6 space-y-6">
    @csrf @method('PATCH')

    {{-- Hiển thị ID (disabled) --}}
    <div>
      <label class="block mb-1 font-medium">ID</label>
      <input type="text" value="{{ $current['id'] }}"
             class="w-24 border px-4 py-2 rounded bg-gray-100" disabled>
    </div>

    {{-- Tên danh mục --}}
    <div>
      <label class="block mb-1 font-medium">Tên danh mục</label>
      <input type="text" name="name"
             value="{{ old('name', $current['name']) }}"
             class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary"
             required>
      @error('name')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Trạng thái --}}
    <div>
      <label class="block mb-1 font-medium">Trạng thái</label>
      <select name="status"
              class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary"
              required>
        <option value="1" {{ old('status', $current['status']) == 1 ? 'selected':'' }}>Active</option>
        <option value="0" {{ old('status', $current['status']) == 0 ? 'selected':'' }}>Inactive</option>
      </select>
      @error('status')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Danh mục cha --}}
    <div>
      <label class="block mb-1 font-medium">Danh mục cha (nếu có)</label>
      <select name="parent"
              class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        <option value="">-- Không --</option>
        @foreach($parents as $p)
          <option value="{{ $p['id'] }}"
            {{ old('parent', $current['parent']) == $p['id'] ? 'selected':'' }}>
            {{ $p['name'] }}
          </option>
        @endforeach
      </select>
      @error('parent')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Ngày tạo --}}
    <div>
      <label class="block mb-1 font-medium">Ngày tạo</label>
      <input type="date" name="created_at"
             value="{{ old('created_at', $current['created_at']) }}"
             class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-primary"
             required>
      @error('created_at')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Nút Lưu & Hủy --}}
    <div class="pt-4 space-x-4">
      <button type="submit"
              class="px-6 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
        Lưu
      </button>
      <a href="{{ route('admin.categories.index') }}"
         class="px-6 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
        Hủy
      </a>
    </div>
  </form>
@endsection
