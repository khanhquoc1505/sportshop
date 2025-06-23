

  document.getElementById('toggle-voucher-list').addEventListener('click', () => {
    const list = document.getElementById('voucher-list');
    const caret = document.getElementById('voucher-caret');
    if (list.style.display === 'none') {
      list.style.display = 'block';
      caret.textContent = '▲';
    } else {
      list.style.display = 'none';
      caret.textContent = '▼';
    }
  });

