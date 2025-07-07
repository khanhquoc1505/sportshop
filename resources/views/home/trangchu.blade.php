<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang chủ</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <link rel="stylesheet" href="{{ asset('css/timkiem.css') }}">
  <link rel="stylesheet" href="{{ asset('css/trangchu.css') }}">
  <link rel="stylesheet" href="{{ asset('css/chitiet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/giohang.css') }}">
  <link rel="stylesheet" href="{{ asset('css/donhang.css') }}">
  <link rel="stylesheet" href="{{ asset('css/ctdonhang.css') }}">
  <link rel="stylesheet" href="{{ asset('css/thanhtoan.css') }}">
  <link rel="stylesheet" href="{{ asset('css/csthongtin.css') }}">
  <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
  
  <!-- HEADER -->
<header class="header-container">
  <div class="header-left">
    <a href="/" class="logo">
      <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="logo-image">
    </a>
  </div>
 <nav class="header-nav">
  <ul>
    {{-- QUẦN ÁO --}}
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-target="#loai-menu">
        QUẦN ÁO <span class="caret">▾</span>
      </a>
      <ul id="loai-menu" class="dropdown-menu">
        @foreach($loais as $loai)
          <li>
            <a href="{{ route('product.search', ['loai' => $loai->id]) }}">
              {{ ucfirst($loai->loai) }}
            </a>
          </li>
        @endforeach
      </ul>
    </li>

    {{-- BỘ MÔN --}}
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-target="#bomon-menu">
        BỘ MÔN <span class="caret">▾</span>
      </a>
      <ul id="bomon-menu" class="dropdown-menu">
        @foreach($bomons as $bm)
          <li>
            <a href="{{ route('product.search', ['bomon' => $bm->id]) }}">
              {{ $bm->bomon }}
            </a>
          </li>
        @endforeach
      </ul>
    </li>

    <li class="mobile-menu">
      <a href="#"><span>☰</span></a>
    </li>
  </ul>
</nav>

<!-- Search Box -->
<div class="header-search" style="position: relative;">
  <form action="{{ route('product.search') }}" method="GET" autocomplete="off">
    <input
      id="search-input"
      type="text"
      name="q"
      placeholder="Tìm kiếm sản phẩm..."
      value="{{ request('q','') }}"
      data-url="{{ route('product.autocomplete') }}"
    >
    <button type="submit">🔍</button>
  </form>
  <div id="search-suggestions" class="suggestions-box"></div>
</div>
  <div class="header-right">
  <div class="relative inline-block">
    {{-- Nút icon 👤 --}}
    <button onclick="toggleUserMenu()" class="icon-btn-user text-white px-2 py-1">
        👤
    </button>

    {{-- Dropdown menu --}}
    <div id="userDropdown" style="display: none;" class="user-dropdown">
    @auth
        <ul class="user-dropdown-list">
            @if(Auth::user()->vai_tro === 'admin')
                <li><a href="{{ url('/admin/dashboard') }}">Quản trị</a></li>
            @else
                <li><a href="{{ route('profile.index') }}">Hồ sơ</a></li>
                <li><a href="{{ route('donhang.index') }}">Đơn hàng của tôi</a></li>
            @endif
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Đăng xuất</button>
                </form>
            </li>
        </ul>
    @else
        <ul class="user-dropdown-list">
            <li><a href="{{ url('/dangnhap') }}">Đăng nhập</a></li>
        </ul>
    @endauth
</div>
  </div>
    
      @if(isset($donhang) && $donhang && $donhang->chiTiet->count())
  <a href="{{ route('cart.index') }}" class="icon-btn">
    🛒 <span class="badge">{{ $donhang->chiTiet->sum('soluong') }}</span>
  </a>
@else
  <a href="{{ route('cart.index') }}" class="icon-btn">
    🛒 <span class="badge">0</span>
  </a>
@endif

  </div>
</header>
  @yield('content')

  <footer class="footer-shop">
  {{-- PHẦN 1: Các cột thông tin --}}
  <div class="footer-top">
    <div class="footer-col">
      <h4 class="col-title">DỊCH VỤ KHÁCH HÀNG</h4>
      <ul class="col-list">
        <li><a href="#">2Q Blog</a></li>
        <li><a href="#">Hướng Dẫn Mua Hàng/Đặt Hàng</a></li>
        <li><a href="{{ route('donhang.index') }}">Đơn Hàng</a></li>
        <li><a href="{{ route('product.search') }}">Sản Phẩm</a></li>
        <li><a href="#">Trả Hàng/Hoàn Tiền</a></li>
        <li><a href="#">Chính Sách Bảo Hành</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4 class="col-title">2Q SPORT</h4>
      <ul class="col-list">
        <li><a href="#">Về Chúng tôi</a></li>
        <li><a href="#">Tuyển Dụng</a></li>
        <li><a href="#">Điều Khoản</a></li>
        <li><a href="#">Chính Sách Bảo Mật</a></li>
        <li><a href="#">Flash Sale</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4 class="col-title">THANH TOÁN</h4>
      <div class="col-logos">
        <img src="{{ asset('images/VNPAY.jpg') }}" alt="VNPAY">
      </div>

      <h4 class="col-title mt-4">ĐƠN VỊ VẬN CHUYỂN</h4>
      <div class="col-logos">
        <img src="{{ asset('images/viettel.jpg') }}" alt="Viettel Post">
      </div>
    </div>

    <div class="footer-col">
      <h4 class="col-title">THEO DÕI 2Q SPORT</h4>
      <ul class="col-list">
        <li><a href="#"><i class="fab fa-facebook-f"></i> Facebook</a></li>
        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
        <li><a href="#"><i class="fab fa-linkedin-in"></i> LinkedIn</a></li>
      </ul>
    </div>
  </div>
  {{-- NGĂN CÁCH --}}
  <div class="footer-divider"></div>
  {{-- PHẦN 2: Thông tin chung và vùng lãnh thổ --}}
  <div class="footer-info">
    <span>© 2025 2Q Sport. Tất cả các quyền được bảo lưu.</span>
    <nav class="country-list">
      <a href="#">Việt Nam</a>
    </nav>
  </div>

  <div class="footer-divider"></div>

  {{-- PHẦN 3: Chính sách --}}
  <div class="footer-policies">
    <a href="#">Chính sách bảo mật</a>
    <span class="sep">|</span>
    <a href="#">Quy chế hoạt động</a>
    <span class="sep">|</span>
    <a href="#">Chính sách vận chuyển</a>
    <span class="sep">|</span>
    <a href="#">Chính sách trả hàng và hoàn tiền</a>
  </div>

  {{-- PHẦN 4: Logo chứng nhận --}}
  <!-- <div class="footer-certifications">
    <img src="{{ asset('images/certs/bocongthuong1.png') }}" alt="Đã đăng ký Bộ Công Thương">
    <img src="{{ asset('images/certs/bocongthuong2.png') }}" alt="Đã đăng ký Bộ Công Thương">
    <img src="{{ asset('images/certs/secure-seal.png') }}" alt="Chứng nhận bảo mật">
  </div> -->

  {{-- PHẦN 5: Thông tin công ty --}}
  <div class="footer-company">
    <p>Địa chỉ: 197 NGUYỄN THƯỢNG HIỀN, PHƯỜNG 5, QUẬN BÌNH THẠNH, TP.HCM</p>
  </div>
  
</footer>
  {{-- Nút chat --}}
  <div class="chat-bubble" title="Chat với chúng tôi">💬</div>
  @include('layouts.chatbot')
  
  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/timkiem.js') }}"></script>
    <script src="{{ asset('js/trangchu.js') }}"></script>
    <script src="{{ asset('js/chitiet.js') }}"></script>
    <script src="{{ asset('js/giohang.js') }}"></script>
    <script src="{{ asset('js/ctdonhang.js') }}"></script>
    <script src="{{ asset('js/thanhtoan.js') }}"></script>
  
</body>
</html>
