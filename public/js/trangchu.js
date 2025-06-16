const swiper = new Swiper('.swiper', {
      loop: true,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
    });

    const tabs = document.querySelectorAll('.tab-btn');
  const contents = document.querySelectorAll('.tab-product-content');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      document.getElementById(tab.dataset.tab).classList.add('active');
    });
  });

  function toggleUserMenu() {
    const menu = document.getElementById('userDropdown');
    menu.classList.toggle('hidden');
}

// Đóng menu nếu click ra ngoài
document.addEventListener('click', function(e) {
    const menu = document.getElementById('userDropdown');
    const button = document.querySelector('.icon-btn');
    if (!menu.contains(e.target) && !button.contains(e.target)) {
        menu.classList.add('hidden');
    }
});