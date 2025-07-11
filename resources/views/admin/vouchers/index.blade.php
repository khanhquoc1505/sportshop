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
  <h1 class="text-3xl font-semibold mb-6 text-dark">Quản lý Voucher</h1>

  <div class="flex items-center space-x-2 mb-4">
    {{-- Khai báo id cho form --}}
    <form id="filter-form" method="GET" action="{{ route('admin.vouchers.index') }}" class="flex items-center space-x-2">

    <input id="searchInput" type="text" name="search" placeholder="Nhập mã voucher..." value="{{ $search }}"
      class="px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-primary" />
    </form>
    
    <a href="{{ route('admin.vouchers.create') }}"
    class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
    Thêm Voucher
    </a>
  </div>

  <div id="voucher-list" class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full table-auto">
    <thead class="bg-gray-200 text-gray-700 text-center">
      <tr>
      <th class="px-4 py-2">Mã Voucher</th>
      <th class="px-4 py-2">Sản phẩm (SKU)</th>
      <th class="px-4 py-2">Loại</th>
      <th class="px-4 py-2">Giá trị</th>
      <th class="px-4 py-2">Số Lượng</th>
      <th class="px-4 py-2">Ngày tạo</th>
      <th class="px-4 py-2">Hạn dùng</th>
      <th class="px-4 py-2">Hành động</th>
      </tr>
    </thead>
    <tbody id="voucher-body">
      @forelse($vouchers as $voucher)
      <tr class="text-center border-b">
      <td class="voucher-code px-4 py-2">{{ $voucher->ma_voucher }}</td>
      <td class="px-4 py-2">
      @foreach($voucher->sanphams as $sp)
      <span class="inline-block bg-gray-100 px-2 py-1 rounded text-sm">
      {{ $sp->masanpham }}
      </span>
      @endforeach
      </td>
      <td class="px-4 py-2 capitalize">{{ $voucher->loai }}</td>
      <td class="px-4 py-2">
      @if($voucher->loai === 'fixed')
      {{ number_format($voucher->soluong, 0, ',', '.') }} ₫
      @else
      {{ $voucher->soluong }} %
      @endif
      </td>
      <td class="px-4 py-2">{{ $voucher->soluong }}</td>
      <td class="px-4 py-2">{{ $voucher->ngay_bat_dau }}</td>
      <td class="px-4 py-2">{{ $voucher->ngay_ket_thuc }}</td>
      <td class="px-4 py-2">
      <a href="{{ route('admin.vouchers.edit', $voucher->id) }}"
      class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500">Sửa</a>
      <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" class="inline-block"
      onsubmit="return confirm('Xác nhận xóa voucher này?')">
      @csrf @method('DELETE')
      <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
        Xóa
      </button>
      </form>
      </td>
      </tr>
    @empty
      <tr>
      <td colspan="8" class="py-6 text-center text-gray-500">Không có voucher nào</td>
      </tr>
    @endforelse
    </tbody>
    </table>

    @if($vouchers->hasPages())
    <div class="p-4">{{ $vouchers->links() }}</div>
    @endif
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('searchInput');
    const rows = document.querySelectorAll('#voucher-body tr');

    function strip(str) {
      return str.normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase();
    }

    input.addEventListener('input', () => {
      const q = strip(input.value);
      rows.forEach(tr => {
      const codeCell = tr.querySelector('.voucher-code');
      if (!codeCell) return; // phòng khi null
      const code = strip(codeCell.textContent);
      tr.style.display = code.includes(q) ? '' : 'none';
      });
    });
    });
  </script>
@endpush