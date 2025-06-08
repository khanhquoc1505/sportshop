@extends('home.trangchu')
@section('title', 'Chi tiết đơn hàng')
@section('content')
<div class="ct-container">
    <!-- Breadcrumb -->
    <nav class="ct-breadcrumb">
      <a href="#">Trang chủ</a> / 
      <a href="#">Đơn hàng của tôi</a> / 
      <span>132321</span>
    </nav>

    <!-- Tiêu đề chính -->
    <h1 class="ct-title">Mã đơn: 230429112534907987</h1>
    <p class="ct-subtext">Ngày đặt hàng: 8/6/2025</p>
    <p class="ct-subtext">Trạng thái: hoàng thành</p>

    <!-- Chi tiết đơn hàng -->
    <h2 class="ct-section-title">Chi tiết đơn hàng</h2>
    <div class="ct-order-details">
      <div class="ct-item-row">
        <div class="ct-item-desc">
          Tên sản phẩm
        </div>
        <div class="ct-item-price">Giá</div>
        <div class="ct-item-qty">SL</div>
        <div class="ct-item-discount">Giảm giá</div>
        <div class="ct-item-amount">Tổng</div>
      </div>
      <hr class="ct-separator" />
      <div class="ct-item-row">
      </div>
    </div>
    <div class="ct-order-details">
      <div class="ct-item-row">
        <div class="ct-item-desc">
          1. tên sản phẩm / mã sản phẩm
        </div>
        <div class="ct-item-price">0</div>
        <div class="ct-item-qty">× 1</div>
        <div class="ct-item-discount">−0%</div>
        <div class="ct-item-amount">0</div>
      </div>
      <hr class="ct-separator" />
      <div class="ct-item-row">
        <div class="ct-item-desc">
          2. tên sản phẩm / mã sản phẩm
        </div>
        <div class="ct-item-price">397.000</div>
        <div class="ct-item-qty">× 1</div>
        <div class="ct-item-discount">−5%</div>
        <div class="ct-item-amount">377.150</div>
      </div>
    </div>

    <!-- Hóa đơn tổng hợp -->
    <h2 class="ct-section-title">Hóa đơn</h2>
    <div class="ct-summary">
      <div class="ct-summary-row">
        <div class="ct-summary-label">Tổng giá trị sản phẩm:</div>
        <div class="ct-summary-value">397.000</div>
      </div>
      <div class="ct-summary-row">
        <div class="ct-summary-label">Giảm giá:</div>
        <div class="ct-summary-value">19.850</div>
      </div>
      <hr class="ct-separator" />
      <div class="ct-summary-row">
        <div class="ct-summary-label">Tổng hóa đơn:</div>
        <div class="ct-summary-value">377.150</div>
      </div>
    </div>
  </div>
@endsection
