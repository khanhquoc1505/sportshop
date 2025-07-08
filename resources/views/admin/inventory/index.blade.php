{{-- resources/views/admin/inventory/index.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Quản lý kho</h1>

  {{-- 2) Search & Filters --}}
  <form id="filterForm" method="GET" action="{{ route('admin.inventory.index') }}"
        class="flex items-center space-x-2 mb-4">
    <input
      type="text"
      name="search"
      id="searchBox"
      value="{{ $search ?? '' }}"
      placeholder="Tìm theo tên hoặc mã SP"
      class="px-4 py-2 border rounded focus:outline-none focus:ring w-1/3"
      oninput="document.getElementById('filterForm').submit()"
    />
    <select name="type"
            class="px-4 py-2 border rounded-lg"
            onchange="document.getElementById('filterForm').submit()">
      <option value="">-- Loại sản phẩm --</option>
      @foreach($dsLoai as $loaiItem)
        <option value="{{ $loaiItem }}"
          {{ ($type ?? '') === $loaiItem ? 'selected' : '' }}>
          {{ $loaiItem }}
        </option>
      @endforeach
    </select>
  </form>

  {{-- 3) Table --}}
  <div class="bg-white rounded-lg shadow overflow-auto">
    <table class="min-w-full table-auto">
      <thead>
        <tr class="bg-gray-100 text-gray-700 text-center">
          <th class="px-4 py-2">Mã SP</th>
          <th class="px-4 py-2">Tên SP</th>
          <th class="px-4 py-2">Loại SP</th>
          <th class="px-4 py-2">Tồn kho</th>
          <th class="px-4 py-2">Ngày khởi tạo</th>
          <th class="px-4 py-2">Giá bán lẻ</th>
          <th class="px-4 py-2">Giá nhập</th>
          <th class="px-4 py-2">Giá bán buôn</th>
        </tr>
      </thead>
      <tbody>
        @foreach($products as $p)
          <tr class="border-b hover:bg-gray-50 text-center">
            <td class="px-4 py-2">{{ $p->masanpham }}</td>
            <td class="px-4 py-2 text-left">{{ $p->ten }}</td>
            <td class="px-4 py-2">{{ $p->loais->first()->loai ?? '-' }}</td>
            <td class="px-4 py-2">
              {{-- ví dụ: nếu bạn có relation variants --}}
               {{ number_format($p->tong_ton ?? 0) }}
            </td>
            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($p->thoi_gian_them)->format('d/m/Y') }}</td>
            <td class="px-4 py-2">{{ number_format($p->gia_ban,0,',','.') }} ₫</td>
            <td class="px-4 py-2">
              {{ number_format($p->gia_nhap ?? 0,0,',','.') }} ₫
            </td>
            <td class="px-4 py-2">{{ number_format($p->gia_buon ?? 0,0,',','.') }} ₫</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- 4) Pagination --}}
  <div class="flex items-center justify-between mt-4">
    <div>
    </div>
    <div>{{ $products->links() }}</div>
  </div>
@endsection

@push('scripts')
<script>
  document.getElementById('searchBox').addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    document.querySelectorAll('tbody tr').forEach(tr => {
      // tr.cells[1] = Mã SP, tr.cells[2] = Tên SP
      const code = tr.cells[1]?.textContent.toLowerCase() || '';
      const name = tr.cells[2]?.textContent.toLowerCase() || '';
      tr.style.display = (!q || code.includes(q) || name.includes(q)) ? '' : 'none';
    });
  });
</script>
@endpush
