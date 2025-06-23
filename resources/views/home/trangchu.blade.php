<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang chủ</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
  <link rel="stylesheet" href="{{ asset('css/timkiem.css') }}">
  <link rel="stylesheet" href="{{ asset('css/trangchu.css') }}">
  <link rel="stylesheet" href="{{ asset('css/chitiet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/giohang.css') }}">
  <link rel="stylesheet" href="{{ asset('css/donhang.css') }}">
  <link rel="stylesheet" href="{{ asset('css/ctdonhang.css') }}">
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
<div class="header-search">
  <form action="{{ route('product.search') }}" method="GET" class="search-form">
    <input
      type="text"
      name="q"
      placeholder="Tìm kiếm sản phẩm..."
      value="{{ request('q','') }}"
    >
    <button type="submit">🔍</button>
  </form>
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

  <footer class="footer-grid">
    <div class="footer-section">
      <h4>Use cases</h4>
      <ul>
        <li>UI design</li>
        <li>UX design</li>
        <li>Wireframing</li>
      </ul>
    </div>
    <div class="footer-section">
      <h4>Explore</h4>
      <ul>
        <li>Design</li>
        <li>Prototyping</li>
        <li>Design systems</li>
      </ul>
    </div>
    <div class="footer-section">
      <h4>Resources</h4>
      <ul>
        <li>Blog</li>
        <li>Best practices</li>
        <li>Support</li>
      </ul>
    </div>
    <div class="footer-section">
      <h4>Theo dõi</h4>
      <ul>
        <li>Facebook</li>
        <li>Instagram</li>
        <li>LinkedIn</li>
      </ul>
    </div>
  </footer>

  <div class="chat-bubble" title="Chat với chúng tôi">
    💬
  </div>

  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/timkiem.js') }}"></script>
    <script src="{{ asset('js/trangchu.js') }}"></script>
    <script src="{{ asset('js/chitiet.js') }}"></script>
    <script src="{{ asset('js/giohang.js') }}"></script>
    <script src="{{ asset('js/ctdonhang.js') }}"></script>
  
</body>
</html>
