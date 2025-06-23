@extends('home.trangchu')
@section('title', $product->ten)
@section('content')
<div class="ct-product-tong">
  <div class="ct-product-detail">
    <div class="ct-product-detail-container">
      {{-- Thumbnails --}}
      <div class="ct-thumbnail-list">
  @foreach($thumbnails as $url)
    <img class="thumbnail" src="{{ $url ?? asset('images/default.jpg') }}" alt="thumb">
@endforeach
</div>
      {{-- Main image: lấy ảnh đầu --}}
      <div class="ct-main-image">
        {{-- Khởi tạo với ảnh đầu --}}
        <img id="main-image" src="{{ $thumbnails->first() ?? asset('images/default.jpg') }}" alt="Main image">
      </div>
    </div>

    <div class="ct-product-info">
      <h2 class="ct-product-title">{{ $product->ten }}</h2>
      <div class="ct-product-meta">
        <p><strong>Mã sản phẩm:</strong> {{ $product->masanpham }}</p>
        <p><strong>Trạng thái:</strong>
          {{ $product->trang_thai==='active'?'Còn hàng':'Hết hàng' }}
        </p>
      </div>
      <div class="ct-product-price">
        {{ number_format($product->gia_ban,0,',','.') }} VNĐ
      </div>

      <div class="ct-product-options">
        {{-- Màu --}}
        <label>Màu</label>
        <div class="ct-color-options">
          @foreach($colorVariants as $i => $cv)
            <div class="color-swatch {{ $i===0?'active':'' }}"
                 data-image="{{ $cv['image_url'] }}"
                 title="{{ $cv['mausac'] }}">
              {{ $cv['mausac'] }}
            </div>
          @endforeach
        </div>

        {{-- Size --}}
        <label>SIZE</label>
        <div class="ct-size-options">
          @foreach($sizes as $size)
            <button class="size-btn">{{ $size->size }}</button>
          @endforeach
        </div>
      </div>

      <div class="ct-product-quantity">
        <button id="qty-decrease">-</button>
        <input id="qty" type="text" value="1" readonly>
        <button id="qty-increase">+</button>
      </div>

      <div class="ct-action-buttons">
        <form action="{{ route('cart.add', $product) }}" method="POST">
          @csrf
          <input type="hidden" name="quantity" id="form-qty" value="1">
          <button type="submit" class="ct-add-to-cart">Thêm vào giỏ hàng</button>
        </form>
        <button class="ct-buy-now">Mua ngay</button>
        <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
          @csrf
          <button class="ct-add-to-yt">Yêu thích</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Tabs: mô tả, đánh giá, danh sách đánh giá --}}
  <div class="ct-product-detail-tabs">
    <div class="ct-tab-buttons">
      <button class="ct-tab-btn active" onclick="showTab('details')">CHI TIẾT SẢN PHẨM</button>
      <button class="ct-tab-btn" onclick="showTab('reviews')">GỬI ĐÁNH GIÁ</button>
      <button class="ct-tab-btn" onclick="showTab('danhsach')">DANH SÁCH ĐÁNH GIÁ</button>
    </div>

    <div id="details" class="ct-tab-content active">
      {!! $product->mo_ta !!}
    </div>

    <div id="reviews" class="ct-tab-content">
      <h3>Gửi đánh giá</h3>
      <form action="{{ route('product.mo_ta', $product) }}" method="POST" class="ct-review-form">
        @csrf
        <label>Đánh giá của bạn:</label>
        <select name="sosao">
          @for($i=1;$i<=5;$i++)
            <option value="{{ $i }}">{{ str_repeat('★',$i) }}</option>
          @endfor
        </select>
        <input name="ten" type="text" placeholder="Họ và tên *" required>
        <input name="sdt" type="text" placeholder="Số điện thoại *" required>
        <textarea name="noi_dung" rows="5" placeholder="Đánh giá của bạn..." required></textarea>
        <button type="submit">Gửi đánh giá</button>
      </form>
    </div>

    <div id="danhsach" class="ct-tab-content">
      <h3>Đánh giá gần đây</h3>
      @forelse($product->danhGias as $dg)
        <div class="ct-review-item">
          <div class="ct-review-name">{{ $dg->user->ten_nguoi_dung ?? 'Khách' }}</div>
          <span class="ct-stars">{{ str_repeat('★', $dg->sosao) }}</span>
          <div class="ct-review-text">{{ $dg->user->pivot->noi_dung ?? '' }}</div>
        </div>
      @empty
        <p>Chưa có đánh giá nào.</p>
      @endforelse
    </div>
  </div>

  {{-- Sản phẩm liên quan --}}
  <div class="ct-lienquan">Sản phẩm liên quan</div>
<div class="ct-related-products">
  @forelse($related as $item)
    <div class="ct-related-product-card">
      <a href="{{ route('product.show', $item->id) }}">
        {{-- Ảnh đại diện sản phẩm liên quan --}}
        <img 
          src="{{ asset('images/' . $item->avatarImage->hinh_anh) }}" 
          alt="{{ $item->ten }}"
        >
        <div class="ct-related-info">
          <p>{{ $item->ten }}</p>
          <strong>{{ number_format($item->gia_ban, 0, ',', '.') }}đ</strong>
        </div>
      </a>
    </div>
  @empty
    <p>Không có sản phẩm liên quan.</p>
  @endforelse
</div>
@endsection
