// <!-- JS ẩn/hiện form đánh giá -->

  document.querySelectorAll('.btn-show-review').forEach(btn => {
    btn.addEventListener('click', () => {
      const pid = btn.dataset.productId;
      // ẩn hết form
      document.querySelectorAll('.order-detail-review')
              .forEach(f => f.style.display = 'none');
      // ẩn nút này
      btn.style.display = 'none';
      // show đúng form
      document.getElementById(`review-form-${pid}`)
              .style.display = 'block';
    });
  });

