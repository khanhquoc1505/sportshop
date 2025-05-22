function toggleFilter(element) {
      const options = element.nextElementSibling;
      const icon = element.querySelector('span');
      if (options.style.display === 'block') {
        options.style.display = 'none';
        icon.textContent = '+';
      } else {
        options.style.display = 'block';
        icon.textContent = '-';
      }
    }