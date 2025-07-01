<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title')</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="stylesheet" href="{{ asset('css/viewdangnhap.css') }}">
  <link rel="stylesheet" href="{{ asset('css/quenmatkhau.css') }}">
  <link rel="stylesheet" href="{{ asset('css/quenmk1.css') }}">
  <link rel="stylesheet" href="{{ asset('css/quenmk2.css') }}">
</head>
<body>
  <header class="login-header">
    <div class="header-left">
    <a href="/" class="home-icon">
    <i class="fas fa-home"></i>
  </a>
</div>
  <div class="header-center">
    <a href="/" class="logo-home">
      <img src="{{ asset('images/logo.jpg') }}" alt="Home">
    </a>
  </div>
  <div class="header-right"></div> <!-- để giữ bố cục đối xứng -->
</header>

  <main class="login-container">
    <div class="login-left">
      <img src="{{ asset('https://bizweb.dktcdn.net/100/455/994/products/frame-8565.jpg?v=1679020910940') }}" alt="Banner thể thao">
    </div>

    <div class="login-right">
      <div class="login-toggle">
        <a href="{{ url('/dangnhap') }}" class="{{ request()->is('dangnhap') ? 'active' : '' }}">Đăng nhập</a>
        <a href="{{ url('/dangky') }}" class="{{ request()->is('dangky') ? 'active' : '' }}">Đăng ký</a>
      </div>
    
      @yield('content')
      
    </div>
  </main>
  <script src="{{ asset('js/quenmatkhau.js') }}"></script>
</body>
</html>
