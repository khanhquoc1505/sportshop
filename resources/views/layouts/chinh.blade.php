@extends('home.trangchu')
@section('title', 'Trang chủ')
@section('content')
  <nav class="section-deals-tabs">
    <a href="{{ url('layouts/timkiemSP?loai=ao') }}"><button>Áo</button></a>
    <a href="{{ url('layouts/timkiemSP?loai=quan') }}"><button>Quần</button></a>
    <a href="{{ url('layouts/timkiemSP?loai=giay') }}"><button>Giày</button></a>
    <a href="{{ url('layouts/timkiemSP?loai=phukien') }}"><button>Phụ kiện</button></a>
    <a href="{{ url('layouts/timkiemSP?loai=khac') }}"><button>Khác</button></a>
  </nav>

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
    @for ($i = 0; $i < 10; $i++)
      <a href="layouts/chitiet" class="product-card">
      <img src="https://cdn2.yame.vn/pimg/quan-jogger-cool-touch-03-0022614/149711e8-95f4-9d00-0189-001c69a79628.jpg?w=540&h=756&c=true&v=052025" alt="Product">
      <div class="product-info">
        <div class="product-title">Product Name</div>
        <div>$0</div>
      </div>
    </a>
    @endfor
  </main>

<!-- HTML Tabs + Product Slider -->
<div class="tab-product-container">
  <button class="tab-btn active" data-tab="adidas">adidas</button>
  <button class="tab-btn" data-tab="nike">nike</button>
  <button class="tab-btn" data-tab="Puma">Puma</button>
  <button class="tab-btn" data-tab="Converse">Converse</button>
  <button class="tab-btn" data-tab="Fila">Fila</button>
</div>
<div class="tab-product-wrapper">
  <div id="adidas" class="tab-product-content active">
    <div class="tab-product-card">
      <img src="LINK_VGA_IMG1" alt="">
      <h4>tên</h4>
      <div class="tab-product-price">7,500,000đ <span class="tab-product-old-price">7,550,000đ</span></div>
    </div>
    <div class="tab-product-card">
      <img src="LINK_VGA_IMG1" alt="">
      <h4>tên</h4>
      <div class="tab-product-price">7,500,000đ <span class="tab-product-old-price">7,550,000đ</span></div>
    </div>
  </div>
  <div id="nike" class="tab-product-content">
    <div class="tab-product-card">
      <img src="LINK_CPU_IMG1" alt="">
      <h4>tên</h4>
      <div class="tab-product-price">4,200,000đ</div>
    </div>
  </div>
  <div id="Puma" class="tab-product-content">
    <div class="tab-product-card">
      <img src="LINK_MB_IMG1" alt="">
      <h4>tên</h4>
      <div class="tab-product-price">3,350,000đ</div>
    </div>
  </div>
  <div id="Converse" class="tab-product-content">
    <div class="tab-product-card">
      <img src="LINK_RAM_IMG1" alt="">
      <h4>tên</h4>
      <div class="tab-product-price">1,150,000đ</div>
    </div>
  </div>

  <div id="Fila" class="tab-product-content">
    <div class="tab-product-card">
      <img src="LINK_SSD_IMG1" alt="">
      <h4>Tên</h4>
      <div class="tab-product-price">1,550,000đ</div>
    </div>
  </div>
</div>
@endsection