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
</head>
<body>
  <header class="header-container">
    <div class="header-left">
      <a href="/" class="logo-wrapper">
        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="logo-image">
      </a>
    </div>
    <div class="header-center">
      <div class="search-box">
        <span class="search-icon">🌐</span>
        <input type="text" placeholder="Tìm kiếm" class="search-input">
        <button class="search-button">🔍</button>
      </div>
    </div>
    <div class="header-right">
      <a href="/user" class="icon-link">👤</a>
      <a href="/cart" class="icon-link">🛒</a>
      <a href="/help" class="icon-link help">❓</a>
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
  
</body>
</html>
