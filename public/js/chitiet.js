function showTab(tabId) {
  const tabs = document.querySelectorAll(".ct-tab-content");
  const buttons = document.querySelectorAll(".ct-tab-btn");
  tabs.forEach(tab => tab.classList.remove("active"));
  buttons.forEach(btn => btn.classList.remove("active"));

  document.getElementById(tabId).classList.add("active");
  event.target.classList.add("active");
}
const btn = document.getElementById('showMoreBtn');
  btn.addEventListener('click', () => {
    document.querySelectorAll('.hidden-review').forEach(el => {
      el.style.display = 'block';
    });
    btn.style.display = 'none'; // Ẩn nút sau khi bấm
  });