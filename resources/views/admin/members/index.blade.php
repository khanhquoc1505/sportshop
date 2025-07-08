{{-- resources/views/admin/members/index.blade.php --}}
@extends('layouts.admin')

@section('content')
@section('content')
    @if (session('success'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        toast: true,
                        position: 'top-end'
                    });
                });
            </script>
        @endpush
    @endif
  <h1 class="text-3xl font-semibold mb-6 text-dark">Quản lý Thành viên</h1>
  {{-- 1) Search & Filter --}}
  <div class="flex flex-wrap gap-3 items-center mb-4">
    {{-- Search box (auto-filter) --}}
    <input
      id="memberSearch"
      type="text"
      placeholder="Nhập tên thành viên..."
      class="px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-primary"
    />
    {{-- Form wrapper so filters work --}}
    <form id="memberFilterForm" method="GET" action="{{ route('admin.members.index') }}" class="hidden"></form>

    {{-- Nút Thêm thành viên --}}
    <a href="{{ route('admin.members.create') }}"
       class=" px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
      Thêm Thành viên
    </a>
  </div>

  {{-- 2) Table Danh sách Thành viên --}}
  <div class="bg-white rounded-xl shadow p-6 w-full overflow-auto border border-gray-200">
    <table id="membersTable" class="w-full table-auto">
      <thead>
        <tr class="bg-gray-100 text-gray-700 text-center">
          <th class="px-4 py-2">ID</th>
          <th class="px-4 py-2">Tên</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">SĐT</th>
          <th class="px-4 py-2">Bậc</th>
          <th class="px-4 py-2">Trạng thái</th>
          <th class="px-4 py-2">Hành động</th>
        </tr>
      </thead>
      <tbody id="membersTable">
        @forelse($members as $member)
          <tr class="border-b hover:bg-gray-50">
            {{-- ID --}}
            <td class="px-4 py-2 text-center text-sm member-id">{{ $member['id'] }}</td>

            {{-- Tên --}}
            <td class="px-4 py-2 member-name">
        {{ optional($member->user)->ten_nguoi_dung ?? '—' }}
      </td>

            {{-- Email --}}
            <td class="px-4 py-2 member-email">
        {{ optional($member->user)->email ?? '—' }}
      </td>

            {{-- SĐT --}}
           <td class="px-4 py-2 text-center">
       {{ optional($member->user)->phone ?? optional($member->user)->sdt ?? '—' }}
      </td>

            {{-- Bậc --}}
            <td class="px-4 py-2 text-center">
              @switch($member['membership_tier'])
                @case('Silver')
                  <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Silver</span>
                  @break
                @case('Gold')
                  <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Gold</span>
                  @break
                @case('Platinum')
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Platinum</span>
                  @break
                @default
                  <span class="px-2 py-1 bg-gray-200 text-gray-600 rounded-full text-sm">
                    {{ $member['membership_tier'] }}
                  </span>
              @endswitch
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
              <a href="{{ route('admin.members.show', $member['id']) }}"
                 class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-sm">
                Xem
              </a>
              <a href="{{ route('admin.members.edit', $member['id']) }}"
                 class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition text-sm">
                Sửa
              </a>
              <form action="{{ route('admin.members.destroy', $member['id']) }}"
                    method="POST" class="inline-block"
                    onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này?');">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm">
                  Xóa
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="py-6 text-center text-gray-500">
              Chưa có thành viên nào
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- 3) Phân trang --}}
    @if(method_exists($members, 'links'))
      <div class="mt-4">
        {{ $members->links() }}
      </div>
    @endif
  </div>
@endsection

@push('scripts')
<script>
(() => {
  const input = document.getElementById('memberSearch');
  const rows  = document.querySelectorAll('#membersTable tbody tr');

  input.addEventListener('input', () => {
    const q = input.value.trim().toLowerCase();

    rows.forEach(tr => {
      const id    = tr.querySelector('.member-id').textContent.toLowerCase();
      const name  = tr.querySelector('.member-name').textContent.toLowerCase();
      const email = tr.querySelector('.member-email').textContent.toLowerCase();
      const show  = id.includes(q) || name.includes(q) || email.includes(q);
      tr.style.display = show ? '' : 'none';
    });
  });
})();
</script>
@endpush
