
  function toggleFilter(el) {
    const opts = el.nextElementSibling;
    const sign = el.querySelector('span');
    if (opts.style.display === 'block') {
      opts.style.display = 'none'; sign.textContent = '+';
    } else {
      opts.style.display = 'block'; sign.textContent = '–';
    }
  }
