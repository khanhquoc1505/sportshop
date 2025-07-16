document.addEventListener('DOMContentLoaded', () => {
  // Dữ liệu variants đã được gán lên window
  const variants     = window.productVariants || [];
  const mainImage    = document.getElementById('main-image');
  const thumbnails   = Array.from(document.querySelectorAll('.thumbnail'));
  const swatches     = Array.from(document.querySelectorAll('.color-swatch'));
  const sizeBtns     = Array.from(document.querySelectorAll('.size-btn'));

  // Form inputs
  const colorCartIn   = document.getElementById('selected-color');
  const sizeCartIn    = document.getElementById('selected-size');
  const imageCartIn   = document.getElementById('selected-image');
  const qtyCartIn     = document.getElementById('form-qty');

  const colorBuyIn    = document.getElementById('selected-color-buynow');
  const sizeBuyIn     = document.getElementById('selected-size-buynow');
  const qtyBuyIn      = document.getElementById('form-qty-buynow');
  const buyNowForm    = document.getElementById('ct-buy-now-form');
  const buyNowBtn     = document.getElementById('ct-buy-now-btn');

  const decBtn        = document.getElementById('qty-decrease');
  const incBtn        = document.getElementById('qty-increase');
  const qtyDisplay    = document.getElementById('qty');

  // Helper: cập nhật tất cả hidden inputs dựa trên current selections & qty
  function updateForms() {
    const qty  = parseInt(qtyDisplay.value, 10) || 1;
    const color = colorCartIn.value;
    const size  = sizeCartIn.value;
    const img   = imageCartIn.value;

    qtyCartIn.value  = qty;
    qtyBuyIn.value   = qty;
    colorBuyIn.value = color;
    sizeBuyIn.value  = size;
  }

  // Ẩn/hiện size theo color & stock, rồi chọn size đầu
  function refreshSizes(colorId) {
    sizeBtns.forEach(btn => {
      const sz = btn.dataset.size;
      const v  = variants.find(x => x.color_id === Number(colorId) && x.size === sz);
      if (!v || v.stock === 0) {
        btn.style.display = 'none';
      } else {
        btn.style.display = 'inline-block';
      }
      btn.classList.remove('active');
    });

    // chọn size đầu tiên còn hàng
    const first = sizeBtns.find(b => b.style.display !== 'none');
    if (first) {
      first.classList.add('active');
      sizeCartIn.value = first.dataset.size;
    } else {
      sizeCartIn.value = '';
    }
    updateForms();
  }

  // Lọc thumbnails theo color, click tự động cái đầu
  function refreshThumbnails(colorId, fallbackImage) {
  let firstThumb = null;
  let count = 0;
  thumbnails.forEach(thumb => {
    // luôn remove active trước
    thumb.classList.remove('active');

    if (thumb.dataset.colorId === colorId && count < 4) {
      // chỉ hiển thị tối đa 4 ảnh đầu
      thumb.style.display = 'inline-block';
      if (!firstThumb) firstThumb = thumb;
      count++;
    } else {
      thumb.style.display = 'none';
    }
  });

  if (firstThumb) {
    firstThumb.click();
  } else if (fallbackImage) {
    mainImage.src = fallbackImage;
    imageCartIn.value = fallbackImage.split('/').pop();
    updateForms();
  }
}

  // === XỬ LÝ CHỌN MÀU ===
  swatches.forEach(swatch => {
    swatch.addEventListener('click', () => {
      swatches.forEach(s => s.classList.remove('active'));
      swatch.classList.add('active');

      const colorId = swatch.dataset.colorId;
      const imgUrl  = swatch.dataset.image; // full URL

      // Cập nhật color ở hidden inputs
      colorCartIn.value = colorId;
      colorBuyIn.value  = colorId;
      imageCartIn.value = imgUrl.split('/').pop();
      updateForms();

      // Refresh thumbnails & sizes
      refreshThumbnails(colorId, imgUrl);
      refreshSizes(colorId);
    });
  });

  // === XỬ LÝ CLICK THUMBNAIL ===
  thumbnails.forEach(thumb => {
    thumb.addEventListener('click', () => {
      thumbnails.forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
      mainImage.src = thumb.src;
      imageCartIn.value  = thumb.src.split('/').pop();
      updateForms();
    });
  });

  // === XỬ LÝ CHỌN SIZE ===
  sizeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      if (btn.style.display === 'none') return;
      sizeBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      sizeCartIn.value = btn.dataset.size;
      updateForms();
    });
  });

  // === XỬ LÝ SỐ LƯỢNG ===
  decBtn.addEventListener('click', () => {
    let v = Math.max(1, parseInt(qtyDisplay.value, 10) - 1);
    qtyDisplay.value = v;
    updateForms();
  });
  incBtn.addEventListener('click', () => {
    const colorId = Number(colorCartIn.value);
    const size    = sizeCartIn.value;
    const v = variants.find(x => x.color_id === colorId && x.size === size);
    const maxStock = v ? v.stock : 1;

    let q = parseInt(qtyDisplay.value, 10) || 1;
    if (q < maxStock) q++;
    qtyDisplay.value = q;
    updateForms();
  });

  // === XỬ LÝ MUA NGAY ===
  if (buyNowBtn && buyNowForm) {
    buyNowBtn.addEventListener('click', () => {
      updateForms();
      buyNowForm.submit();
    });
  }

  // --- Khởi tạo lần đầu: click swatch active để load ảnh & sizes ---
  document.querySelector('.color-swatch.active')?.click();
});
window.showTab = function(tabId) {
  // 1) Ẩn hết nội dung
  document.querySelectorAll('.ct-tab-content').forEach(c => c.classList.remove('active'));
  // 2) Bỏ active trên tất cả nút
  document.querySelectorAll('.ct-tab-btn').forEach(b => b.classList.remove('active'));
  // 3) Hiện nội dung và active nút tương ứng
  const panel = document.getElementById(tabId);
  if (panel) panel.classList.add('active');
  const btn = document.querySelector(`.ct-tab-btn[onclick="showTab('${tabId}')"]`);
  if (btn) btn.classList.add('active');
};