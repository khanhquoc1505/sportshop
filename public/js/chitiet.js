document.addEventListener('DOMContentLoaded', () => {
  // === TABS ===
  const tabs = document.querySelectorAll(".ct-tab-content");
  const buttons = document.querySelectorAll(".ct-tab-btn");
  buttons.forEach(btn => {
    btn.addEventListener('click', function () {
      tabs.forEach(tab => tab.classList.remove("active"));
      buttons.forEach(b => b.classList.remove("active"));
      const tabId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
      document.getElementById(tabId).classList.add("active");
      this.classList.add("active");
    });
  });

  // === SHOW MORE REVIEWS ===
  const btnShowMore = document.getElementById('showMoreBtn');
  if (btnShowMore) {
    btnShowMore.addEventListener('click', () => {
      document.querySelectorAll('.hidden-review').forEach(el => {
        el.style.display = 'block';
      });
      btnShowMore.style.display = 'none';
    });
  }

  // === TĂNG GIẢM SỐ LƯỢNG ===
  const decBtn   = document.getElementById('qty-decrease');
  const incBtn   = document.getElementById('qty-increase');
  const qtyInput = document.getElementById('qty');
  const formQty  = document.getElementById('form-qty');

  if (decBtn && incBtn && qtyInput && formQty) {
    decBtn.addEventListener('click', () => {
      let v = parseInt(qtyInput.value) || 1;
      v = Math.max(1, v - 1);
      qtyInput.value = formQty.value = v;
    });
    incBtn.addEventListener('click', () => {
      let v = parseInt(qtyInput.value) || 1;
      qtyInput.value = formQty.value = v + 1;
    });
  }

  // === CHỌN SIZE ===
  const sizeBtns = document.querySelectorAll('.size-btn');
  sizeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      sizeBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  // === CLICK THUMBNAIL ===
  const mainImage = document.getElementById('main-image');
  document.querySelectorAll('.thumbnail').forEach(img => {
    img.addEventListener('click', e => {
      mainImage.src = e.currentTarget.src;
      document.querySelectorAll('.thumbnail').forEach(i => i.classList.remove('active'));
      e.currentTarget.classList.add('active');
    });
  });

  // === CLICK MÀU (SWATCH) ===
  document.querySelectorAll('.color-swatch').forEach(swatch => {
    swatch.addEventListener('click', () => {
      const url = swatch.dataset.image;
      mainImage.src = url;
      document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
      swatch.classList.add('active');
    });
  });
});