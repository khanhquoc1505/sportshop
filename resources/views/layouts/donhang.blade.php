@extends('home.trangchu')
@section('title', 'Đơn hàng')
@section('content')
<div class="dh-container dh-mt-4">
  <h3 class="dh-mb-4">Đơn hàng của tôi</h3>

  <!-- Order 1 -->
  <div class="dh-card">
    <div class="dh-card-body">
      <h5 class="dh-card-title">
        1. 230429112534907987 ngày 29/04/2023 [ - 5 %]
      </h5>
      <p class="dh-text-muted dh-mb-3">
        Quần Vải Dáng Vừa Y2010 Originals Ver13 Vol 23 / Đen / 30 … và 1 sản phẩm khác
      </p>
      <hr>
      <p class="dh-mb-1">
        Tổng hóa đơn: 397.000 đ / Thành tiền: 377.000 đ
      </p>
      <p class="dh-mb-3">
        Tình trạng đơn hàng:
        <span class="dh-badge dh-bg-warning">Đã mua tại Cửa hàng</span>
      </p>
      <a href="{{ url('/chitietdonhang') }}" class="dh-btn dh-btn-info">Xem chi tiết</a>
    </div>
  </div>

  <!-- Order 2 -->
  <div class="dh-card">
    <div class="dh-card-body">
      <h5 class="dh-card-title">
        2. 230428173716907987 ngày 28/04/2023 [ - 5 %]
      </h5>
      <p class="dh-text-muted dh-mb-3">
        [HUY0324] Áo Polo Cổ Bé Tay Ngắn Vải Cotton… / Trắng / M … và 1 sản phẩm khác
      </p>
      <hr>
      <p class="dh-mb-1">
        Tổng hóa đơn: 255.000 đ / Thành tiền: 242.000 đ
      </p>
      <p class="dh-mb-3">
        Tình trạng đơn hàng:
        <span class="dh-badge dh-bg-warning">Đã mua tại Cửa hàng</span>
      </p>
      <a href="{{ url('/chitietdonhang') }}" class="dh-btn dh-btn-info">Xem chi tiết</a>
    </div>
  </div>

  <!-- Khi chưa có đơn -->
  <!--
  <div class="alert alert-secondary">
    Bạn chưa có đơn hàng nào.
  </div>
  -->
</div>
@endsection