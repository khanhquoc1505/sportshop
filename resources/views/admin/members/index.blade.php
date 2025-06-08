{{-- resources/views/admin/members/index.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Quản lý Thành viên</h1>

  {{-- 1. Search & Filter --}}
  <form method="GET" action="{{ route('admin.members.index') }}" class="flex flex-wrap gap-3 items-center mb-4">
    {{-- Search box: tìm theo ID, tên hoặc email --}}
    <input
      type="text"
      name="search"
      value="{{ request('search') }}"
      placeholder="Tìm kiếm ID, tên hoặc email"
      class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-primary"
    />

    {{-- Filter: Status --}}
    <select
      name="status"
      class="px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
    >
      <option value="">-- Trạng thái --</option>
      <option value="active"   {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>

    {{-- Filter: Role --}}
    <select
      name="role"
      class="px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary"
    >
      <option value="">-- Vai trò --</option>
      <option value="admin"    {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
      <option value="member"   {{ request('role') == 'member' ? 'selected' : '' }}>Member</option>
      <option value="guest"    {{ request('role') == 'guest' ? 'selected' : '' }}>Guest</option>
    </select>

    <button
      type="submit"
      class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition"
    >
      Bộ lọc
    </button>

    {{-- Nút Thêm Thành viên mới (nếu cần) --}}
    <a
      href="{{ route('admin.members.create') }}"
      class="ml-auto px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition"
    >
      Thêm Thành viên
    </a>
  </form>

  {{-- 2. Table Danh sách Thành viên --}}
  <div class="bg-white rounded-xl shadow p-6 w-full overflow-auto border border-gray-200">
    <table class="w-full table-auto">
      <thead>
        <tr class="bg-gray-100 text-gray-700 text-center">
          <th class="px-4 py-2">ID</th>
          <th class="px-4 py-2">Tên</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">SĐT</th>
          <th class="px-4 py-2">Vai trò</th>
          <th class="px-4 py-2">Trạng thái</th>
          <th class="px-4 py-2">Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse($members as $member)
          <tr class="border-b hover:bg-gray-50">
            {{-- ID --}}
            <td class="px-4 py-2 text-center text-sm">{{ $member['id'] }}</td>

            {{-- Tên --}}
            <td class="px-4 py-2 text-left">{{ $member['name'] }}</td>

            {{-- Email --}}
            <td class="px-4 py-2 text-left">{{ $member['email'] }}</td>

            {{-- SĐT --}}
            <td class="px-4 py-2 text-center">{{ $member['phone'] ?? '—' }}</td>

            {{-- Vai trò --}}
            <td class="px-4 py-2 text-center">
              @if($member['role'] === 'admin')
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Admin</span>
              @elseif($member['role'] === 'member')
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Member</span>
              @elseif($member['role'] === 'guest')
                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Guest</span>
              @else
                <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-full text-sm">{{ ucfirst($member['role'] ?? '—') }}</span>
              @endif
            </td>

            {{-- Trạng thái --}}
            <td class="px-4 py-2 text-center">
              @if($member['is_active'])
                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Active</span>
              @else
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Inactive</span>
              @endif
            </td>

            {{-- Hành động --}}
            <td class="px-4 py-2 text-center space-x-2">
              {{-- Xem chi tiết --}}
              <a
                href="{{ route('admin.members.show', $member['id']) }}"
                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-sm"
              >
                Xem
              </a>

              {{-- Sửa --}}
              <a
                href="{{ route('admin.members.edit', $member['id']) }}"
                class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition text-sm"
              >
                Sửa
              </a>

              {{-- Xóa --}}
              <form
                action="{{ route('admin.members.destroy', $member['id']) }}"
                method="POST"
                class="inline-block"
                onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này?');"
              >
                @csrf
                @method('DELETE')
                <button
                  type="submit"
                  class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm"
                >
                  Xóa
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="py-6 text-center text-gray-500">Chưa có thành viên nào</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- 3. Pagination --}}
    @if(method_exists($members, 'links'))
      <div class="mt-4">
        {{ $members->links() }}
      </div>
    @endif
  </div>
@endsection
