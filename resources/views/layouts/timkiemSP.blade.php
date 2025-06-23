@extends('home.trangchu')
@section('title', 'Kết quả tìm kiếm')
@section('content')



<div class="search-page-container">
  <!-- Sidebar filter -->
  <aside class="search-sidebar">
    <h3>Bộ lọc</h3>
    <button class="clear-filters" onclick="window.location='{{ route('product.search') }}'">
      Xóa bộ lọc
    </button>

    <div class="filter-section">
      <div class="filter-title" onclick="toggleFilter(this)">
        Loại sản phẩm <span>+</span>
      </div>
      <div class="filter-options">
        @foreach($loais as $l)
          <a href="{{ route('product.search', array_merge(request()->query(), ['loai'=>$l->id])) }}">
            {{ ucfirst($l->loai) }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="filter-section">
      <div class="filter-title" onclick="toggleFilter(this)">
        Bộ môn <span>+</span>
      </div>
      <div class="filter-options">
        @foreach($bomons as $b)
          <a href="{{ route('product.search', array_merge(request()->query(), ['bomon'=>$b->id])) }}">
            {{ $b->bomon }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="filter-section">
      <div class="filter-title" onclick="toggleFilter(this)">
        Tìm kiếm <span>+</span>
      </div>
      <div class="filter-options">
        <form action="{{ route('product.search') }}" method="GET">
          <input type="text" name="q" placeholder="Nhập từ khoá..." value="{{ request('q','') }}">
          @foreach(request()->except('q','page') as $key=>$val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
          @endforeach
          <button type="submit">🔍 Tìm</button>
        </form>
      </div>
    </div>
  </aside>

  <!-- Product display -->
  <main class="search-results">
    <div class="results-header">
      <div class="sort-controls">
        <label>Sắp xếp:</label>
        <select onchange="location = this.value;">
          <option value="{{ request()->fullUrlWithQuery([]) }}">Mặc định</option>
          <option value="{{ request()->fullUrlWithQuery(['sort'=>'price_asc']) }}">Giá thấp→cao</option>
          <option value="{{ request()->fullUrlWithQuery(['sort'=>'price_desc']) }}">Giá cao→thấp</option>
          <option value="{{ request()->fullUrlWithQuery(['sort'=>'name_asc']) }}">Tên A→Z</option>
          <option value="{{ request()->fullUrlWithQuery(['sort'=>'name_desc']) }}">Tên Z→A</option>
        </select>
      </div>
    </div>

    @if($products->count())
      <div class="product-grid">
        @foreach($products as $product)
          @php
            $file = optional($product->avatarImage)->image_path ?? 'default.jpg';
            $img  = asset('images/'.$file);
          @endphp
          <a href="{{ route('product.show',$product) }}" class="product-card">
            <div class="card-image">
              <img src="{{ $img }}" alt="{{ $product->ten }}">
            </div>
            <div class="card-body">
              <h3 class="card-title">{{ Str::limit($product->ten, 50) }}</h3>
              <div class="card-price">{{ number_format($product->gia_ban,0,',','.') }} đ</div>
            </div>
          </a>
        @endforeach
      </div>

      <div class="pagination-wrapper">
  @if(method_exists($products, 'previousPageUrl') && $products->previousPageUrl())
    <a href="{{ $products->previousPageUrl() }}" class="page-link">‹ Trước</a>
  @endif
  @if(method_exists($products, 'nextPageUrl') && $products->nextPageUrl())
    <a href="{{ $products->nextPageUrl() }}" class="page-link">Tiếp ›</a>
  @endif
</div>
</div>
    @else
      <p class="no-results">Không tìm thấy sản phẩm nào phù hợp.</p>
    @endif
  </main>
</div>


@endsection
