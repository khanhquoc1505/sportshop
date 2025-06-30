@extends('home.trangchu')
@section('title', 'Thanh Toán')

@section('content')
<div class="tt-checkout-wrapper">

    {{-- ╔═══════════════ ĐỊA CHỈ ═══════════════╗ --}}
    <section class="tt-address-block">
        <div class="tt-address-title"><i class="fa fa-map-marker-alt"></i> Địa Chỉ Nhận Hàng</div>
        <div class="tt-address-content">
            <strong id="tt-user-name">{{ $user->ten_nguoi_dung }}</strong>
            ( <span id="tt-user-phone">{{ $user->sdt }}</span> )
            <span class="tt-address-detail" id="tt-user-address">
                {{ $user->dia_chi }}, {{ $user->tinh_thanh }}
            </span>
            <span class="tt-address-default">Mặc Định</span>
            <a href="#" onclick="ttShowAddressForm(event)" class="tt-address-change">Thay Đổi</a>
        </div>

        {{-- form đổi địa chỉ --}}
        <form id="tt-address-form" class="tt-address-form" style="display:none;">
            @csrf
            <div class="tt-address-title"><i class="fa fa-map-marker-alt"></i> Tên</div>
            <input id="tt-input-name"  class="form-control" placeholder="Họ và tên" value="{{ $user->ten_nguoi_dung }}">
            <div class="tt-address-title"><i class="fa fa-map-marker-alt"></i> SĐT</div>
            <input id="tt-input-phone" class="form-control" placeholder="Số ĐT"        value="{{ $user->sdt }}">
            <div class="tt-address-title"><i class="fa fa-map-marker-alt"></i> ĐỊA CHỈ</div>
            <input id="tt-input-address" class="form-control" placeholder="Địa chỉ"    value="{{ $user->dia_chi }}">
            <button type="button" class="btn btn-success btn-sm" onclick="ttSaveAddressAjax()">Lưu</button>
            <button type="button" class="btn btn-light   btn-sm" onclick="ttHideAddressForm()">Hủy</button>
        </form>

        {{-- hidden để submit thanh toán --}}
        <input type="hidden" id="tt-hidden-address" value="{{ $user->dia_chi }}">
        <input type="hidden" id="tt-hidden-name"    value="{{ $user->ten_nguoi_dung }}">
        <input type="hidden" id="tt-hidden-phone"   value="{{ $user->sdt }}">
    </section>

    {{-- ╔═══════════════ DANH SÁCH SẢN PHẨM ═══════════════╗ --}}
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
            {{-- ảnh + tên --}}
            <div class="tt-col tt-col-shop">
                <img src="{{ $item['image_url'] }}" class="tt-product-img">
                <span class="tt-product-name">
                    {{ $item['name'] }}<br>
                    <small>Màu: {{ $item['mausac'] }}, Size: {{ $item['size'] }}</small>
                </span>
            </div>

            {{-- đơn giá --}}
            <div class="tt-col tt-col-price">
                {{ number_format($item['price'],0,',','.') }}₫
            </div>

            {{-- số lượng --}}
            <div class="tt-col tt-col-qty">
                @if (!empty($item['donhangsp_id']))
                    {{-- item giỏ hàng --}}
                    <form method="POST" action="{{ route('cart.buynow') }}" class="d-inline-flex">
                        @csrf
                        <button name="action" value="decrease">–</button>
                        <span class="mx-2">{{ $item['quantity'] }}</span>
                        <button name="action" value="increase">+</button>

                        <input type="hidden" name="donhangsp_id" value="{{ $item['donhangsp_id'] }}">
                        <x-keep :data="$buyNowData ?? []"/>
                    </form>
                @else
                    {{-- item mua ngay --}}
                    <form method="POST" action="{{ route('cart.buynow') }}" class="d-inline-flex">
                        @csrf
                        <button name="action" value="decrease">–</button>
                        <span class="mx-2">{{ $item['quantity'] }}</span>
                        <button name="action" value="increase">+</button>

                        <x-keep :data="$buyNowData"/>
                        <input type="hidden" name="quantity" value="{{ $item['quantity'] }}">
                    </form>
                @endif
            </div>

            {{-- thành tiền --}}
            <div class="tt-col tt-col-total">
                {{ number_format($item['total'],0,',','.') }}₫
            </div>

            {{-- xoá --}}
            <div class="tt-col tt-col-action">
                @if (!empty($item['donhangsp_id']))
    {{-- A. Xoá item đã nằm trong giỏ (DB) --}}
                  <form method="POST" action="{{ route('cart.remove', $item['donhangsp_id']) }}">
  @csrf
  <x-keep :data="$buyNowData"/>
  <button type="submit">Xóa</button>
</form>
                @else
    {{-- B. Xoá item “mua ngay” (không ở DB) --}}
          <form method="POST" action="{{ route('cart.buynow') }}">
        @csrf
        <input type="hidden" name="action" value="remove">
        <x-keep :data="$buyNowData"/>
        <button type="submit">Xóa</button>
          </form>
        @endif
            </div>
        </div>
        @endforeach
    </section>

    {{-- ╔═══════════════ VOUCHER ═══════════════╗ --}}
    <section class="tt-note-voucher-row">
        <div class="tt-voucher-block">
            <span class="tt-voucher-label">Voucher của Shop</span>

            @if ($availableVouchers->count())
                <form method="POST" action="{{ route('cart.buynow') }}">
                    @csrf
                    <x-keep :data="$buyNowData"/>
                    <select name="order_voucher" onchange="this.form.submit()">
                        <option value="">Chọn Voucher</option>
                        @foreach ($availableVouchers as $v)
                            <option value="{{ $v->id }}" {{ request('order_voucher')==$v->id?'selected':'' }}>
                                {{ $v->ma_voucher }}
                                @if($v->loai==='percent')
                                    ({{ $v->soluong }}%)
                                @else
                                    ({{ number_format($v->soluong,0,',','.') }}₫)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </form>
            @else
                <span class="text-danger">Không có voucher khả dụng</span>
            @endif
        </div>
    </section>

    {{-- ╔═══════════════ TÓM TẮT ═══════════════╗ --}}
    <section class="tt-payment-summary">
        <div class="tt-payment-row">
            <span class="tt-summary-label">Tổng tiền hàng</span>
            <span class="tt-summary-value">{{ number_format($order['tongSau'] + $order['voucherGiam'],0,',','.') }}₫</span>
        </div>
        <div class="tt-payment-row">
            <span class="tt-summary-label">Phí vận chuyển</span>
            <span class="tt-summary-value">{{ number_format($order['phiGiaoHang'],0,',','.') }}₫</span>
        </div>
        @if ($order['voucherGiam']>0)
            <div class="tt-payment-row tt-summary-discount">
                <span class="tt-summary-label">Voucher giảm giá</span>
                <span class="tt-summary-value">-{{ number_format($order['voucherGiam'],0,',','.') }}₫</span>
            </div>
        @endif
        <div class="tt-payment-row tt-summary-total">
            <span class="tt-summary-label"><strong>Tổng thanh toán</strong></span>
            <span class="tt-summary-value tt-summary-value-big">
                <strong>{{ number_format($order['tongCuoi'],0,',','.') }}₫</strong>
            </span>
        </div>
    </section>

    {{-- ╔═══════════════ PHƯƠNG THỨC & ĐẶT HÀNG ═══════════════╗ --}}
    <form action="{{ route('cart.thanhtoan') }}" method="POST" class="space-y-4">
        @csrf
        <x-keep :data="$buyNowData"/>
        @if (!empty($buyNowData))
    @foreach ($buyNowData as $key => $val)
      <input type="hidden"
             name="buyNowData[{{ $key }}]"
             value="{{ $val }}">
    @endforeach
  @endif
        <input type="hidden" name="order_voucher" value="{{ request('order_voucher') }}">

        <section class="tt-payment-method-row flex items-center gap-4">
            {{-- chọn phương thức --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <label class="flex items-center gap-1">
                    <input type="radio" name="payment_method" value="cod" checked>
                    <span>Thanh toán khi nhận hàng</span>
                </label>
            </div>
            {{-- nút đặt hàng --}}
            <button type="submit" class="ml-auto tt-order-btn">Đặt hàng</button>
        </section>
    </form>
    <!-- //////////////////////////////////////// -->
    <form action="{{ route('vnpay.payment') }}" method="post">
  @csrf
  <input type="hidden" name="total_vnpay" value="{{ $order['tongCuoi'] }}">
  @foreach($order['items'] as $i => $item)
    <input type="hidden" name="products[{{ $i }}][id]" value="{{ $item['sanpham_id'] }}">
    <input type="hidden" name="products[{{ $i }}][quantity]" value="{{ $item['quantity'] }}">
    <input type="hidden" name="products[{{ $i }}][price]" value="{{ $item['price'] }}">
    <input type="hidden" name="products[{{ $i }}][size]" value="{{ $item['size']}}">
    <input type="hidden" name="products[{{ $i }}][mausac]" value="{{ $item['mausac']}}">
    <input type="hidden" name="products[{{ $i }}][image]" value="{{ $item['image_url'] }}">
  @endforeach
  <button type="submit">Thanh toán VNPAY</button>
</form>


    <!-- //////////////////////////////////////// -->

    <div class="tt-order-note">
        Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo <a href="#">Điều khoản</a> của Shop.
    </div>
</div>

<script>
    window.ttUpdateAddressUrl = "{{ route('nguoidung.update_address') }}";
</script>
@endsection
