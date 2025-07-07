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
<h1 class="text-3xl font-semibold mb-6">Quản lý sản phẩm</h1>

{{-- Tìm kiếm & bộ lọc --}}
<div class="flex items-center space-x-4 mb-4">
  <input type="text" id="searchInput" placeholder="Tìm tên sản phẩm..."
         class="px-4 py-2 border rounded focus:outline-none focus:ring w-1/3" />

  <select id="filterSize" class="px-3 py-2 border rounded">
    <option value="">-- Size --</option>
    @foreach(\App\Models\KichCo::all() as $kc)
      <option value="{{ $kc->id }}">{{ $kc->size }}</option>
    @endforeach
  </select>

  <select id="filterColor" class="px-3 py-2 border rounded">
    <option value="">-- Màu sắc --</option>
    @foreach(\App\Models\MauSac::all() as $m)
      <option value="{{ $m->id }}">{{ $m->mausac }}</option>
    @endforeach
  </select>

  <a href="{{ route('admin.product.create') }}"
  class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
  Thêm sản phẩm
  </a>  
</div>


{{-- Danh sách biến thể --}}
<div id="variantTable">
  @include('admin.product._table', ['variants' => $variants])
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const sizeSelect = document.getElementById("filterSize");
  const colorSelect = document.getElementById("filterColor");

  const fetchVariants = () => {
    const search = searchInput.value;
    const size = sizeSelect.value;
    const color = colorSelect.value;

    const url = new URL("{{ route('admin.product.index') }}");
    url.searchParams.append("search", search);
    url.searchParams.append("size", size);
    url.searchParams.append("color", color);

    fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
      .then(res => res.text())
      .then(html => {
        document.getElementById("variantTable").innerHTML = html;
      });
  };

  let timeout;
  searchInput.addEventListener("input", () => {
    clearTimeout(timeout);
    timeout = setTimeout(fetchVariants, 300);
  });

  sizeSelect.addEventListener("change", fetchVariants);
  colorSelect.addEventListener("change", fetchVariants);
});
</script>
@endpush
