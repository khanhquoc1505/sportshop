@extends('home.trangchu')
@section('title', 'giỏ hàng')
@section('content')
<div class="gt-cart-container">

    <!-- Chi tiết đơn hàng -->
    <aside class="gt-cart-summary">
      <div class="gt-summary-title">CHI TIẾT GIỎ HÀNG</div>
      <!-- Sản phẩm -->
      <div class="gt-order-item">
        <div>
            <img src="https://cdn2.yame.vn/pimg/ao-so-mi-co-tru-tay-ngan-soi-nhan-tao-tham-hut-bieu-tuong-dang-rong-on-gian-seventy-seven-24-0023265/13dc542e-bf76-4500-aed8-001c30ed3cfc.jpg?w=540&h=756&c=true&v=052025" alt="Sản phẩm" class="gt-item-image">
            <div class="gt-item-remove">🗑️ Xóa</div>
        </div>
        <div>
            <div class="gt-item-info">
                <div class="gt-item-name">
                    Áo Sơ Mi Nam Cổ Trụ Vải Linen Thoáng mát Seventy Seven 24 - Trắng, S
                </div>
                <div class="gt-item-qty-price">
                    Số lượng 1 × 205.600 ₫ 
                </div>
            </div>
            <div class="gt-line"></div>
            <div class="gt-summary-row">
                <strong>=</strong>
            <span class="gt-price">205.600 ₫</span>
      </div>

        </div>
      </div>
      <!-- Phí giao hàng -->
      <div class="gt-summary-row">
        <span>Phí giao hàng:</span>
        <span class="gt-price">19.000 ₫</span>
      </div>
      <div style="font-size:.9rem;color:#777;margin-bottom:1rem;">
        (Miễn phí với đơn hàng trên 300,000 ₫)
      </div>

      <div class="gt-line"></div>

      <!-- Tổng -->
      <div class="gt-summary-row gt-grand-total">
        <span>Tổng:</span>
        <span class="gt-price">224.600 ₫</span>
      </div>

      <a href="{{ url('/dangnhap') }}" class="gt-vip-note">ĐĂNG NHẬP/TẠO TÀI KHOẢN để nhận voucher giảm giá</a>
    </aside>

    <!-- Thông tin người nhận -->
    <section class="gt-cart-form">
      <div class="gt-form-title">NGƯỜI NHẬN HÀNG</div>

      <div class="gt-form-group">
        <label for="gt-name">Tên</label>
        <input type="text" id="gt-name" class="gt-form-input" placeholder="Tên người nhận">
      </div>

      <div class="gt-form-group">
        <label for="gt-phone">Điện thoại liên lạc</label>
        <input type="text" id="gt-phone" class="gt-form-input" placeholder="Số điện thoại">
      </div>

      <div class="gt-form-group gt-radio-group">
        <label><input type="radio" name="gt-delivery" checked> Địa chỉ</label>
        <input type="text" class="gt-form-input" placeholder="Địa chỉ nhận hàng">
      </div>

      <div class="gt-form-group">
        <select class="gt-form-select">
          <option value="">- Chọn Tỉnh/Thành phố -</option>
          <option>Hà Nội</option>
          <option>TP. Hồ Chí Minh</option>
          <option>Đà Nẵng</option>
        </select>
      </div>

      <div class="gt-form-group">
        <label for="gt-note">Ghi chú</label>
        <textarea id="gt-note" class="gt-form-textarea" placeholder="Ghi chú (nếu có)"></textarea>
      </div>

      <div class="gt-form-group gt-radio-group">
        <label class="gt-radio-option">
          <input type="radio" name="gt-payment" checked>
          Thanh toán khi nhận hàng (COD)
        </label>
        <label class="gt-radio-option">
          <input type="radio" name="gt-payment">
          Thanh toán bằng ví MoMo
        </label>
      </div>

      <button class="gt-submit-btn">ĐẶT HÀNG: GIAO HÀNG VÀ THU TIỀN TẬN NƠI</button>
    </section>

  </div>
@endsection
