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
window.openModal = function (id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.add('active');
  document.body.style.overflow = 'hidden';
};

window.closeModal = function (id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('active');
  if (!document.querySelector('.modal.active')) {
    document.body.style.overflow = '';
  }
};

/* Close on Escape key */
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    const modals = document.querySelectorAll('.modal.active');
    if (modals.length) {
      const last = modals[modals.length - 1];
      if (last.id !== 'modal-confirm') {
        last.classList.remove('active');
        if (!document.querySelector('.modal.active')) document.body.style.overflow = '';
      }
    }
  }
});

/* Close on backdrop click */
document.addEventListener('click', e => {
  if (e.target.classList.contains('modal') && e.target.classList.contains('active')) {
    if (e.target.id !== 'modal-confirm') {
      e.target.classList.remove('active');
      if (!document.querySelector('.modal.active')) document.body.style.overflow = '';
    }
  }
});

/* Flash messages */
document.addEventListener('DOMContentLoaded', () => {
  if (window.__flash) showToast(window.__flash.msg, window.__flash.type === 'success' ? 'success' : 'error');
});

/* ================================================================
   CONFIRM MODAL — replaces browser confirm() for data-confirm forms
   ================================================================ */
(function () {
  function injectConfirmModal() {
    if (document.getElementById('modal-confirm')) return;
    const el = document.createElement('div');
    el.className = 'modal';
    el.id = 'modal-confirm';
    el.innerHTML = `
      <div class="modal-dialog modal-dialog-confirm">
        <div class="confirm-body">
          <div class="confirm-icon danger" id="confirm-icon">🗑️</div>
          <div class="confirm-title" id="confirm-title">Hapus Data?</div>
          <div class="confirm-desc"  id="confirm-desc">Tindakan ini tidak dapat dibatalkan.</div>
        </div>
        <div class="confirm-footer">
          <button type="button" class="btn btn-outline" id="confirm-cancel">Batal</button>
          <button type="button" class="btn btn-danger"  id="confirm-ok">Ya, Hapus</button>
        </div>
      </div>`;
    document.body.appendChild(el);
    document.getElementById('confirm-cancel').addEventListener('click', closeConfirmModal);
  }

  let _pendingForm = null;

  function openConfirmModal(form) {
    injectConfirmModal();
    _pendingForm = form;

    const msg    = form.dataset.confirm       || 'Yakin ingin melanjutkan?';
    const icon   = form.dataset.confirmIcon   || '🗑️';
    const title  = form.dataset.confirmTitle  || 'Konfirmasi';
    const btnTxt = form.dataset.confirmBtn    || 'Ya, Hapus';
    const btnCls = form.dataset.confirmClass  || 'btn btn-danger';

    document.getElementById('confirm-icon').textContent  = icon;
    document.getElementById('confirm-title').textContent = title;
    document.getElementById('confirm-desc').textContent  = msg;

    // Set icon background based on button type
    const iconEl = document.getElementById('confirm-icon');
    iconEl.className = 'confirm-icon';
    if (btnCls.includes('btn-success'))      iconEl.classList.add('success');
    else if (btnCls.includes('btn-warning')) iconEl.classList.add('warning');
    else                                     iconEl.classList.add('danger');

    const okBtn = document.getElementById('confirm-ok');
    okBtn.textContent = btnTxt;
    okBtn.className   = btnCls;
    okBtn.onclick = () => {
      closeConfirmModal();
      form.dataset._confirmed = '1';
      if (form.requestSubmit) form.requestSubmit();
      else form.submit();
    };

    openModal('modal-confirm');
  }

  function closeConfirmModal() {
    closeModal('modal-confirm');
    _pendingForm = null;
  }

  document.addEventListener('submit', e => {
    const form = e.target;
    if (!form.dataset.confirm) return;
    if (form.dataset._confirmed === '1') {
      delete form.dataset._confirmed;
      return;
    }
    e.preventDefault();
    openConfirmModal(form);
  });
})();