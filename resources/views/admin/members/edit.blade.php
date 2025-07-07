{{-- resources/views/admin/members/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-6">Chỉnh sửa Thành viên</h1>

  {{-- Hiển thị lỗi validate nếu có --}}
  @if($errors->any())
    <div class="mb-4">
      <ul class="bg-red-100 text-red-800 p-3 rounded">
        @foreach($errors->all() as $error)
          <li>• {{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.members.update', $member->id) }}" method="POST"
        class="bg-white p-6 rounded-lg shadow-md w-full">
    @csrf
    @method('PUT')

    {{-- Tên (chỉ đọc) --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Tên</label>
      <input type="text" value="{{ optional($member->user)->ten_nguoi_dung }}" disabled
             class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100" />
    </div>

    {{-- Email (chỉ đọc) --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Email</label>
      <input type="email" value="{{ optional($member->user)->email }}" disabled
             class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100" />
    </div>

    {{-- Số điện thoại (chỉ đọc) --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Số điện thoại</label>
      <input type="text" value="{{ optional($member->user)->sdt ?? '—' }}" disabled
             class="w-full px-4 py-2 border border-gray-300 rounded bg-gray-100" />
    </div>

    {{-- Cấp độ thành viên --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Cấp độ thành viên<span class="text-red-500">*</span></label>
      <select name="membership_tier" required
              class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        <option value="Silver"   @selected($member->membership_tier==='Silver')>Silver</option>
        <option value="Gold"     @selected($member->membership_tier==='Gold')>Gold</option>
        <option value="Platinum" @selected($member->membership_tier==='Platinum')>Platinum</option>
      </select>
    </div>

    {{-- Trạng thái --}}
    <div class="mb-4">
      <label class="block text-gray-700 mb-1">Trạng thái<span class="text-red-500">*</span></label>
      <select name="is_active" required
              class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary">
        <option value="1" @selected($member->is_active)>Active</option>
        <option value="0" @selected(!$member->is_active)>Inactive</option>
      </select>
    </div>

    <div class="flex justify-between items-center">
      <a href="{{ route('admin.members.index') }}"
         class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
        ← Quay lại
      </a>
      <button type="submit"
              class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
        Cập nhật
      </button>
    </div>
  </form>
@endsection
