/* ================================================================
   SATRIA WISATA TRANSPORT — Global JS
   ================================================================ */

/* ---- Sidebar Toggle ---- */
(function () {
  const sidebar  = document.getElementById('sidebar');
  const overlay  = document.getElementById('sidebar-overlay');
  const btnOpen  = document.getElementById('btn-sidebar-open');
  const btnClose = document.getElementById('btn-sidebar-close');

  function openSidebar()  { sidebar?.classList.add('open');  overlay?.classList.add('active'); }
  function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('active'); }

  btnOpen?.addEventListener('click',  openSidebar);
  btnClose?.addEventListener('click', closeSidebar);
  overlay?.addEventListener('click',  closeSidebar);

  const path = location.pathname;
  document.querySelectorAll('.nav-link').forEach(a => {
    if (path.startsWith(a.getAttribute('href') || '__')) a.classList.add('active');
  });

  document.querySelectorAll('.nav-group-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const group = btn.closest('.nav-group');
      group?.classList.toggle('open');
    });
  });
})();

/* ---- Toast ---- */
window.showToast = function (msg, type = 'info') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }
  const icons = { success: '✓', error: '✕', info: 'ℹ', warning: '⚠' };
  const toast = document.createElement('div');
  toast.className = 'toast ' + type;
  toast.innerHTML = '<span>' + (icons[type] || 'ℹ') + '</span><span>' + msg + '</span>';
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = 'toastOut .3s ease forwards';
    setTimeout(() => toast.remove(), 300);
  }, 3500);
};

/* ---- Modal helpers ---- */
window.openModal  = function(id) { document.getElementById(id)?.classList.add('active'); };
window.closeModal = function(id) { document.getElementById(id)?.classList.remove('active'); };

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') document.querySelectorAll('.modal.active').forEach(m => m.classList.remove('active'));
});

document.addEventListener('click', e => {
  if (e.target.classList.contains('modal')) e.target.classList.remove('active');
});

document.addEventListener('DOMContentLoaded', () => {
  if (window.__flash) showToast(window.__flash.msg, window.__flash.type === 'success' ? 'success' : 'error');
});

document.addEventListener('submit', e => {
  const form = e.target;
  if (form.dataset.confirm) {
    if (!confirm(form.dataset.confirm)) e.preventDefault();
  }
});