@extends('home.trangchu')
@section('title', 'Giỏ hàng của bạn')
@section('content')

<div class="gt-cart-container">
  {{-- Cột trái: Chi tiết đơn hàng --}}
  <aside class="gt-cart-summary">
    <div class="gt-summary-title">CHI TIẾT ĐƠN HÀNG</div>

    {{-- Giỏ trống --}}
    @if(! $donhang || $donhang->chiTiet->isEmpty())
      <p>Giỏ hàng của bạn hiện chưa có sản phẩm nào.</p>
    @else

      {{-- Loop từng item --}}
      @foreach($donhang->chiTiet as $item)
        @php
          $unitPrice   = $item->dongia;
          $subtotal    = $item->soluong * $unitPrice;
          $discount    = $item->item_discount ?? 0;
          $discountOne = $item->soluong ? round($discount / $item->soluong, 0) : 0;
          $percentOff  = $unitPrice ? round($discountOne / $unitPrice * 100) : 0;
          $itemTotal   = $subtotal - $discount;
        @endphp

        <div class="gt-order-item">
          <div class="gt-item-thumb">
            <img src="{{ asset('images/'.$item->hinh_anh) }}" alt="SP">
            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
              @csrf @method('DELETE')
              <button class="gt-item-remove">🗑️ Xóa</button>
            </form>
          </div>

          <div class="gt-item-details">
            <div class="name">
              {{ $item->sanPham->ten }}
              — Size: <strong>{{ optional($item->kichCo)->size ?? $item->size }}</strong>
              — Màu:  <strong>{{ optional($item->mauSac)->mausac ?? $item->mausac }}</strong>
            </div>

            {{-- Số lượng và giá đơn vị --}}
            <div class="qty-line">
              <form method="POST" action="{{ route('cart.update', $item->id) }}" class="qty-form">
                @csrf
                <button name="action" value="decrease">–</button>
                <span class="qty-number">{{ $item->soluong }}</span>
                <button name="action" value="increase">+</button>
              </form>
              x {{ number_format($unitPrice,0,',','.') }} ₫

              {{-- Hiển thị % giảm nếu có --}}
              @if($discount > 0)
                <span class="discount-badge">–{{ $percentOff }}%</span>
              @endif
            </div>

            <div class="line"></div>

            {{-- Tổng tiền item --}}
            <div class="total-line">
              = {{ number_format($itemTotal,0,',','.') }} ₫
            </div>
          </div>
        </div>
      @endforeach

      {{-- Voucher & Tổng --}}
      <div class="gt-summary-footer">
        {{-- Chọn voucher --}}
        
        

        {{-- Tổng tiền hàng sau giảm --}}
        <div class="row">
          <span>Tổng tiền hàng:</span>
          <span>{{ number_format($tongSau,0,',','.') }} ₫</span>
        </div>

        {{-- Phí giao hàng --}}
        <div class="row">
          <span>Phí giao hàng (Đơn ≥300k freeship):</span>
          <span>{{ number_format($phiGiaoHang,0,',','.') }} ₫</span>
        </div>

        {{-- Tổng hóa đơn --}}
        <div class="row total">
          <span>Tổng hóa đơn:</span>
          <span class="gt-price">{{ number_format($tongCuoi,0,',','.') }} ₫</span>
        </div>
      </div>

    @endif
  </aside>

  {{-- Cột phải: Thông tin nhận hàng --}}
  <section class="gt-cart-form">
    <form action="{{ route('cart.thanhtoan') }}" method="POST">
      @csrf
      <div class="gt-form-title">NGƯỜI NHẬN HÀNG</div>

      <div class="gt-form-group">
        <label for="gt-name">Tên</label>
        <input id="gt-name" type="text" name="ten" class="gt-form-input"
               value="{{ $user->ten_nguoi_dung }}">
      </div>
      <div class="gt-form-group">
        <label for="gt-phone">Điện thoại</label>
        <input id="gt-phone" type="text" name="sdt" class="gt-form-input"
               value="{{ $user->sdt }}">
      </div>
      <div class="gt-form-group">
        <label for="gt-address">Địa chỉ</label>
        <input id="gt-address" type="text" name="dia_chi" class="gt-form-input"
               value="{{ $user->dia_chi }}">
      </div>
      <div class="gt-form-group">
        <label for="gt-city">Tỉnh/Thành phố</label>
        <select id="gt-city" name="tinh_thanh" class="gt-form-select">
          <option value="">- Chọn -</option>
          <option>Hà Nội</option>
          <option>TP. Hồ Chí Minh</option>
          <option>Đà Nẵng</option>
        </select>
      </div>
      <div class="gt-form-group">
        <label for="gt-note">Ghi chú</label>
        <textarea id="gt-note" name="ghi_chu" class="gt-form-textarea"></textarea>
      </div>
      <a href="{{ route('cart.checkout') }}" class="gt-submit-btn">ĐẶT HÀNG</a>
    </div>

@endsection
