
  function toggleFilter(el) {
    const opts = el.nextElementSibling;
    const sign = el.querySelector('span');
    if (opts.style.display === 'block') {
      opts.style.display = 'none'; sign.textContent = '+';
    } else {
      opts.style.display = 'block'; sign.textContent = '–';
    }
  }
document.addEventListener('DOMContentLoaded', () => {
  const input    = document.getElementById('search-input');
  const box      = document.getElementById('search-suggestions');
  if (!input || !box) return;

  const endpoint = input.dataset.url;
  let timer;

  input.addEventListener('input', () => {
    clearTimeout(timer);
    const q = input.value.trim();
    if (!q) {
      box.style.display = 'none';
      return;
    }

    timer = setTimeout(() => {
      fetch(`${endpoint}?q=${encodeURIComponent(q)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(r => {
        if (!r.ok) {
          return r.text().then(txt => {
            console.error('Server error HTML:', txt);
            throw new Error(`Status ${r.status}`);
          });
        }
        return r.json();
      })
      .then(items => {
        if (!items.length) {
          box.innerHTML = '<div class="no-result">Không tìm thấy</div>';
        } else {
          console.log(items);
          box.innerHTML = items.map(item => `
            <div class="suggestion-item" data-url="${item.url}">
              <img src="${item.img}" alt="${item.ten}">
              <div class="info">
                <span class="name">${item.ten}</span>
                <span class="price">${item.gia} ₫</span>
              </div>
            </div>
          `).join('');
          box.querySelectorAll('.suggestion-item').forEach(el =>
            el.addEventListener('click', () =>
              window.location.href = el.dataset.url
            )
          );
        }
        box.style.display = 'block';
      })
      .catch(err => {
        console.error('Autocomplete failed:', err);
        box.style.display = 'none';
      });
    }, 200);
  });

  document.addEventListener('click', e => {
    if (!input.contains(e.target) && !box.contains(e.target)) {
      box.style.display = 'none';
    }
  });
});



document.querySelectorAll('.filter-title').forEach(el => {
    el.addEventListener('click', () => {
      el.closest('.filter-section').classList.toggle('open');
    });
  });