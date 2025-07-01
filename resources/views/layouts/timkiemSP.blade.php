@extends('home.trangchu')
@section('title', 'Kết quả tìm kiếm')
@section('content')

<div class="search-page-container">

  {{-- 1) Sidebar filter (bên trái) --}}
  <aside class="search-sidebar">
    <h3>Bộ lọc</h3>
    <button class="clear-filters"
            onclick="window.location='{{ route('product.search') }}'">
      Xóa bộ lọc
    </button>

    {{-- Lọc theo Loại --}}
    <div class="filter-section">
      <div class="filter-title" onclick="toggleFilter(this)">
        Loại sản phẩm <span>+</span>
      </div>
      <div class="filter-options">
        @foreach($loais as $l)
          <a href="{{ route('product.search',
                    array_merge(request()->query(), ['loai'=>$l->id])) }}"
             class="{{ request('loai')==$l->id?'active':'' }}">
            {{ ucfirst($l->loai) }}
          </a>
        @endforeach
      </div>
    </div>

    {{-- Lọc theo Bộ môn --}}
    <div class="filter-section">
      <div class="filter-title" onclick="toggleFilter(this)">
        Bộ môn <span>+</span>
      </div>
      <div class="filter-options">
        @foreach($bomons as $b)
          <a href="{{ route('product.search',
                    array_merge(request()->query(), ['bomon'=>$b->id])) }}"
             class="{{ request('bomon')==$b->id?'active':'' }}">
            {{ $b->bomon }}
          </a>
        @endforeach
      </div>
    </div>

    {{-- Tìm kiếm từ khoá --}}
    <div class="filter-section">
      <div class="filter-title" onclick="toggleFilter(this)">
        Tìm kiếm <span>+</span>
      </div>
      <div class="filter-options">
        <form action="{{ route('product.search') }}" method="GET">
          <input type="text"
                 name="q"
                 placeholder="Nhập từ khoá..."
                 value="{{ request('q','') }}">
          @foreach(request()->except(['q','page']) as $key => $val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
          @endforeach
          <button type="submit">🔍 Tìm</button>
        </form>
      </div>
    </div>
  </aside>

  {{-- 2) Khu vực kết quả (bên phải) --}}
  <main class="search-results">

    {{-- 2.1) Thanh Sắp xếp --}}
    <div class="results-header">
      <form method="GET" action="{{ route('product.search') }}" class="sort-form">
        {{-- Giữ lại tất cả filter và từ khoá --}}
        @foreach(request()->except(['page','sort']) as $key => $val)
          <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endforeach

        <label for="sort">Sắp xếp:</label>
        <select name="sort" id="sort" onchange="this.form.submit()">
          <option value="default"  {{ request('sort','default')=='default'?'selected':'' }}>
            Mặc định
          </option>
          <option value="price_asc"  {{ request('sort')=='price_asc'?'selected':'' }}>
            Giá thấp → cao
          </option>
          <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>
            Giá cao → thấp
          </option>
          <option value="name_asc"   {{ request('sort')=='name_asc'?'selected':'' }}>
            Tên A → Z
          </option>
          <option value="name_desc"  {{ request('sort')=='name_desc'?'selected':'' }}>
            Tên Z → A
          </option>
        </select>
      </form>
    </div>

    {{-- 2.2) Grid sản phẩm --}}
    @if($products->count())
      <div class="product-grid">
        @foreach($products as $product)
          @php
            $file = optional($product->avatarImage)->image_path ?? 'default.jpg';
            $img  = asset('images/'.$file);
          @endphp
          <a href="{{ route('product.show', $product) }}" class="product-card">
            <div class="card-image">
              <img src="{{ $img }}" alt="{{ $product->ten }}">
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ Str::limit($product->ten, 60) }}</h3>
              <div class="card-price">
                {{ number_format($product->gia_ban,0,',','.') }} đ
              </div>
            </div>
          </a>
        @endforeach
      </div>

      {{-- 2.3) Phân trang --}}
      <div class="pagination-wrapper">
        @if($products->previousPageUrl())
          <a href="{{ $products->previousPageUrl() }}" class="page-link">‹ Trước</a>
        @endif
        @if($products->nextPageUrl())
          <a href="{{ $products->nextPageUrl() }}" class="page-link">Tiếp ›</a>
        @endif
      </div>
    @else
      <p class="no-results">Không tìm thấy sản phẩm nào phù hợp.</p>
    @endif

  </main>
</div>

{{-- JS helper để toggle filter --}}
<script>
function toggleFilter(el) {
  const opts = el.nextElementSibling;
  const span = el.querySelector('span');
  if (opts.style.display === 'block') {
    opts.style.display = 'none';
    span.textContent = '+';
  } else {
    opts.style.display = 'block';
    span.textContent = '–';
  }
}
</script>

@endsection
