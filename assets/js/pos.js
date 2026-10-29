/* =====================================================================
   AWAY FOTOCOPY — Kasir (POS)
   ===================================================================== */
(function () {
  'use strict';

  const root = document.getElementById('pos-root');
  if (!root) return;

  const cfg = window.APP || { base: '', csrf: '' };
  const taxPercent = Number(root.dataset.tax || 0);
  const rp = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(Number(value) || 0));
  const nf = new Intl.NumberFormat('id-ID');
  const esc = (value) =>
    String(value ?? '').replace(/[&<>"']/g, (char) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[char]));

  /* ----------------------------- Elements ----------------------------- */
  const searchInput = document.getElementById('pos-search');
  const productGrid = document.getElementById('product-grid');
  const cards = Array.from(productGrid.querySelectorAll('[data-product]'));
  const cartEl = document.getElementById('cart-items');
  const cartCount = document.getElementById('cart-count');
  const clearBtn = document.getElementById('cart-clear');
  const messageEl = document.getElementById('cart-message');
  const discountInput = document.getElementById('discount-input');
  const discountType = document.getElementById('discount-type');
  const customerInput = document.getElementById('customer-input');
  const paidInput = document.getElementById('paid-input');
  const payBtn = document.getElementById('pay-button');
  const payLabel = document.getElementById('pay-label');
  const payTotal = document.getElementById('pay-total');
  const changeEl = document.getElementById('sum-change');
  const subtotalEl = document.getElementById('sum-subtotal');
  const taxEl = document.getElementById('sum-tax');
  const totalEl = document.getElementById('sum-total');
  const mobileBar = document.getElementById('mobile-cart-bar');
  const mobileCount = document.getElementById('mobile-cart-count');
  const mobileTotal = document.getElementById('mobile-cart-total');

  const printForm = document.getElementById('print-form');
  const serviceSelect = document.getElementById('print-service');
  const pagesInput = document.getElementById('print-pages');
  const copiesInput = document.getElementById('print-copies');
  const printTotalEl = document.getElementById('print-total');
  const printAddBtn = document.getElementById('print-add');

  const state = { items: [], busy: false };
  let activeCategory = 'all';
  let messageTimer = null;

  /* ----------------------------- Helpers ----------------------------- */
  function notify(text, tone = 'error') {
    if (!messageEl) return;
    messageEl.textContent = text;
    messageEl.classList.remove('hidden', 'border-red-400/25', 'bg-red-500/10', 'text-red-300', 'border-gold/25', 'bg-gold/10', 'text-gold');
    if (tone === 'info') {
      messageEl.classList.add('border-gold/25', 'bg-gold/10', 'text-gold');
    } else {
      messageEl.classList.add('border-red-400/25', 'bg-red-500/10', 'text-red-300');
    }
    clearTimeout(messageTimer);
    messageTimer = setTimeout(() => messageEl.classList.add('hidden'), 4000);
  }

  function clearMessage() {
    messageEl?.classList.add('hidden');
  }

  function parseProduct(card) {
    try {
      return JSON.parse(card.dataset.product || '{}');
    } catch (error) {
      return {};
    }
  }

  /* ----------------------------- Cart ----------------------------- */
  function addProduct(product) {
    if (!product.id) return;
    const existing = state.items.find((item) => item.type === 'product' && item.id === product.id);

    if (existing) {
      if (existing.qty + 1 > existing.stock) {
        notify(`Stok "${product.name}" hanya ${existing.stock}.`);
        return;
      }
      existing.qty += 1;
    } else {
      if (Number(product.stock) <= 0) {
        notify(`Stok "${product.name}" habis.`);
        return;
      }
      state.items.push({
        key: 'p' + product.id,
        type: 'product',
        id: Number(product.id),
        name: product.name,
        unit: product.unit || 'pcs',
        unit_price: Number(product.price) || 0,
        stock: Number(product.stock) || 0,
        qty: 1,
      });
    }

    clearMessage();
    renderCart();
  }

  function renderCart() {
    const totalQty = state.items.reduce((sum, item) => sum + item.qty, 0);
    cartCount.textContent = `${state.items.length} item`;

    if (state.items.length === 0) {
      cartEl.innerHTML = `
        <div class="flex flex-col items-center justify-center py-12 text-center">
          <div class="flex h-12 w-12 items-center justify-center rounded-full border border-line">
            <svg class="h-5 w-5 text-muted/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M3 4h2l2.4 11.2a1 1 0 0 0 1 .8h8.8a1 1 0 0 0 1-.8L20 7H6"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
          </div>
          <p class="mt-3 font-display text-lg text-ivory/70">Keranjang kosong</p>
          <p class="mt-1 max-w-[190px] text-[11px] leading-relaxed text-muted/60">Pilih produk di sebelah kiri atau tambahkan jasa cetak.</p>
        </div>`;
      recalc();
      return;
    }

    cartEl.innerHTML = state.items.map((item) => {
      const lineTotal = item.unit_price * item.qty;
      const isPrint = item.type === 'print';

      const metaLine = isPrint
        ? `${item.pages} hlm × ${item.copies} copy${item.finishing_names.length ? ' · ' + esc(item.finishing_names.join(', ')) : ''}`
        : `${rp(item.unit_price)} / ${esc(item.unit)}`;

      const qtyControls = isPrint
        ? `<span class="badge badge-ivory">${nf.format(item.qty)} lembar</span>`
        : `<div class="flex items-center gap-1.5">
             <button type="button" class="qty-btn" data-action="dec" aria-label="Kurangi">−</button>
             <span class="w-6 text-center text-[12px] tabular-nums text-ivory">${item.qty}</span>
             <button type="button" class="qty-btn" data-action="inc" aria-label="Tambah">+</button>
           </div>`;

      return `
        <div class="cart-row" data-key="${esc(item.key)}">
          <div class="min-w-0 flex-1">
            <p class="truncate text-[12.5px] font-medium text-ivory/90">${esc(item.name)}</p>
            <p class="mt-0.5 truncate text-[10px] text-muted">${metaLine}</p>
            <div class="mt-1.5">${qtyControls}</div>
          </div>
          <div class="flex shrink-0 flex-col items-end gap-1.5">
            <span class="text-[12px] tabular-nums text-ivory/85">${rp(lineTotal)}</span>
            <button type="button" class="text-muted transition-colors hover:text-red-300" data-action="remove" aria-label="Hapus">
              <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
          </div>
        </div>`;
    }).join('');

    recalc();
  }

  function calcTotals() {
    const subtotal = state.items.reduce((sum, item) => sum + item.unit_price * item.qty, 0);
    const rawDiscount = Math.max(0, Number(discountInput.value) || 0);
    const discount = discountType.value === 'percent'
      ? Math.round(subtotal * Math.min(100, rawDiscount) / 100)
      : Math.min(subtotal, Math.round(rawDiscount));
    const taxable = Math.max(0, subtotal - discount);
    const tax = Math.round(taxable * taxPercent / 100);
    const total = taxable + tax;

    return { subtotal, discount, tax, total };
  }

  function paidValue() {
    return Number(String(paidInput.value || '').replace(/\D/g, '')) || 0;
  }

  function recalc() {
    const { subtotal, tax, total } = calcTotals();
    const paid = paidValue();
    const change = paid - total;

    subtotalEl.textContent = rp(subtotal);
    if (taxEl) taxEl.textContent = rp(tax);
    totalEl.textContent = rp(total);
    payTotal.textContent = rp(total);

    if (mobileBar) {
      const hasItems = state.items.length > 0;
      mobileBar.classList.toggle('hidden', !hasItems);
      mobileBar.classList.toggle('flex', hasItems);
      mobileCount.textContent = `${state.items.length} item`;
      mobileTotal.textContent = rp(total);
    }

    if (change > 0) {
      changeEl.textContent = rp(change);
      changeEl.classList.add('text-gold');
      changeEl.classList.remove('text-ivory/80', 'text-red-300');
    } else if (paid > 0 && change < 0) {
      changeEl.textContent = 'Kurang ' + rp(Math.abs(change));
      changeEl.classList.add('text-red-300');
      changeEl.classList.remove('text-ivory/80', 'text-gold');
    } else {
      changeEl.textContent = 'Rp 0';
      changeEl.classList.remove('text-gold', 'text-red-300');
      changeEl.classList.add('text-ivory/80');
    }

    payBtn.disabled = state.busy || state.items.length === 0 || paid < total;
  }

  /* ----------------------------- Cart events ----------------------------- */
  cartEl.addEventListener('click', (event) => {
    const button = event.target.closest('[data-action]');
    if (!button) return;

    const row = button.closest('.cart-row');
    const key = row?.dataset.key;
    const index = state.items.findIndex((item) => item.key === key);
    if (index < 0) return;

    const item = state.items[index];
    const action = button.dataset.action;

    if (action === 'remove') {
      state.items.splice(index, 1);
    } else if (action === 'inc') {
      if (item.qty + 1 > item.stock) {
        notify(`Stok "${item.name}" hanya ${item.stock}.`);
        return;
      }
      item.qty += 1;
    } else if (action === 'dec') {
      item.qty -= 1;
      if (item.qty <= 0) state.items.splice(index, 1);
    }

    clearMessage();
    renderCart();
  });

  clearBtn?.addEventListener('click', () => {
    if (!state.items.length) return;
    if (!window.confirm('Kosongkan seluruh keranjang?')) return;
    state.items = [];
    paidInput.value = '';
    renderCart();
  });

  discountInput?.addEventListener('input', recalc);
  discountType?.addEventListener('change', recalc);

  paidInput?.addEventListener('input', () => {
    const digits = paidInput.value.replace(/\D/g, '');
    paidInput.value = digits ? nf.format(Number(digits)) : '';
    recalc();
  });

  paidInput?.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' || payBtn.disabled) return;
    event.preventDefault();
    payBtn.click();
  });

  mobileBar?.addEventListener('click', () => {
    document.getElementById('cart-panel')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  document.querySelectorAll('[data-cash]').forEach((button) => {
    button.addEventListener('click', () => {
      const mode = button.dataset.cash;
      const amount = mode === 'exact' ? calcTotals().total : Number(mode);
      paidInput.value = nf.format(Math.max(amount, 0));
      recalc();
      paidInput.focus();
    });
  });

  /* ----------------------------- Checkout ----------------------------- */
  payBtn?.addEventListener('click', async () => {
    if (state.busy) return;

    const { total } = calcTotals();
    if (!state.items.length) {
      notify('Keranjang masih kosong.');
      return;
    }
    const paid = paidValue();
    if (paid < total) {
      notify('Uang diterima belum mencukupi total pembayaran.');
      paidInput.focus();
      return;
    }

    const payload = {
      items: state.items.map((item) => item.type === 'product'
        ? { type: 'product', id: item.id, qty: item.qty }
        : { type: 'print', service_id: item.service_id, pages: item.pages, copies: item.copies, finishing_ids: item.finishing_ids }),
      discount: Number(discountInput.value) || 0,
      discount_type: discountType.value,
      paid,
      customer_name: customerInput.value.trim(),
      note: '',
    };

    state.busy = true;
    payLabel.textContent = 'Memproses…';
    payBtn.disabled = true;
    clearMessage();

    try {
      const response = await fetch(cfg.base + '/kasir/checkout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-Token': cfg.csrf,
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json().catch(() => ({ ok: false, message: 'Respons server tidak valid.' }));

      if (!response.ok || !data.ok) {
        throw new Error(data.message || 'Transaksi gagal disimpan.');
      }

      window.location.href = data.redirect;
      return;
    } catch (error) {
      notify(error.message || 'Tidak dapat terhubung ke server.');
    } finally {
      state.busy = false;
      payLabel.textContent = 'Bayar';
      recalc();
    }
  });

  /* ----------------------------- Search & filter ----------------------------- */
  function applyFilter() {
    const query = searchInput.value.trim().toLowerCase();
    let visible = 0;

    cards.forEach((card) => {
      const haystack = (card.dataset.search || '').toLowerCase();
      const matchQuery = query === '' || haystack.includes(query);
      const matchCategory = activeCategory === 'all' || String(card.dataset.category) === activeCategory;
      const show = matchQuery && matchCategory;
      card.classList.toggle('hidden', !show);
      if (show) visible += 1;
    });

    return { visible, query };
  }

  searchInput?.addEventListener('input', applyFilter);

  searchInput?.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      searchInput.value = '';
      applyFilter();
      return;
    }

    if (event.key !== 'Enter') return;
    event.preventDefault();

    const query = searchInput.value.trim().toLowerCase();
    if (query === '') return;

    const { visible } = applyFilter();
    if (visible === 0) {
      notify('Produk tidak ditemukan.');
      return;
    }

    let target = null;
    for (const card of cards) {
      if (card.classList.contains('hidden') || card.disabled) continue;
      const data = parseProduct(card);
      if ((data.barcode || '').toLowerCase() === query || (data.sku || '').toLowerCase() === query) {
        target = card;
        break;
      }
    }

    if (!target) {
      const firstVisible = cards.find((card) => !card.classList.contains('hidden') && !card.disabled);
      if (visible === 1 && firstVisible) target = firstVisible;
    }

    if (!target) {
      notify('Scan tidak cocok dengan produk mana pun.');
      return;
    }

    addProduct(parseProduct(target));
    searchInput.value = '';
    applyFilter();
    searchInput.focus();
  });

  productGrid?.addEventListener('click', (event) => {
    const card = event.target.closest('[data-product]');
    if (!card || card.disabled) return;
    addProduct(parseProduct(card));
  });

  document.querySelectorAll('.chip[data-category]').forEach((chip) => {
    chip.addEventListener('click', () => {
      activeCategory = chip.dataset.category;
      document.querySelectorAll('.chip[data-category]').forEach((other) => other.classList.toggle('active', other === chip));
      applyFilter();
    });
  });

  /* ----------------------------- Print service ----------------------------- */
  function printCalc() {
    const option = serviceSelect?.selectedOptions?.[0];
    const price = Number(option?.dataset.price || 0);
    const pages = Math.max(1, Number(pagesInput.value) || 1);
    const copies = Math.max(1, Number(copiesInput.value) || 1);
    let finishingTotal = 0;

    printForm.querySelectorAll('.finishing-check:checked').forEach((checkbox) => {
      finishingTotal += Number(checkbox.dataset.price || 0);
    });

    return {
      price,
      pages,
      copies,
      finishingTotal,
      total: price * pages * copies + finishingTotal,
    };
  }

  function refreshPrintTotal() {
    if (!printTotalEl) return;
    printTotalEl.textContent = rp(printCalc().total);
  }

  [serviceSelect, pagesInput, copiesInput].forEach((element) => {
    element?.addEventListener('input', refreshPrintTotal);
    element?.addEventListener('change', refreshPrintTotal);
  });

  printForm?.querySelectorAll('.finishing-check').forEach((checkbox) => {
    checkbox.addEventListener('change', refreshPrintTotal);
  });

  document.querySelectorAll('[data-modal-open="print-modal"]').forEach((button) => {
    button.addEventListener('click', () => setTimeout(refreshPrintTotal, 60));
  });

  printAddBtn?.addEventListener('click', () => {
    const option = serviceSelect?.selectedOptions?.[0];
    if (!option) {
      notify('Belum ada layanan cetak aktif.');
      return;
    }

    const calc = printCalc();
    const finishingIds = [];
    const finishingNames = [];

    printForm.querySelectorAll('.finishing-check:checked').forEach((checkbox) => {
      finishingIds.push(Number(checkbox.value));
      finishingNames.push(checkbox.dataset.name || 'Finishing');
    });

    state.items.push({
      key: 's' + Date.now().toString(36) + Math.random().toString(36).slice(2, 6),
      type: 'print',
      service_id: Number(serviceSelect.value),
      name: option.dataset.name || option.textContent.split(' — ')[0],
      pages: calc.pages,
      copies: calc.copies,
      qty: calc.pages * calc.copies,
      unit_price: calc.price,
      finishing_ids: finishingIds,
      finishing_names: finishingNames,
      finishing_total: calc.finishingTotal,
    });

    window.closeModal('print-modal');
    printForm.reset();
    refreshPrintTotal();
    clearMessage();
    renderCart();
  });

  /* ----------------------------- Keyboard ----------------------------- */
  document.addEventListener('keydown', (event) => {
    if (event.key === 'F2') {
      event.preventDefault();
      searchInput?.focus();
      searchInput?.select();
    }
    if (event.key === 'F4') {
      event.preventDefault();
      paidInput?.focus();
      paidInput?.select();
    }
  });

  /* ----------------------------- Init ----------------------------- */
  renderCart();
  refreshPrintTotal();
  applyFilter();
})();
