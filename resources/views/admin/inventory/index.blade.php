{{-- resources/views/admin/inventory/index.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Quản lý kho</h1>

  {{-- 1) Toolbar: xuất, chuyển qua danh sách sản phẩm --}}
  <div class="flex items-center justify-between mb-4">
    <div class="flex space-x-2">
      <button class="px-3 py-1 border rounded hover:bg-gray-100">In tem mẫu</button>
      <button class="px-3 py-1 border rounded hover:bg-gray-100">Quản lý khác</button>
    </div>
    <a href="{{ route('admin.inventory.index') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
      Xem danh sách sản phẩm
    </a>
  </div>

  {{-- 2) Search & Filters --}}
  <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex items-center space-x-2 mb-4"></form>
  <div class="bg-white rounded-lg shadow p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <input type="text" name="search" id="searchBox"
             value="{{ $search ?? '' }}"
             placeholder="Tìm theo tên sản phẩm, barcode"
             class="col-span-1 md:col-span-2 px-4 py-2 border rounded-full focus:ring focus:outline-none" />

      <select name="type" class="px-4 py-2 border rounded-lg">
    <option value="">-- Loại sản phẩm --</option>
    <option value="Áo"    {{ ($type ?? '')==='Áo'    ? 'selected':'' }}>Áo</option>
    <option value="Quần"  {{ ($type ?? '')==='Quần'  ? 'selected':'' }}>Quần</option>
    {{-- thêm option khác nếu có --}}
  </select>
      <button class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition">
        Lưu bộ lọc
      </button>
    </div>
  </div>
  </form>

  {{-- 3) Table --}}
  <div class="bg-white rounded-lg shadow overflow-auto">
    <table id="itemsTable" class="min-w-full table-auto">
      <thead>
        <tr class="bg-gray-100 text-gray-700 text-center">
          <th class="px-4 py-2"><input type="checkbox" id="checkAll" /></th>
          <th class="px-4 py-2">Ảnh</th>
          <th class="px-4 py-2">Tên phiên bản sản phẩm</th>
          <th class="px-4 py-2">Có thể bán</th>
          <th class="px-4 py-2">Tồn kho</th>
          <th class="px-4 py-2">Ngày khởi tạo</th>
          <th class="px-4 py-2">Giá bán lẻ</th>
          <th class="px-4 py-2">Giá nhập</th>
          <th class="px-4 py-2">Giá bán buôn</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
          <tr class="border-b hover:bg-gray-50 text-center">
            <td class="px-4 py-2">
              <input type="checkbox" class="row-check" />
            </td>
            <td class="px-4 py-2">
              <img src="{{ $item['image_url'] ?? 'https://via.placeholder.com/50' }}"
                   alt="" class="h-10 w-10 object-cover mx-auto rounded" />
            </td>
            <td class="px-4 py-2 text-left">{{ $item['name'] }}</td>
            <td class="px-4 py-2">{{ $item['sellable'] ?? '–' }}</td>
            <td class="px-4 py-2">{{ $item['stock'] ?? $item['quantity'] ?? 0 }}</td>
            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y') }}</td>
            <td class="px-4 py-2">{{ number_format($item['price_retail'] ?? 0,0,',','.') }} ₫</td>
            <td class="px-4 py-2">{{ number_format($item['price_import'] ?? 0,0,',','.') }} ₫</td>
            <td class="px-4 py-2">{{ number_format($item['price_wholesale'] ?? 0,0,',','.') }} ₫</td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="py-6 text-gray-500">Chưa có sản phẩm nào</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- 4) Pagination & Per-page --}}
  <div class="flex items-center justify-between mt-4">
    <div class="flex items-center space-x-2">
      <span>Hiển thị</span>
      <select id="perPage" class="px-2 py-1 border rounded">
        <option {{ request('perPage')==10?'selected':'' }}>10</option>
        <option {{ request('perPage')==20?'selected':'' }}>20</option>
        <option {{ request('perPage')==50?'selected':'' }}>50</option>
      </select>
      <span>kết quả</span>
    </div>

    <div class="text-gray-600">
      Từ {{ $items->firstItem() ?? 0 }} đến {{ $items->lastItem() ?? 0 }} trên tổng {{ $items->total() }} kết quả
    </div>

    <div>
      {{ $items->links() }}
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // Check/uncheck all
  document.getElementById('checkAll').addEventListener('change', function(){
    document.querySelectorAll('.row-check')
      .forEach(ch => ch.checked = this.checked);
  });

  // Live-search (chỉ demo front-end)
  document.getElementById('searchBox').addEventListener('input', function(){
    const q = this.value.trim().toLowerCase();
    document.querySelectorAll('#itemsTable tbody tr').forEach(tr => {
      const name = tr.querySelector('td:nth-child(3)').textContent.toLowerCase();
      const id   = tr.querySelector('td:nth-child(1) .row-check')?.closest('tr')
                   .querySelector('td:nth-child(3)').textContent; // or adjust if needed
      tr.style.display = name.includes(q) ? '' : 'none';
    });
  });

  // Thay đổi per-page
  document.getElementById('perPage').addEventListener('change', function(){
    const params = new URLSearchParams(location.search);
    params.set('perPage', this.value);
    location.search = params.toString();
  });
</script>
@endpush
