@extends('home.trangchu')
@section('title', 'Trang chủ')
@section('content')
  

  <section class="banner-carousel">
    <div class="swiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <img src="https://cmsv2.yame.vn/uploads/36aa2b76-21be-4fa4-b481-c8162834ed91/thoi_trang_mac_suong_gia_that_tot.jpg?quality=80&w=1280&h=0" alt="Banner 1">
        </div>
        <div class="swiper-slide">
          <img src="https://cmsv2.yame.vn/uploads/35c8ca09-b3c1-4c6d-bac3-3c8cbe3895aa/KHUY%e1%ba%beN_M%c3%83I.jpg?quality=80&w=1920&h=0" alt="Banner 2">
        </div>
        <div class="swiper-slide">
          <img src="https://cmsv2.yame.vn/uploads/06dddfd8-85a3-46c2-8228-e3a7161ed211/quan_ao_the_thao.jpg?quality=80&w=0&h=0" alt="Banner 3">
        </div>
      </div>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>
  </section>

  <main class="product-grid">
  @forelse($products as $product)
    @php
      // Lấy file ảnh hoặc fallback
      $file = optional($product->avatarImage)->image_path ?? 'default.jpg';
      $imgPath = asset('images/' . $file);
    @endphp

    <a href="{{ route('product.show', $product) }}" class="product-card">
      <img src="{{ $imgPath }}" alt="{{ $product->ten }}" class="product-card-img">
      <div class="product-info">
        <h3 class="product-title">{{ $product->ten }}</h3>
        <p class="product-price">{{ number_format($product->gia_ban,0,',','.') }} đ</p>
      </div>
    </a>
  @empty
    <p>Chưa có sản phẩm nào.</p>
  @endforelse
</main>

<!-- HTML Tabs + Product Slider -->
<div class="tab-product-container">
  @foreach($bomons as $i => $bomon)
    <button
      class="tab-btn {{ $i === 0 ? 'active' : '' }}"
      data-tab="{{ Str::slug($bomon->bomon, '_') }}"
    >
      {{ $bomon->bomon }}
    </button>
  @endforeach
</div>

<div class="tab-product-wrapper">
  @foreach($bomons as $i => $bomon)
    <div
      id="{{ Str::slug($bomon->bomon, '_') }}"
      class="tab-product-content {{ $i === 0 ? 'active' : '' }}"
    >
      @forelse($bomon->sanPhams as $product)
        @php
          // Lấy ảnh đại diện variant đầu tiên, fallback ảnh default nếu null
          $avatar = optional($product->avatarImage)->image_path;
          $imgUrl = $avatar
            ? asset('images/' . $avatar)
            : asset('images/default.jpg');
        @endphp

        <div class="tab-product-card">
          <img
            src="{{ $imgUrl }}"
            alt="{{ $product->ten }}"
          >
          <h4>{{ $product->ten }}</h4>
          <div class="tab-product-price">
            {{ number_format($product->gia_ban,0,',','.') }}đ
          </div>
        </div>
      @empty
        <p>Chưa có sản phẩm nào.</p>
      @endforelse
    </div>
  @endforeach
</div>

@endsection