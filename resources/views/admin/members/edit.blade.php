{{-- resources/views/admin/members/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-6 text-dark">Chỉnh sửa Thành viên</h1>

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

  <form action="{{ route('admin.members.update', $member['id']) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md w-full md:w-2/3">
    @csrf
    @method('PUT')

    {{-- Tên --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Tên<span class="text-red-500">*</span></label>
      <input
        type="text"
        name="name"
        value="{{ old('name', $member['name']) }}"
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
        required
      />
    </div>

    {{-- Email --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Email<span class="text-red-500">*</span></label>
      <input
        type="email"
        name="email"
        value="{{ old('email', $member['email']) }}"
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
        required
      />
    </div>

    {{-- Số điện thoại --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Số điện thoại</label>
      <input
        type="text"
        name="phone"
        value="{{ old('phone', $member['phone']) }}"
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
      />
    </div>

    {{-- Cấp độ thành viên --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Cấp độ thành viên<span class="text-red-500">*</span></label>
      <select
        name="membership_tier"
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
        required
      >
        <option value="">-- Chọn cấp độ --</option>
        <option value="Silver"   {{ old('membership_tier', $member['membership_tier']) === 'Silver'   ? 'selected' : '' }}>Silver</option>
        <option value="Gold"     {{ old('membership_tier', $member['membership_tier']) === 'Gold'     ? 'selected' : '' }}>Gold</option>
        <option value="Platinum" {{ old('membership_tier', $member['membership_tier']) === 'Platinum' ? 'selected' : '' }}>Platinum</option>
      </select>
    </div>

    {{-- Trạng thái (Active / Inactive) --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Trạng thái<span class="text-red-500">*</span></label>
      <select
        name="is_active"
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
        required
      >
        <option value="1" {{ old('is_active', $member['is_active']) == true ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('is_active', $member['is_active']) == false ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>
    <div class="flex justify-between">
      <a
        href="{{ route('admin.members.index') }}"
        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
      >
        ← Quay lại
      </a>
      <button
        type="submit"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
      >
        Cập nhật
      </button>
    </div>
  </form>
@endsection
