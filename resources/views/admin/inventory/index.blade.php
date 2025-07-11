{{-- resources/views/admin/inventory/index.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Quản lý kho</h1>

  {{-- Search & Filter --}}
  <div class="flex items-center space-x-2 mb-4">
    <input
      type="text"
      id="searchBox"
      placeholder="Tìm theo tên hoặc mã SP"
      value="{{ $search ?? '' }}"
      class="px-4 py-2 border rounded focus:outline-none focus:ring w-1/3"
    />

    <select
      id="typeSelect"
      class="px-4 py-2 border rounded-lg"
    >
      <option value="">-- Loại sản phẩm --</option>
      @foreach($dsLoai as $loaiItem)
        <option value="{{ $loaiItem }}"
          {{ ($type ?? '') === $loaiItem ? 'selected' : '' }}>
          {{ $loaiItem }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Container sẽ được JS cập nhật lại --}}
  <div id="tableContainer">
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
              <td class="px-4 py-2">{{ number_format($p->tong_ton ?? 0) }}</td>
              <td class="px-4 py-2">{{ \Carbon\Carbon::parse($p->thoi_gian_them)->format('d/m/Y') }}</td>
              <td class="px-4 py-2">{{ number_format($p->gia_ban,0,',','.') }} ₫</td>
              <td class="px-4 py-2">{{ number_format($p->gia_nhap ?? 0,0,',','.') }} ₫</td>
              <td class="px-4 py-2">{{ number_format($p->gia_buon ?? 0,0,',','.') }} ₫</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between mt-4">
      <div></div>
      <div>{{ $products->links() }}</div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  const searchBox    = document.getElementById('searchBox');
  const typeSelect   = document.getElementById('typeSelect');
  const tableContainer = document.getElementById('tableContainer');

  // debounce để không spam request
  function debounce(fn, ms = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), ms);
    };
  }

  // fetch trang với query params, parse và cập nhật lại #tableContainer
  async function updateTable(url = null) {
    // nếu không truyền url (pagination), build từ input/select
    if (!url) {
      const params = new URLSearchParams({
        search: searchBox.value,
        type:   typeSelect.value
      });
      url = `?${params.toString()}`;
    }

    const res  = await fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} });
    const text = await res.text();
    // parse HTML
    const doc = new DOMParser().parseFromString(text, 'text/html');
    const newContainer = doc.getElementById('tableContainer');
    if (newContainer) {
      tableContainer.innerHTML = newContainer.innerHTML;
    }
  }

  // gán sự kiện
  searchBox.addEventListener('input',  () => updateTable());
  typeSelect.addEventListener('change', () => updateTable());

  // pagination link delegation
  tableContainer.addEventListener('click', e => {
    const a = e.target.closest('.pagination a');
    if (!a) return;
    e.preventDefault();
    updateTable(a.href);
  });
</script>
@endpush
