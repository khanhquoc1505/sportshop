{{-- resources/views/admin/members/create.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-6 text-dark">Thêm Thành viên Mới</h1>

  {{-- Hiển thị lỗi validate --}}
  @if($errors->any())
    <div class="mb-4">
    <ul class="bg-red-100 text-red-800 p-3 rounded">
    @foreach($errors->all() as $error)
    <li>• {{ $error }}</li>
    @endforeach
    </ul>
    </div>
  @endif

  <form action="{{ route('admin.members.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md w-full">
    @csrf

    {{-- Chọn User --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Người dùng<span class="text-red-500">*</span></label>
      <select name="user_id" required
        class="w-full px-4 py-2 border rounded">
        <option value="">-- Chọn người dùng --</option>
       @foreach($users as $u)
  <option value="{{ $u->id }}"
    {{ old('user_id') == $u->id ? 'selected': '' }}>
    {{ $u->ten_nguoi_dung }}
  </option>
@endforeach
      </select>
    </div>

    {{-- Cấp độ thành viên --}}
    <div class="mb-4">
    <label class="block text-gray-700 mb-1">Cấp độ thành viên<span class="text-red-500">*</span></label>
    <select name="membership_tier"
      class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
      required>
      <option value="">-- Chọn cấp độ --</option>
      <option value="Silver" {{ old('membership_tier') === 'Silver' ? 'selected' : '' }}>Silver</option>
      <option value="Gold" {{ old('membership_tier') === 'Gold' ? 'selected' : '' }}>Gold</option>
      <option value="Platinum" {{ old('membership_tier') === 'Platinum' ? 'selected' : '' }}>Platinum</option>
    </select>
    </div>

    {{-- Trạng thái (Active / Inactive) --}}
    <div class="mb-4">
    <label class="block text-gray-700 mb-1">Trạng thái<span class="text-red-500">*</span></label>
    <select name="is_active"
      class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
      required>
      <option value="1" {{ old('is_active') === '1' ? 'selected' : '' }}>Active</option>
      <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
    </select>
    </div>

    {{-- Ngày gia nhập --}}
    <div class="mb-4">
    <label class="block text-gray-700 mb-1">Ngày gia nhập<span class="text-red-500">*</span></label>
    <input type="date" name="created_at" value="{{ old('created_at', now()->toDateString()) }}"
      class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
      required />
    </div>

    <div class="flex justify-between">
    <a href="{{ route('admin.members.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
      ← Quay lại
    </a>
    <button type="submit" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
      Lưu
    </button>
    </div>
  </form>
@endsection