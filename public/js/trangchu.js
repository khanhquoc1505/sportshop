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
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}

document.addEventListener('click', function (e) {
    const menu = document.getElementById('userDropdown');
    const button = document.querySelector('.icon-btn-user');
    if (menu && !menu.contains(e.target) && !button.contains(e.target)) {
        menu.style.display = 'none';
    }
});



