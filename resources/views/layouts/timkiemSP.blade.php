@extends('home.trangchu')
@section('title', 'timkiem')
@section('content')
  <div class="container">
    <!-- Sidebar filter -->
    <aside class="sidebar">
      <h3>Bộ lọc đã dùng</h3>
      <a href="#" style="color: red; font-size: 14px; display: block; margin-bottom: 1rem;">Xóa bộ lọc</a>

      <div class="filter-section">
        <div class="filter-title" onclick="toggleFilter(this)">
          Thương hiệu <span>+</span>
        </div>
        <div class="filter-options">
          <a href="?cpu=i5">Nike</a>
          <a href="?cpu=i7">Adidas</a>
          <a href="?cpu=ryzen5">Puma</a>
          <a href="?cpu=i7">Fila</a>
          <a href="?cpu=ryzen5">Converse</a>
        </div>
      </div>

      <div class="filter-section">
        <div class="filter-title" onclick="toggleFilter(this)">
          Loại sản phẩm <span>+</span>
        </div>
        <div class="filter-options">
          <a href="?ram=8gb">Quần</a>
          <a href="?ram=16gb">Áo</a>
          <a href="?ram=32gb">Giày</a>
        </div>
      </div>

      <div class="filter-section">
        <div class="filter-title" onclick="toggleFilter(this)">
          Size <span>+</span>
        </div>
        <div class="filter-options">
          <a href="?ssd=256gb">S</a>
          <a href="?ssd=512gb">M</a>
          <a href="?ssd=1tb">L</a>
        </div>
      </div>
    </aside>

    <!-- Product display -->
    <main class="product-list">
      <div class="product-list-header">
        <h3>Áo (61 sản phẩm)</h3>
        <div>
          Sắp xếp theo:
          <button>Giá: Tăng dần</button>
          <button>Giá: Giảm dần</button>
          <button>Tên: A-Z</button>
        </div>
      </div>

      <div class="product-grid">
        <div class="product-card">
          <img src="LINK_IMAGE_1" alt="">
          <h4>tên sản phẩm</h4>
          <div class="price">27,890,000đ</div>
        </div>
        <!-- Add more product cards here -->
      </div>
    </main>
  </div>
@endsection

  

  
