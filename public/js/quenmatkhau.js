 const overlay = document.getElementById('popup-overlay');

  const popup1 = document.getElementById('popup-step1');
  const popup2 = document.getElementById('popup-step2');

  const forgotBtn = document.getElementById('forgot-link');
  const nextStepBtn = document.getElementById('next-step');
  const cancel1 = document.getElementById('cancel-popup1');
  const cancel2 = document.getElementById('cancel-popup2');

  // Hiển thị popup bước 1
  forgotBtn.addEventListener('click', function(e) {
    e.preventDefault();
    popup1.style.display = 'block';
    overlay.style.display = 'block';
  });

  // Chuyển sang bước 2
  nextStepBtn.addEventListener('click', function() {
    popup1.style.display = 'none';
    popup2.style.display = 'block';
  });

  // Đóng popup bước 1
  cancel1.addEventListener('click', function() {
    popup1.style.display = 'none';
    overlay.style.display = 'none';
  });

  // Đóng popup bước 2
  cancel2.addEventListener('click', function() {
    popup2.style.display = 'none';
    overlay.style.display = 'none';
  });

  // Đóng popup khi click nền mờ
  overlay.addEventListener('click', function() {
    popup1.style.display = 'none';
    popup2.style.display = 'none';
    overlay.style.display = 'none';
  });