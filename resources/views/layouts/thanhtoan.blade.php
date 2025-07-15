@extends('home.trangchu')
@section('title', 'Thanh Toán')

@section('content')
@php
  session(['url.intended' => url()->full()]);
@endphp

<div class="tt-checkout-wrapper">


  {{-- 1) ĐỊA CHỈ (chỉ auth) --}}
  @auth
  <section class="tt-address-block">
    <div class="tt-address-title">
      <i class="fa fa-map-marker-alt"></i> Địa Chỉ Nhận Hàng
    </div>
    <div class="tt-address-content">
      <strong id="tt-user-name">{{ auth()->user()->ten_nguoi_dung }}</strong>
      ( <span id="tt-user-phone">{{ auth()->user()->sdt }}</span> )
      <span class="tt-address-detail" id="tt-user-address">
        {{ auth()->user()->dia_chi }}, {{ auth()->user()->tinh_thanh }}
      </span>
      <a href="#" onclick="ttShowAddressForm(event)" class="tt-address-change">Thay Đổi</a>
    </div>
    {{-- Form ẩn --}}
    <form id="tt-address-form" class="tt-address-form" style="display:none;">
      @csrf
      <input id="tt-input-name"   class="form-control" placeholder="Họ và tên" value="{{ auth()->user()->ten_nguoi_dung }}">
      <input id="tt-input-phone"  class="form-control" placeholder="Số ĐT" value="{{ auth()->user()->sdt }}">
      <input id="tt-input-address"class="form-control" placeholder="Địa chỉ" value="{{ auth()->user()->dia_chi }}">
      <button type="button" class="btn btn-success btn-sm" onclick="ttSaveAddressAjax()">Lưu</button>
      <button type="button" class="btn btn-light btn-sm" onclick="ttHideAddressForm()">Hủy</button>
    </form>
  </section>
  @endauth

  {{-- 2) DANH SÁCH SẢN PHẨM --}}
  <section class="tt-product-list">
    <div class="tt-product-header">
      <span class="tt-col tt-col-shop">Sản phẩm</span>
      <span class="tt-col tt-col-price">Đơn giá</span>
      <span class="tt-col tt-col-qty">Số lượng</span>
      <span class="tt-col tt-col-total">Thành tiền</span>
      <span class="tt-col tt-col-action"></span>
    </div>

    @foreach ($order['items'] as $item)
      <div class="tt-product-item">
        {{-- Ảnh + tên --}}
        <div class="tt-col tt-col-shop">
          <img src="{{ $item['image_url'] }}" class="tt-product-img" alt="{{ $item['name'] }}">
          <span class="tt-product-name">
            {{ $item['name'] }}<br>
            <small>Màu: {{ $item['mausac'] }}, Size: {{ $item['size'] }}</small>
          </span>
        </div>

        {{-- Giá --}}
        <div class="tt-col tt-col-price">
          {{ number_format($item['price'],0,',','.') }}₫
        </div>
        {{-- Số lượng --}}
        <div class="tt-col tt-col-qty">
  @if($item['is_buynow'])
    {{-- Form mua-ngay luôn public --}}
    <form method="POST" action="{{ route('cart.buynow') }}" class="d-inline-flex">
      @csrf

      {{-- bắt buộc gửi lên cho controller --}}
      <input type="hidden" name="product_id"    value="{{ $buyNowData['product_id'] }}">
      <input type="hidden" name="size"          value="{{ $buyNowData['size'] }}">
      <input type="hidden" name="mausac"        value="{{ $buyNowData['mausac'] }}">
      <input type="hidden" name="quantity"      value="{{ $buyNowData['quantity'] }}">
      <input type="hidden" name="order_voucher" value="{{ request('order_voucher') }}">
      {{-- **Thêm** hai dòng này để giữ nguyên ảnh + màu mua-ngay xuyên suốt: --}}
      <input type="hidden" name="color_name"  value="{{ $buyNowData['color_name'] }}">
      <input type="hidden" name="image_path"  value="{{ $buyNowData['image_path'] }}">

      {{-- nút giảm --}}
      {{-- Giảm --}}
  <button 
    type="submit" 
    name="action" 
    value="decrease"
    @if($buyNowData['quantity'] <= 1) disabled @endif
  >–</button>

  {{-- Hiển thị số lượng --}}
  <span class="mx-2">{{ $buyNowData['quantity'] }}</span>

  {{-- Tăng --}}
  <button 
    type="submit" 
    name="action" 
    value="increase"
    @if($buyNowData['quantity'] >= $buyNowData['stock']) disabled @endif
  >+</button>
    </form>

  @else
    @auth
      {{-- Form cập nhật giỏ bình thường --}}
      <form method="POST"
            action="{{ $mode==='buynow' ? route('cart.buynow') : route('cart.update', $item['donhangsp_id']) }}"
            class="d-inline-flex">
        @csrf
        @if($mode==='buynow')
          <x-keep :data="$buyNowData" />
          <input type="hidden" name="donhangsp_id" value="{{ $item['donhangsp_id'] }}">
        @endif

        {{-- Giảm --}}
        <button name="action" value="decrease">–</button>

        {{-- Hiển thị --}}
        <span class="mx-2">{{ $item['quantity'] }}</span>

        {{-- Tăng --}}
        <button name="action" value="increase"
                @if($item['quantity'] >= $item['stock']) disabled @endif>
          +
        </button>
      </form>
    @else
      {{-- Khách: chỉ show số lượng tĩnh --}}
      <span class="mx-2">{{ $item['quantity'] }}</span>
    @endauth
  @endif
</div>

        {{-- Thành tiền --}}
        <div class="tt-col tt-col-total">
          {{ number_format($item['total'],0,',','.') }}₫
        </div>

        {{-- Xóa --}}
        <div class="tt-col tt-col-action">
          @if($item['is_buynow'])
            <form method="POST" action="{{ route('cart.buynow') }}">
              @csrf
              <input type="hidden" name="action" value="remove">
              <x-keep :data="$buyNowData" />
              <button type="submit">Xóa</button>
            </form>
          @else
            @auth
              <form method="POST" action="{{ route('cart.remove', $item['donhangsp_id']) }}">
                @csrf
                <x-keep :data="$buyNowData" />
                <button type="submit">Xóa</button>
              </form>
            @endauth
          @endif
        </div>
      </div>
    @endforeach
  </section>

  {{-- 3) Voucher --}}
  <section class="tt-note-voucher-row">
    <div class="tt-voucher-block">
      <span class="tt-voucher-label">Voucher của Shop</span>
      @if ($availableVouchers->count())
        <form method="POST" action="{{ route($mode==='buynow' ? 'cart.buynow' : 'cart.checkout') }}">
          @csrf
          <x-keep :data="$buyNowData" />
          <select name="order_voucher" onchange="this.form.submit()">
            <option value="">Chọn Voucher</option>
            @foreach ($availableVouchers as $v)
              <option value="{{ $v->id }}" {{ request('order_voucher')==$v->id?'selected':'' }}>
                {{ $v->ma_voucher }} 
                @if($v->loai==='percent') ({{ $v->soluong }}%) @else ({{ number_format($v->soluong,0,',','.') }}₫) @endif
              </option>
            @endforeach
          </select>
        </form>
      @else
        <span class="text-danger">Không có voucher khả dụng</span>
      @endif
    </div>
  </section>

  {{-- 4) Tổng thanh toán --}}
  <section class="tt-payment-summary">
    <div class="tt-payment-row">
      <span class="tt-summary-label">Tổng tiền hàng</span>
      <span class="tt-summary-value">
        {{ number_format($order['tongSau'] + $order['voucherGiam'],0,',','.') }}₫
      </span>
    </div>
    <div class="tt-payment-row">
      <span class="tt-summary-label">Phí vận chuyển</span>
      <span class="tt-summary-value">
        {{ number_format($order['phiGiaoHang'],0,',','.') }}₫
      </span>
    </div>
    @if ($order['voucherGiam']>0)
      <div class="tt-payment-row tt-summary-discount">
        <span class="tt-summary-label">Voucher giảm giá</span>
        <span class="tt-summary-value">
          -{{ number_format($order['voucherGiam'],0,',','.') }}₫
        </span>
      </div>
    @endif
    <div class="tt-payment-row tt-summary-total">
      <span class="tt-summary-label"><strong>Tổng thanh toán</strong></span>
      <span class="tt-summary-value tt-summary-value-big">
        <strong>{{ number_format($order['tongCuoi'],0,',','.') }}₫</strong>
      </span>
    </div>
  </section>

  {{-- 5) Nút đặt hàng / đăng nhập --}}
  @auth
    {{-- COD --}}
    <form method="POST" action="{{ route('cart.thanhtoan') }}" class="mb-4">
      @csrf
      <x-keep :data="$buyNowData" />
      <input type="hidden" name="order_voucher"  value="{{ request('order_voucher') }}">
      <input type="hidden" name="payment_method" value="cod">
      @foreach($order['items'] as $i => $it)
        <input type="hidden" name="products[{{ $i }}][id]"       value="{{ $it['sanpham_id'] }}">
        <input type="hidden" name="products[{{ $i }}][quantity]" value="{{ $it['quantity']    }}">
        <input type="hidden" name="products[{{ $i }}][price]"    value="{{ $it['price']       }}">
        <input type="hidden" name="products[{{ $i }}][size]"     value="{{ $it['size']        }}">
        <input type="hidden" name="products[{{ $i }}][mausac]"   value="{{ $it['mausac']      }}">
        <input type="hidden" name="products[{{ $i }}][image]"    value="{{ $it['image_url']   }}">
      @endforeach
      <input type="hidden" name="total" value="{{ $order['tongCuoi'] }}">
      <button class="tt-order-btn">Đặt hàng</button>
    </form>

    {{-- VNPAY --}}
    <form method="POST" action="{{ route('vnpay.payment') }}">
      @csrf
      <input type="hidden" name="total_vnpay" value="{{ $order['tongCuoi'] }}">
      @php
        $all = []; $skipKey = null;
        if (!empty($buyNowData['product_id'])) {
          $all[] = [
            'id'=>$buyNowData['product_id'], 'quantity'=>$buyNowData['quantity'],
            'price'=>\App\Models\SanPham::find($buyNowData['product_id'])->gia_ban,
            'size'=>$buyNowData['size'],'mausac'=>$buyNowData['color_name'],'image'=>$buyNowData['image_path'],
          ];
          $skipKey = $buyNowData['product_id'].'_'.$buyNowData['size'].'_'.$buyNowData['color_name'];
        }
        foreach ($order['items'] as $it) {
          $key = $it['sanpham_id'].'_'.$it['size'].'_'.$it['mausac'];
          if ($skipKey && $key === $skipKey) continue;
          $all[] = ['id'=>$it['sanpham_id'],'quantity'=>$it['quantity'],'price'=>$it['price'],'size'=>$it['size'],'mausac'=>$it['mausac'],'image'=>$it['image_url']];
        }
      @endphp
      @foreach ($all as $i => $item)
        <input type="hidden" name="products[{{ $i }}][id]"       value="{{ $item['id'] }}">
        <input type="hidden" name="products[{{ $i }}][quantity]" value="{{ $item['quantity'] }}">
        <input type="hidden" name="products[{{ $i }}][price]"    value="{{ $item['price'] }}">
        <input type="hidden" name="products[{{ $i }}][size]"     value="{{ $item['size'] }}">
        <input type="hidden" name="products[{{ $i }}][mausac]"   value="{{ $item['mausac'] }}">
        <input type="hidden" name="products[{{ $i }}][image]"    value="{{ $item['image'] }}">
      @endforeach
      <button class="tt-order-btn-secondary">Thanh toán VNPAY</button>
    </form>
  @else
  <div class="tt-guest-notice">
    <p>Bạn có thể “Mua ngay” nhưng để nhận ưu đãi, hãy đăng nhập hoặc đăng ký.</p>
    <a href="{{ route('login') }}" class="btn btn-primary">Đăng nhập</a>
    
  </div>
@endauth

  <div class="tt-order-note">
    Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo <a href="#">Điều khoản</a> của Shop.
  </div>

</div>

<script>
  window.ttUpdateAddressUrl = "{{ route('nguoidung.update_address') }}";
</script>
@endsection
