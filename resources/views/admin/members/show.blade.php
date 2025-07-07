{{-- resources/views/admin/members/show.blade.php --}}
@extends('layouts.admin')

@section('content')
  {{-- Breadcrumb --}}
  <nav class="text-sm text-gray-500 mb-2">
    <a href="{{ route('admin.members.index') }}" class="hover:underline">Thành viên</a>
    <span class="px-1">/</span>
    <span class="font-semibold text-gray-700">{{ $member['name'] }}</span>
  </nav>

  {{-- Tiêu đề & Nút quay lại --}}
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-black">Chi tiết Thành viên</h1>
    <a href="{{ route('admin.members.index') }}"
       class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
      ← Quay lại
    </a>
  </div>

  {{-- Thông tin chung --}}
  <div class="bg-white rounded-lg shadow p-6 space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- Tên --}}
      <div>
        <p class="text-sm text-gray-600">Tên</p>
        <p class="text-lg font-medium">{{ optional($member->user)->ten_nguoi_dung ?? '—' }}</p>
      </div>

      {{-- Email --}}
      <div>
        <p class="text-sm text-gray-600">Email</p>
        <p class="text-lg font-medium">{{ optional($member->user)->email ?? '—' }}</p>
      </div>

      {{-- SĐT --}}
      <div>
        <p class="text-sm text-gray-600">Số điện thoại</p>
        <p class="text-lg font-medium">{{ optional($member->user)->phone ?? optional($member->user)->sdt ?? '—' }}</p>
      </div>

      {{-- Ngày gia nhập --}}
      <div>
        <p class="text-sm text-gray-600">Ngày gia nhập</p>
        <p class="text-lg font-medium">{{ \Carbon\Carbon::parse($member['joined_at'])->format('d/m/Y') }}</p>
      </div>

      {{-- Cấp độ thành viên --}}
      <div>
        <p class="text-sm text-gray-600">Cấp độ thành viên</p>
        <p class="text-lg font-medium">{{ $member['membership_tier'] }}</p>
      </div>

      {{-- Trạng thái --}}
      <div>
        <p class="text-sm text-gray-600">Trạng thái</p>
        <div class="mt-1">
          @if($member['is_active'])
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Active</span>
          @else
            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">Inactive</span>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
