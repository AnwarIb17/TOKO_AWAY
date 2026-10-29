/* =====================================================================
   AWAY FOTOCOPY — UI helper
   ===================================================================== */
(function () {
  'use strict';

  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  /* ---------------- Sidebar (mobile) ---------------- */
  const sidebar = $('#sidebar');
  const sidebarOverlay = $('#sidebar-overlay');

  function toggleSidebar(force) {
    if (!sidebar) return;
    const open = typeof force === 'boolean' ? force : !sidebar.classList.contains('open');
    sidebar.classList.toggle('open', open);
    sidebarOverlay?.classList.toggle('hidden', !open);
    document.body.classList.toggle('overflow-hidden', open);
  }

  $('#sidebar-toggle')?.addEventListener('click', () => toggleSidebar());
  sidebarOverlay?.addEventListener('click', () => toggleSidebar(false));

  /* ---------------- Modal ---------------- */
  window.openModal = function (id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('open');
    document.body.classList.add('overflow-hidden');
    const focusable = modal.querySelector('[autofocus], input, select, textarea, button');
    setTimeout(() => focusable?.focus(), 120);
  };

  window.closeModal = function (id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('open');
    if (!$$('.modal.open').length) {
      document.body.classList.remove('overflow-hidden');
    }
  };

  document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-modal-open]');
    if (opener) {
      event.preventDefault();
      const modalId = opener.getAttribute('data-modal-open');
      const title = opener.getAttribute('data-title');
      if (title) {
        const modal = document.getElementById(modalId);
        const titleEl = modal?.querySelector('[data-modal-title]');
        if (titleEl) titleEl.textContent = title;
      }
      window.openModal(modalId);
      return;
    }

    const closer = event.target.closest('[data-modal-close]');
    if (closer) {
      event.preventDefault();
      const modal = closer.closest('.modal');
      if (modal) window.closeModal(modal.id);
      return;
    }

    if (event.target.classList.contains('modal-backdrop')) {
      const modal = event.target.closest('.modal');
      if (modal) window.closeModal(modal.id);
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    const open = $$('.modal.open').pop();
    if (open) {
      window.closeModal(open.id);
    } else {
      toggleSidebar(false);
    }
  });

  /* ---------------- Confirm submit ---------------- */
  document.addEventListener('submit', (event) => {
    const form = event.target;
    const message = form.getAttribute('data-confirm');
    if (message && !window.confirm(message)) {
      event.preventDefault();
    }
  });

  /* ---------------- Toast auto close ---------------- */
  $$('.toast').forEach((toast, index) => {
    setTimeout(() => {
      toast.style.transition = 'opacity .4s ease, transform .4s ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-8px)';
      setTimeout(() => toast.remove(), 450);
    }, 4500 + index * 300);
  });

  /* ---------------- Clock ---------------- */
  const clock = $('#clock');
  if (clock) {
    const tick = () => {
      const now = new Date();
      const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      const pad = (n) => String(n).padStart(2, '0');
      clock.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} · ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    };
    tick();
    setInterval(tick, 1000);
  }

  /* ---------------- Currency input formatting ---------------- */
  $$('[data-money]').forEach((input) => {
    const format = () => {
      const digits = input.value.replace(/\D/g, '');
      input.value = digits ? new Intl.NumberFormat('id-ID').format(Number(digits)) : '';
    };
    input.addEventListener('input', format);
  });

  /* ---------------- Auto fill modal forms ---------------- */
  document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-form-fill]');
    if (!trigger) return;

    const targetId = trigger.getAttribute('data-form-fill');
    const form = document.getElementById(targetId);
    if (!form) return;

    let payload = {};
    try {
      payload = JSON.parse(trigger.getAttribute('data-payload') || '{}');
    } catch (error) {
      payload = {};
    }

    Object.entries(payload).forEach(([key, value]) => {
      const field = form.elements.namedItem(key);
      if (!field) return;
      if (field.type === 'checkbox') {
        field.checked = Boolean(Number(value));
      } else {
        field.value = value ?? '';
      }
    });

    const title = trigger.getAttribute('data-title');
    const titleEl = form.querySelector('[data-modal-title]');
    if (title && titleEl) titleEl.textContent = title;

    if (targetId === 'stock-form') {
      const qtyField = form.elements.namedItem('qty');
      if (qtyField) qtyField.value = '1';
      const noteField = form.elements.namedItem('note');
      if (noteField) noteField.value = '';
      const nameEl = document.getElementById('stock-product-name');
      if (nameEl) nameEl.textContent = payload.name || '—';
    }
  });

  /* ---------------- Reset forms on open ---------------- */
  document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-modal-reset]');
    if (!trigger) return;
    const form = document.getElementById(trigger.getAttribute('data-modal-reset'));
    if (!form) return;
    form.reset();
    const idField = form.elements.namedItem('id');
    if (idField) idField.value = '0';
    const activeField = form.elements.namedItem('is_active');
    if (activeField) activeField.checked = true;
  });
})();
