@extends('home.trangchu')
@section('title', 'chitiet')
@section('content')
<div class="ct-product-tong">
<div class="ct-product-detail">
  <div class="ct-product-detail-container">
    <div class="ct-thumbnail-list">
      <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-den.jpg" alt="">
      <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-den.jpg" alt="">
      <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-den.jpg" alt="">
      <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-den.jpg" alt="">
    </div>
    <div class="ct-main-image">
      <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-den.jpg" alt="Main product image">
    </div>
  </div>

  <div class="ct-product-info">
    <h2 class="ct-product-title">Găng tay bóp</h2>
    <div class="ct-product-meta">
     <p><strong>Mã sản phẩm:</strong>dsfdsdfdsaaaaa </p>
      <p><strong>Trạng thái:còn hàng</strong></p>
    </div>
    <div class="ct-product-price">10000000 VNĐ</div>

    <div class="ct-product-options">
      <label>Màu</label>
      <div class="ct-color-options">
        <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-den.jpg" alt="">
        <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-do-1.jpg" alt="">
        <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-xanh-la-1.jpg" alt="">
        <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-xanh-duong.jpg" alt="">
      </div>

      <label>SIZE</label>
      <div class="ct-size-options">
        <button>S</button>
        <button>M</button>
        <button>L</button>
        <button>XL</button>
      </div>
    </div>

    <div class="ct-product-quantity">
      <button>-</button>
      <input type="text" value="1" readonly>
      <button>+</button>
    </div>

    <div class="ct-action-buttons">
      <button class="ct-buy-now">Mua ngay</button>
      <button class="ct-add-to-cart">Thêm vào giỏ hàng</button>
      <button class="ct-add-to-yt">Thêm yêu thích</button>
    </div>
  </div>
</div>

<!-- Mô tả + sản phẩm gợi ý -->
<div class="ct-product-detail-tabs">
  <div class="ct-tab-buttons">
    <button class="ct-tab-btn active" onclick="showTab('details')">CHI TIẾT SẢN PHẨM</button>
    <button class="ct-tab-btn" onclick="showTab('reviews')">ĐÁNH GIÁ</button>
    <button class="ct-tab-btn" onclick="showTab('danhsach')">DANH SÁCH ĐÁNH GIÁ</button>
  </div>

  <div id="details" class="ct-tab-content active">
    <h2>Đặc điểm Găng tay Thủ môn Eepro Victory EG10A3</h2>
    <p><a href="#">Găng tay thủ môn <strong>Eepro Victory AG10A3</strong></a> là một trong những món <a href="#">phụ kiện bóng đá</a> đặc lực...</p>
    <ul>
      <li>Mút Latex Grippy Đức: làm từ cao su Đức chất lượng cao với độ dày 4mm</li>
      <li>Thiết kế 3D Back Hand: đột phá 3D trên mặt sau...</li>
      <li>Dòng Găng NEGATIVE CUT: độ ôm chặt tối đa giúp kiểm soát bóng chính xác</li>
      <li>Silicon chống trượt trong lòng bàn tay và 4 ngón: cảm giác êm ái</li>
      <li>Dây đeo cổ tay Hand Strap: chắc chắn, bảo vệ cổ tay</li>
      <li>Xương ngón tay Ergo Roll: dễ dàng tháo lắp và bảo trì</li>
    </ul>
  </div>

  <div id="reviews" class="ct-tab-content">
    <h3>Gửi đánh giá</h3>
    <div class="ct-review-stars">
      <label>Đánh giá của bạn: </label>
      <span class="ct-stars">★ ★ ★ ★ ★</span>
    </div>
    <form class="ct-review-form">
      <input type="text" placeholder="Họ và tên *" required>
      <input type="text" placeholder="Số điện thoại *" required>
      <textarea rows="5" placeholder="Đánh giá của bạn..." required></textarea>
      <button type="submit">Gửi đánh giá</button>
    </form>
  </div>
    
  <div id="danhsach" class="ct-tab-content">
    <h3>Đánh giá gần đây</h3>
    <div class="ct-review-item">
      <div class="ct-review-name">Nguyễn Văn A </div> <span class="ct-stars">★ ★ ★ ★ ★</span>
      <div class="ct-review-text">Sản phẩm rất tốt, giao hàng nhanh.</div>
      <div class="ct-color-options">
        <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-xanh-duong.jpg" alt="">
      </div>
    </div>
    <!-- Các đánh giá thêm, mặc định ẩn -->
    <div class="ct-review-item hidden-review">
      <div class="ct-review-name">Phạm Thị D</div> <span class="ct-stars">★ ★ ★ ★ ☆</span>
      <div class="ct-review-text">Đóng gói cẩn thận, giao hàng nhanh.</div>
      <div class="ct-color-options">
        <img src="LINK_IMAGE_4" alt="">
      </div>
    </div>
    <div class="ct-review-item hidden-review">
      <div class="ct-review-name">Ngô Minh E</div> <span class="ct-stars">★ ★ ★ ☆ ☆</span>
      <div class="ct-review-text">Ổn nhưng giao hơi chậm.</div>
      <div class="ct-color-options">
        <img src="LINK_IMAGE_5" alt="">
      </div>
    </div>
    <button id="showMoreBtn" class="ct-show-more">Xem thêm</button>
  </div>
  
</div>

<div class="ct-lienquan">Một Sản Phẩm liên quan</div>
<div class="ct-related-products">
    
  @for ($i = 0; $i < 8; $i++)
  <div class="ct-related-product-card">
    <img src="https://cdn.yousport.vn/Media/Products/010325111718959/eepro-victory-ag10a3-den.jpg" alt="">
    <div class="ct-related-info">
      <p>Text</p>
      <strong>$0</strong>
    </div>
  </div>
  @endfor
</div>
</div>

@endsection
