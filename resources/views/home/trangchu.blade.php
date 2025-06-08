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
      <li class="dropdown">
        <a href="#">KHUYẾN MÃI <span class="caret">▾</span></a>
        <ul class="dropdown-menu">
          <li><a href="#">Giảm 10%</a></li>
          <li><a href="#">Flash Sale</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#">QUẦN ÁO <span class="caret">▾</span></a>
        <ul class="dropdown-menu">
          <li><a href="{{ url('layouts/timkiemSP?loai=aothun') }}">Áo Thun</a></li>
          <li><a href="{{ url('layouts/timkiemSP?loai=aokhoac') }}">Áo Khoác</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#">GIÀY <span class="caret">▾</span></a>
        <ul class="dropdown-menu">
          <li><a href="{{ url('layouts/timkiemSP?loai=giay_thethao') }}">Giày Thể Thao</a></li>
          <li><a href="{{ url('layouts/timkiemSP?loai=giay_luoi') }}">Giày Lười</a></li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="#">HÃNG <span class="caret">▾</span></a>
        <ul class="dropdown-menu">
          <li><a href="{{ url('layouts/timkiemSP?loai=balo') }}">ADIDAS</a></li>
          <li><a href="{{ url('layouts/timkiemSP?loai=mu') }}">NIKE</a></li>
        </ul>
      </li>
      <li class="mobile-menu">
        <a href="#"><span>☰</span></a>
      </li>
    </ul>
  </nav>
<!-- Search Box ở giữa -->
  <div class="header-search">
    <form action="{{ url('layouts/timkiemSP') }}" method="GET" class="search-form">
      <input
        type="text"
        name="q"
        class="search-input"
        placeholder="Tìm kiếm sản phẩm..."
        value="{{ request('q') ?? '' }}"
      />
      <button type="submit" class="search-btn" aria-label="Search">🔍</button>
    </form>
  </div>
  <div class="header-right">
    <a href="{{ url('/dangnhap') }}" class="icon-btn" aria-label="User">👤</a>
    <a href="{{ url('/giohang') }}" class="icon-btn" aria-label="Cart">
      🛒<span class="badge">1</span>
    </a>
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
  
</body>
</html>
