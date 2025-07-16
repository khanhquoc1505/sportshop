@extends('home.trangchu')
@section('title', $product->ten)
@section('content')
@php
    // Chuẩn bị mảng biến thể thuần PHP
    $variants = $product->variants
        ->map(fn($v) => [
            'color_id' => $v->mauSac->id,
            'size'     => $v->kichCo->size,
            'stock'    => $v->sl,
        ])
        ->values()
        ->toArray();
@endphp

<div class="ct-product-tong">
  <div class="ct-product-detail">
    <div class="ct-product-detail-container">
      {{-- Thumbnails --}}
      <div class="ct-thumbnail-list">
  @foreach($allColorImages as $img)
    <img 
      class="thumbnail" 
      src="{{ $img['url'] }}" 
      data-color-id="{{ $img['mausac_id'] }}" 
      alt="{{ pathinfo($img['image_path'], PATHINFO_FILENAME) }}"
    >
  @endforeach
</div>
      {{-- Main image: lấy ảnh đầu --}}
      <div class="ct-main-image">
  <img 
    id="main-image" 
    src="{{ $colorVariants[0]['image_url'] }}" 
    alt="{{ $product->slug}}"
  >
</div>
    </div>

    <div class="ct-product-info">
      <h2 class="ct-product-title">{{ $product->ten }}</h2>
      <div class="ct-product-meta">
        <p><strong>Mã sản phẩm:</strong> {{ $product->masanpham }}</p>
        <p><strong>Trạng thái:</strong>
          {{ $inStock ? 'Còn hàng' : 'Hết hàng' }}
        </p>
      </div>
      <div class="ct-product-price">
        {{ number_format($product->gia_ban,0,',','.') }} VNĐ
      </div>

      <div class="ct-product-options">
        {{-- Màu --}}
        <label>Màu</label>
        <div class="ct-color-options">
  @foreach($colorVariants as $i => $c)
    <div 
      class="color-swatch {{ $i===0?'active':'' }}"
      data-color-id="{{ $c['mausac_id'] }}"
      data-image="{{ $c['image_url'] }}"
      title="{{ $c['mausac'] }}"
    >
      {{ $c['mausac'] }}
    </div>
  @endforeach
</div>

        {{-- Size --}}
        <label>SIZE</label>
        <div class="ct-size-options">
          @foreach($sizes as $size)
            <button class="size-btn" data-size="{{ $size->size }}">{{ $size->size }}</button>
          @endforeach
        </div>
      </div>

      <div class="ct-product-quantity">
        <button id="qty-decrease">-</button>
        <input id="qty" type="text" value="1" readonly>
        <button id="qty-increase">+</button>
      </div>

      <div class="ct-action-buttons">
  {{-- Thêm vào giỏ hàng --}}
  <form action="{{ route('cart.them', $product) }}" method="POST">
    @csrf
    <input type="hidden" name="quantity" id="form-qty" value="1">
    <input type="hidden" name="size" id="selected-size" value="">
    <input type="hidden" id="selected-color" name="mausac"
           value="{{ $colorVariants[0]['mausac_id'] }}">
    <input type="hidden" id="selected-image" name="hinh_anh"
           value="{{ basename($colorVariants[0]['image_url']) }}">
    <button
      type="submit"
      class="ct-add-to-cart"
      @if(! $inStock) disabled style="opacity:0.5;cursor:not-allowed;" @endif
    >
      {{ $inStock ? 'Thêm vào giỏ hàng' : 'Hết hàng' }}
    </button>
  </form>

  {{-- Mua ngay --}}
  <form id="ct-buy-now-form" action="{{ route('cart.buynow') }}" method="POST">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="quantity" id="form-qty-buynow" value="1">
    <input type="hidden" name="size" id="selected-size-buynow" value="">
    <input type="hidden" name="mausac" id="selected-color-buynow"
           value="{{ $colorVariants[0]['mausac_id'] }}">
    <button
      type="button"
      id="ct-buy-now-btn"
      @if(! $inStock) disabled style="opacity:0.5;cursor:not-allowed;" @endif
    >
      Mua ngay
    </button>
  </form>

  {{-- Yêu thích thì vẫn cho phép --}}
  <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
    @csrf
    <button class="ct-add-to-yt">Yêu thích</button>
  </form>
</div>
    </div>
  </div>

  {{-- Tabs: mô tả, đánh giá, danh sách đánh giá --}}
  <div class="ct-product-detail-tabs">
    <div class="ct-tab-buttons">
      <button class="ct-tab-btn active" onclick="showTab('details')">CHI TIẾT SẢN PHẨM</button>
      <button class="ct-tab-btn" onclick="showTab('danhsach')">DANH SÁCH ĐÁNH GIÁ</button>
    </div>

    <div id="details" class="ct-tab-content active">
      {!! $product->mo_ta !!}
    </div>
    <div id="danhsach" class="ct-tab-content">
  <h3>Đánh giá gần đây</h3>

  @forelse($product->danhGias as $dg)
    <div class="ct-review-item">
      <div class="ct-review-header">
        <span class="ct-review-name">{{ $dg->user->ten_nguoi_dung ?? 'Khách' }}</span>
        <span class="ct-stars">{{ str_repeat('★', $dg->sosao) }}</span>
        <span class="ct-review-date">{{ optional($dg->created_at)->format('d/m/Y') }}</span>
      </div>

      <div class="ct-review-text">{{ $dg->noi_dung }}</div>

      @if(!empty($dg->hinh_anh) && is_array($dg->hinh_anh))
        <div class="ct-review-images">
          @foreach($dg->hinh_anh as $img)
            <img src="{{ asset('images/'.$img) }}"
                 alt="Review image"
                 class="ct-review-image">
          @endforeach
        </div>
      @endif

      {{-- Hiển thị reply nếu đã có --}}
      @if(!empty($dg->is_replied) && $dg->is_replied)
        <div class="ct-review-reply">
          <div class="ct-review-reply-label">Phản hồi từ cửa hàng:</div>
          <div class="ct-review-reply-text">{{ $dg->reply }}</div>
        </div>
      @endif
    </div>
  @empty
    <p>Chưa có đánh giá nào.</p>
  @endforelse
</div>
  </div>

  {{-- Sản phẩm liên quan --}}
  <div class="ct-lienquan">Sản phẩm liên quan</div>
<div class="ct-related-products">
  @forelse($related as $item)
    @php
      // nếu avatarImage trả về null thì dùng default.jpg
      $avatar = optional($item->avatarImage)->hinh_anh ?? 'default.jpg';
    @endphp

    <div class="ct-related-product-card">
      <a href="{{ route('product.show', $item->id) }}">
        <img 
          src="{{ asset('images/' . $avatar) }}" 
          alt="{{ $item->slug }}"
        >
        <div class="ct-related-info">
          <p>{{ $item->ten }}</p>
          <strong>{{ number_format($item->gia_ban, 0, ',', '.') }}đ</strong>
        </div>
      </a>
    </div>
  @empty
    <p>Không có sản phẩm liên quan.</p>
  @endforelse

</div>
<script>
  window.productVariants = @json($variants);
</script>

@endsection
