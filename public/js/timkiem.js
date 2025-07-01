
  function toggleFilter(el) {
    const opts = el.nextElementSibling;
    const sign = el.querySelector('span');
    if (opts.style.display === 'block') {
      opts.style.display = 'none'; sign.textContent = '+';
    } else {
      opts.style.display = 'block'; sign.textContent = '–';
    }
  }
document.addEventListener('DOMContentLoaded', function(){
  const input = document.getElementById('search-input');
  const box   = document.getElementById('search-suggestions');
  if (!input || !box) return;

  const endpoint = input.dataset.url;
  let timer;

  input.addEventListener('input', function(){
    clearTimeout(timer);
    const q = this.value.trim();
    if (q === '') {
      box.style.display = 'none';
      return;
    }
    // debounce 200ms
    timer = setTimeout(() => {
      fetch(`${endpoint}?q=${encodeURIComponent(q)}`)
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(items => {
          if (!items.length) {
            box.innerHTML = '<div class="no-result">Không tìm thấy</div>';
          } else {
            box.innerHTML = items.map(item => `
              <div class="suggestion-item" data-url="${item.url}">
                <img src="${item.img}" alt="${item.ten}" />
                <div class="info">
                  <span class="name">${item.ten}</span>
                  <span class="price">${item.gia} đ</span>
                </div>
              </div>
            `).join('');
            box.querySelectorAll('.suggestion-item').forEach(el =>
              el.addEventListener('click', ()=>
                window.location.href = el.dataset.url
              )
            );
          }
          box.style.display = 'block';
        })
        .catch(console.error);
    }, 200);
  });

  // Click ngoài ẩn dropdown
  document.addEventListener('click', e => {
    if (!input.contains(e.target) && !box.contains(e.target)) {
      box.style.display = 'none';
    }
  });
});