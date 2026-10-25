(() => {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('menuToggle');
  const scrim = document.getElementById('mobileScrim');

  function setMenu(open) {
    if (!sidebar || !toggle || !scrim) return;
    sidebar.classList.toggle('is-open', open);
    scrim.classList.toggle('is-visible', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.body.style.overflow = open ? 'hidden' : '';
  }

  if (toggle) {
    toggle.addEventListener('click', () => {
      setMenu(!sidebar.classList.contains('is-open'));
    });
  }
  if (scrim) scrim.addEventListener('click', () => setMenu(false));
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') setMenu(false);
  });
  window.addEventListener('resize', () => {
    if (window.innerWidth > 800) setMenu(false);
  });
})();
