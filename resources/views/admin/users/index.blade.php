@extends('layouts.admin')

@section('content')
  <h1 class="text-3xl font-semibold mb-6 text-dark">Quản lý người dùng</h1>

  {{-- Search & Filters --}}
  <div class="flex items-center space-x-2 mb-4">
    <input id="searchInput" type="text" placeholder="Tìm kiếm ID, tên hoặc email"
    class="px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-primary" />

    <select id="statusSelect" class="px-4 py-2 border rounded">
    <option value="">-- Trạng thái --</option>
    <option value="active">Active</option>
    <option value="inactive">Inactive</option>
    </select>

    <select id="roleSelect" class="px-4 py-2 border rounded">
    <option value="">-- Vai trò --</option>
    <option value="admin">Admin</option>
    <option value="customer">Customer</option>
    </select>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-xl shadow p-8 w-full overflow-auto shadow border border-gray-300">
    <table id="usersTable" class="w-full table-auto">
    <thead>
      <tr class="bg-gray-200 text-gray-700 text-center">
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
      @foreach($users as $u)
      <tr class="border-b text-center">
      <td class="px-4 py-2 id">{{ $u['id'] }}</td>
      <td class="px-4 py-2 name">{{ $u['name'] }}</td>
      <td class="px-4 py-2 email">{{ $u['email'] }}</td>
      <td class="px-4 py-2 phone">{{ $u['phone'] }}</td>
      <td class="px-4 py-2 role">
      <span class="px-2 py-1 rounded-full text-sm
       {{ $u['role'] == 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
      {{ ucfirst($u['role']) }}
      </span>
      </td>
      <td class="px-4 py-2 status">
      @if($u['is_active'])
      <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Active</span>
      @else
      <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">Inactive</span>
      @endif
      </td>
      <td class="px-4 py-2 space-x-2">
      <a href="{{ route('admin.users.edit', $u['id']) }}"
      class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">
      Sửa
      </a>
      <form action="{{ route('admin.users.destroy', $u['id']) }}" method="POST" class="inline"
      onsubmit="return confirm('Xác nhận xóa user này?')">
      @csrf @method('DELETE')
      <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
        Xóa
      </button>
      </form>
      </td>
      </tr>
    @endforeach
    </tbody>
    </table>
  </div>
@endsection

@push('scripts')
  <script>
    (() => {
    const input = document.getElementById('searchInput');
    const status = document.getElementById('statusSelect');
    const role = document.getElementById('roleSelect');
    const rows = document.querySelectorAll('#usersTable tbody tr');

    function filterRows() {
      const q = input.value.trim().toLowerCase();
      const stVal = status.value;   // "" / "active" / "inactive"
      const rlVal = role.value;     // "" / "admin" / "customer"

      rows.forEach(tr => {
      // Lấy text của từng cell qua class
      const id = tr.querySelector('.id').textContent.trim().toLowerCase();
      const name = tr.querySelector('.name').textContent.trim().toLowerCase();
      const email = tr.querySelector('.email').textContent.trim().toLowerCase();
      const rl = tr.querySelector('.role').textContent.trim().toLowerCase();
      const st = tr.querySelector('.status').textContent.trim().toLowerCase();

      const matchText = !q
        || id.includes(q)
        || name.includes(q)
        || email.includes(q);

      const matchStatus = !stVal
        || (stVal === 'active' && st === 'active')
        || (stVal === 'inactive' && st === 'inactive');

      const matchRole = !rlVal
        || (rlVal === 'admin' && rl === 'admin')
        || (rlVal === 'customer' && rl === 'customer');

      tr.style.display = (matchText && matchStatus && matchRole)
        ? ''
        : 'none';
      });
    }

    // Gọi 1 lần lúc load
    filterRows();

    // Gán sự kiện
    input.addEventListener('input', filterRows);
    status.addEventListener('change', filterRows);
    role.addEventListener('change', filterRows);
    })();
  </script>
@endpush