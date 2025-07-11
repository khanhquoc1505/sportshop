@extends('home.trangchu')
@section('title', 'Chi tiết đơn hàng')
@section('content')
<div class="ct-container">
  <nav class="ct-breadcrumb">
    <a href="{{ url('/') }}">Trang chủ</a> /
    <a href="{{ route('donhang.index') }}">Đơn hàng của tôi</a> /
    <span>{{ $order->madon }}</span>
  </nav>
  <h1 class="ct-title">Mã đơn: {{ $order->madon }}</h1>
  <p class="ct-subtext">Ngày đặt hàng: {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y')}}</p>
  @php
            // map trạng thái string -> CSS class hoặc label nếu cần
            $labels = [
              'chuadathang' => 'Chưa thanh toán',
              'dathanhtoan' => 'Đã thanh toán',
              'hoanthanh'   => 'Hoàn thành',
              'huy'          => 'Đã hủy',
            ];
            $classes = [
              'chuadathang' => 'dh-bg-warning',
              'dathanhtoan' => 'dh-bg-warning',
              'hoanthanh'   => 'dh-bg-success',
              'huy'   => 'dh-bg-success',
            ];
            $st = $order->trangthaidonhang;       // string lấy từ DB
            $text  = $labels[$st]  ?? ucfirst($st);
            $class = $classes[$st] ?? 'dh-bg-secondary';
          @endphp
          <span class="dh-badge {{ $class }}">
            {{ $labels[$order->trangthaidonhang] ?? ucfirst($order->trangthaidonhang) }}
          </span>

    <!-- ///////////////////////////////////////////////////// -->

    @unless($order->trangthaidonhang === 'huy')
  @php
    $deliveryLabels = [
      'pending'        => 'Chờ giao hàng',
      'waiting_pickup' => 'Chờ lấy hàng',
      'shipping'       => 'Đang giao hàng',
      'delivered'      => 'Đã giao hàng',
      'returned'       => 'Trả hàng',
      'canceled'       => 'Hủy giao hàng',
      'incomplete'     => 'Chưa hoàn thành',
    ];
    $dl = $order->delivery_status;
    $dlText = $deliveryLabels[$dl] ?? ucfirst($dl);
  @endphp

  <div class="mt-4">
    <span class="text-sm text-gray-600">Trạng thái Giao hàng:</span>
    <span class="dh-badge dh-bg-info ml-2">{{ $dlText }}</span>
  </div>
@endunless

     <!-- ///////////////////////////////////////////////////// -->


  <h2 class="ct-section-title">Chi tiết đơn hàng</h2>
  <div class="ct-order-details">
    <div class="ct-item-row ct-header">
      <div class="ct-item-desc">Sản phẩm</div>
      <div class="ct-item-price">Giá</div>
      <div class="ct-item-qty">SL</div>
      <div class="ct-item-discount">Giảm</div>
      <div class="ct-item-amount">Thành tiền</div>
    </div>
    <hr class="ct-separator" />

    @foreach($order->chiTiet as $item)
      @php
        $subtotal = $item->dongia * $item->soluong;
        $discount = $item->item_discount ?? 0;
        $total    = $subtotal - $discount;
      @endphp
      <div class="ct-item-row">
        <div class="ct-item-desc">{{ $item->sanPham->ten }} / {{ $item->sanPham->masanpham }}</div>
        <div class="ct-item-price">{{ number_format($item->dongia,0,',','.') }}₫</div>
        <div class="ct-item-qty">× {{ $item->soluong }}</div>
        <div class="ct-item-discount">{{ $discount ? '-'.number_format($discount,0,',','.') : '-' }}</div>
        <div class="ct-item-amount">{{ number_format($total,0,',','.') }}₫</div>
      </div>
      <hr class="ct-separator" />

      {{-- Nút và form đánh giá cho từng sản phẩm --}}
      <button type="button"
              class="btn-show-review"
              data-product-id="{{ $item->sanPham->id }}">
        GỬI ĐÁNH GIÁ
      </button>
      <div id="review-form-{{ $item->sanPham->id }}" class="order-detail-review">
        <h3>ĐÁNH GIÁ SẢN PHẨM</h3>
        <form action="{{ route('danhgia.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="san_pham_id" value="{{ $item->sanPham->id }}">

          <div class="review-form-group">
            <label>Đánh giá của bạn:</label>
            <select name="sosao" required>
              @for($i=1; $i<=5; $i++)
                <option value="{{ $i }}">{{ str_repeat('★',$i) }}</option>
              @endfor
            </select>
          </div>

          <div class="review-form-group">
            <textarea name="noi_dung" rows="4" placeholder="Đánh giá của bạn..." required></textarea>
          </div>

          <div class="review-form-group">
            <label>Hình kèm đánh giá (tuỳ chọn):</label>
            <input type="file" name="hinh_anh[]" accept="image/*" multiple>
          </div>

          <button type="submit">Gửi đánh giá</button>
        </form>
      </div>
    @endforeach
  </div>

  <h2 class="ct-section-title">Hóa đơn</h2>
  <div class="ct-summary">
    @php
      $productsTotal = $order->chiTiet->sum(fn($i)=> $i->dongia * $i->soluong);
      $discountTotal = $order->chiTiet->sum(fn($i)=> $i->item_discount ?? 0);
      $finalTotal    = $productsTotal - $discountTotal;
    @endphp
    <div class="ct-summary-row">
      <div class="ct-summary-label">Tổng giá trị sản phẩm:</div>
      <div class="ct-summary-value">{{ number_format($productsTotal,0,',','.') }}₫</div>
    </div>
    <div class="ct-summary-row">
      <div class="ct-summary-label">Giảm giá:</div>
      <div class="ct-summary-value">-{{ number_format($discountTotal,0,',','.') }}₫</div>
    </div>
    <hr class="ct-separator" />
    <div class="ct-summary-row total">
      <div class="ct-summary-label">Tổng hóa đơn:</div>
      <div class="ct-summary-value">{{ number_format($finalTotal,0,',','.') }}₫</div>
    </div>
  </div>
  {{-- Hiển thị flash message --}}
{{-- chỉ hiện khi chuadathang hoặc dathanhtoan --}}
@if((int)$order->trangthai === 2)
  <form action="{{ route('donhang.cancel', $order->id) }}"
        method="POST"
        onsubmit="return confirm('Bạn chắc chắn muốn hủy?');">
    @csrf
    @method('PATCH')
    <button class="btn btn-danger">Hủy đơn hàng</button>
  </form>
@endif
</div>
@endsection
