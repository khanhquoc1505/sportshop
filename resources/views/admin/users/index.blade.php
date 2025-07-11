@extends('layouts.admin')

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
{{-- Error Toast from with() --}}
@if (session('error'))
  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'error',
          title: @json(session('error')),
          showConfirmButton: false,
          timer: 3000,
          toast: true,
          position: 'top-end'
        });
      });
    </script>
  @endpush
@endif

{{-- Error Toast from withErrors() --}}
@if ($errors->any())
  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'error',
          title: @json($errors->first()),
          showConfirmButton: false,
          timer: 3000,
          toast: true,
          position: 'top-end'
        });
      });
    </script>
  @endpush
@endif
  <h1 class="text-3xl font-semibold mb-6 text-dark">Quản lý người dùng</h1>

  {{-- Tìm kiếm --}}
  <div class="mb-4">
    <input type="text" id="searchInput" placeholder="Nhập tên người dùng..."class="px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-primary">
    <a href="{{ route('admin.users.create') }}"
   class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
   Thêm người dùng
</a>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-xl shadow p-8 w-full overflow-auto border border-gray-300">
    <table id="usersTable" class="w-full table-auto">
    <thead>
      <tr class="bg-gray-200 text-gray-700 text-center">
      <th class="px-4 py-2">ID</th>
      <th class="px-4 py-2">Tên</th>
      <th class="px-4 py-2">Email</th>
      <th class="px-4 py-2">SĐT</th>
      <th class="px-4 py-2">Địa chỉ</th>
      <th class="px-4 py-2">Vai trò</th>
      <th class="px-4 py-2">Mật khẩu</th>
      <th class="px-4 py-2">Ngày tạo</th>
      <th class="px-4 py-2">Cập nhật</th>
      <th class="px-4 py-2">Hành động</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $u)
      <tr class="border-b text-center">
      <td class="px-4 py-2">{{ $u['id'] }}</td>
      <td class="px-4 py-2 name">{{ $u['ten_nguoi_dung'] }}</td>
      <td class="px-4 py-2">{{ $u['email'] }}</td>
      <td class="px-4 py-2">{{ $u['sdt'] }}</td>
      <td class="px-4 py-2">{{ $u['dia_chi'] }}</td>
      <td class="px-4 py-2">
      <span class="px-2 py-1 rounded-full text-sm
        {{ $u['vai_tro'] == 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
      {{ ucfirst($u['vai_tro']) }}
      </span>
      </td>
      <td class="px-4 py-2">{{ $u->mat_khau ?? '—' }}</td>
      <td class="px-4 py-2 text-sm">
      {{ \Carbon\Carbon::parse($u['created_at'])->format('d/m/Y H:i') }}
      </td>
      <td class="px-4 py-2 text-sm">
      {{ \Carbon\Carbon::parse($u['updated_at'])->format('d/m/Y H:i') }}
      </td>
      <td class="px-4 py-2">
      <div class="flex justify-center space-x-2">
      <a href="{{ route('admin.users.edit', $u->id) }}"
        class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">
        Sửa
      </a>
      <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST"
        onsubmit="return confirm('Xác nhận xóa user này?')">
        @csrf @method('DELETE')
        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
        Xóa
        </button>
      </form>
      </div>
      </td>
      </tr>
    @endforeach
    </tbody>
    </table>
  </div>  
@endsection

@push('scripts')
  <script>
    // Ẩn thông báo sau 3 giây
  document.addEventListener('DOMContentLoaded', () => {
    const alert = document.querySelector('.bg-green-100');
    if (alert) {
      setTimeout(() => {
        alert.style.display = 'none';
      }, 3000);
    }
  });
    (() => {
    const input = document.getElementById('searchInput');
    const rows = document.querySelectorAll('#usersTable tbody tr');

    input.addEventListener('input', function () {
      const query = this.value.trim().toLowerCase();

      rows.forEach(row => {
      const nameCell = row.querySelector('.name');
      if (!nameCell) return;

      const name = nameCell.innerText.replace(/\s+/g, ' ').toLowerCase();
      row.style.display = name.includes(query) ? '' : 'none';
      });
    });
    })();
  </script>
@endpush

