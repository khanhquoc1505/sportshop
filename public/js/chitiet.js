document.addEventListener('DOMContentLoaded', () => {
  const mainImage   = document.getElementById('main-image');
  const thumbnails  = Array.from(document.querySelectorAll('.thumbnail'));
  const swatches    = Array.from(document.querySelectorAll('.color-swatch'));
  const colorInput  = document.getElementById('selected-color');
  const imageInput  = document.getElementById('selected-image');
  const sizeBtns    = Array.from(document.querySelectorAll('.size-btn'));
  const sizeInput   = document.getElementById('selected-size');
  const decBtn      = document.getElementById('qty-decrease');
  const incBtn      = document.getElementById('qty-increase');
  const qtyInput    = document.getElementById('qty');
  const formQty     = document.getElementById('form-qty');
  const tabs        = document.querySelectorAll(".ct-tab-content");
  const buttons     = document.querySelectorAll(".ct-tab-btn");
  const btnShowMore = document.getElementById('showMoreBtn');

  // === TABS ===
  buttons.forEach(btn => {
    btn.addEventListener('click', function () {
      tabs.forEach(t => t.classList.remove("active"));
      buttons.forEach(b => b.classList.remove("active"));
      const tabId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
      document.getElementById(tabId).classList.add("active");
      this.classList.add("active");
    });
  });

  // === SHOW MORE REVIEWS ===
  if (btnShowMore) {
    btnShowMore.addEventListener('click', () => {
      document.querySelectorAll('.hidden-review').forEach(el => el.style.display = 'block');
      btnShowMore.style.display = 'none';
    });
  }

  // === TĂNG GIẢM SỐ LƯỢNG ===
  if (decBtn && incBtn && qtyInput && formQty) {
    decBtn.addEventListener('click', () => {
      let v = Math.max(1, (parseInt(qtyInput.value) || 1) - 1);
      qtyInput.value = formQty.value = v;
    });
    incBtn.addEventListener('click', () => {
      let v = (parseInt(qtyInput.value) || 1) + 1;
      qtyInput.value = formQty.value = v;
    });
  }

  // === CHỌN SIZE ===
  sizeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      // 1) Active class
      sizeBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      // 2) Ghi value vào input hidden
      if (sizeInput) {
        sizeInput.value = btn.dataset.size;
      }
    });
  });
  // Mặc định chọn size đầu tiên khi load
  if (sizeBtns.length && sizeInput && sizeInput.value === '') {
    sizeBtns[0].click();
  }

  // === CLICK THUMBNAIL ===
  thumbnails.forEach(thumb => {
    thumb.addEventListener('click', () => {
      thumbnails.forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
      mainImage.src    = thumb.src;
      imageInput.value = thumb.src.split('/').pop();
    });
  });

  // === CLICK SWATCH ===
  swatches.forEach(swatch => {
    swatch.addEventListener('click', () => {
      swatches.forEach(s => s.classList.remove('active'));
      swatch.classList.add('active');

      const colorId = swatch.dataset.colorId;
      const fallback = swatch.dataset.image;
      colorInput.value = colorId;

      let first = null;
      thumbnails.forEach(thumb => {
        if (thumb.dataset.colorId === colorId) {
          thumb.style.display = 'inline-block';
          if (!first) first = thumb;
        } else {
          thumb.style.display = 'none';
        }
        thumb.classList.remove('active');
      });

      if (first) {
        first.click();
      } else {
        mainImage.src    = fallback;
        imageInput.value = fallback.split('/').pop();
      }
    });
  });

  // === Khởi tạo: click swatch active lần đầu ===
  document.querySelector('.color-swatch.active')?.click();
});
