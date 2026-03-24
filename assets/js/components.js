const injectComponent = async (selector, path) => {
  const target = document.querySelector(selector);
  if (!target) return;

  try {
    const response = await fetch(path);
    if (!response.ok) throw new Error(`Failed to load ${path}`);
    target.innerHTML = await response.text();
  } catch (error) {
    console.error(error);
  }
};

const loadSharedComponents = async () => {
  await injectComponent('#navbar-placeholder', './components/navbar.html');
  await injectComponent('#footer-placeholder', './components/footer.html');

  const yearEl = document.getElementById('currentYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  document.dispatchEvent(new CustomEvent('components:loaded'));
};

document.addEventListener('DOMContentLoaded', loadSharedComponents);