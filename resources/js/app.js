/**
 * portal.js — JavaScript Utama Portal Instansi Pemerintah
 * =========================================================
 * Menghidupkan semua komponen dari design system:
 *   layout.css   → Sidebar, Header, Hamburger, Breadcrumb
 *   content.css  → Card, Flash/Alert, Notifikasi Dropdown
 *   forms.css    → Toggle Switch, Form Validasi, Autocomplete
 *   documents.css → Toolbar Filter, View Toggle, Dropzone, Modal
 *   data-display → Tooltip, Skeleton, Badge, Tabel Sort
 *   progress.css → Progress Bar animasi
 *   utilities.css → Focus ring, scroll behavior
 *
 * Cara pakai:
 *   Taruh <script src="portal.js" defer></script> sebelum </body>
 * =========================================================
 */

(() => {
  'use strict';

  /* ═══════════════════════════════════════════════════════
     UTIL HELPERS
  ═══════════════════════════════════════════════════════ */

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
  const on = (el, ev, fn, opts) => el?.addEventListener(ev, fn, opts);
  const off = (el, ev, fn) => el?.removeEventListener(ev, fn);

  /** Dispatch custom event */
  const emit = (el, name, detail = {}) =>
    el.dispatchEvent(new CustomEvent(name, { bubbles: true, detail }));

  /** Format tanggal Indonesia */
  const formatDate = (date = new Date()) =>
    date.toLocaleDateString('id-ID', {
      weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });

  /** Format waktu relatif */
  const relativeTime = (date) => {
    const diff = (Date.now() - new Date(date)) / 1000;
    if (diff < 60)    return 'Baru saja';
    if (diff < 3600)  return `${Math.floor(diff / 60)} menit lalu`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
    return `${Math.floor(diff / 86400)} hari lalu`;
  };

  /** Debounce */
  const debounce = (fn, ms = 300) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  /** Tampilkan / sembunyikan elemen */
  const show = (el) => el?.classList.remove('hidden');
  const hide = (el) => el?.classList.add('hidden');
  const toggle = (el, force) => el?.classList.toggle('hidden', force);


  /* ═══════════════════════════════════════════════════════
     1. SIDEBAR — buka/tutup & navigasi aktif
  ═══════════════════════════════════════════════════════ */

  const Sidebar = (() => {
    let sidebar, backdrop, hamburger, closeBtn, isOpen = false;

    function open() {
      isOpen = true;
      sidebar?.classList.add('open');
      backdrop?.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      hamburger?.setAttribute('aria-expanded', 'true');
      sidebar?.setAttribute('aria-hidden', 'false');
      // Fokus ke tombol close saat sidebar terbuka (aksesibilitas)
      setTimeout(() => closeBtn?.focus(), 300);
    }

    function close() {
      isOpen = false;
      sidebar?.classList.remove('open');
      backdrop?.classList.add('hidden');
      document.body.style.overflow = '';
      hamburger?.setAttribute('aria-expanded', 'false');
      sidebar?.setAttribute('aria-hidden', 'true');
      hamburger?.focus();
    }

    function setActiveLink() {
      const current = location.pathname;
      $$('.sidebar-link').forEach(link => {
        const href = link.getAttribute('href') || link.dataset.href || '';
        const isActive = href && current.startsWith(href) && href !== '/';
        link.classList.toggle('active', isActive);
        link.setAttribute('aria-current', isActive ? 'page' : 'false');
      });
    }

    function init() {
      sidebar    = $('.app-sidebar');
      backdrop   = $('.sidebar-backdrop');
      hamburger  = $('.hamburger-btn');
      closeBtn   = $('.sidebar-close-btn');

      if (!sidebar) return;

      on(hamburger, 'click', open);
      on(closeBtn,  'click', close);
      on(backdrop,  'click', close);

      // Tutup dengan Escape
      on(document, 'keydown', e => {
        if (e.key === 'Escape' && isOpen) close();
      });

      // Tutup otomatis di ≥1024px saat di-resize
      const mq = window.matchMedia('(min-width: 1024px)');
      on(mq, 'change', e => { if (e.matches && isOpen) close(); });

      setActiveLink();

      // Badge "pulse" pada nav item dengan notifikasi
      $$('.nav-badge.pulse').forEach(badge => {
        badge.style.animation = 'pulse-badge 2s ease-in-out infinite';
      });
    }

    return { init, open, close, setActiveLink };
  })();


  /* ═══════════════════════════════════════════════════════
     2. HEADER — tanggal, breadcrumb dinamis
  ═══════════════════════════════════════════════════════ */

  const Header = (() => {
    function updateDate() {
      $$('.header-date-chip, .date-display').forEach(el => {
        const icon = el.querySelector('i');
        el.textContent = formatDate();
        if (icon) el.prepend(icon); // kembalikan ikon setelah update teks
      });
    }

    function buildBreadcrumb() {
      const bcs = $$('.header-breadcrumb, .breadcrumb');
      bcs.forEach(bc => {
        // Jika sudah di-render dari server, skip
        if (bc.children.length > 0) return;
        const parts = location.pathname.replace(/^\//, '').split('/');
        const labels = { '': 'Beranda', dokumen: 'Dokumen', audit: 'Audit', laporan: 'Laporan', surat: 'Surat', pengguna: 'Pengguna', pengaturan: 'Pengaturan' };
        let path = '';
        const items = ['beranda', ...parts.filter(Boolean)].map((p, i, arr) => {
          path += i === 0 ? '/' : `/${p}`;
          const isLast = i === arr.length - 1;
          const label = labels[p] || p.charAt(0).toUpperCase() + p.slice(1);
          return isLast
            ? `<span class="header-breadcrumb-current breadcrumb-current">${label}</span>`
            : `<a href="${path}">${label}</a><span class="header-breadcrumb-sep breadcrumb-sep" aria-hidden="true">/</span>`;
        });
        bc.innerHTML = items.join('');
      });
    }

    function init() {
      updateDate();
      buildBreadcrumb();
      // Perbarui tanggal tiap menit (untuk tab yang lama dibuka)
      setInterval(updateDate, 60_000);
    }

    return { init };
  })();


  /* ═══════════════════════════════════════════════════════
     3. NOTIFIKASI — dropdown + badge + mark-as-read
  ═══════════════════════════════════════════════════════ */

  const Notification = (() => {
    // Data default — ganti dengan fetch() ke API Anda
    const defaultNotifs = [
      { id: 1, type: 'success', icon: 'ti-circle-check', msg: 'Laporan Dinas Pendidikan telah disetujui oleh Kepala Inspektorat.', time: new Date(Date.now() - 5 * 60000), unread: true },
      { id: 2, type: 'warning', icon: 'ti-alert-triangle', msg: 'Berkas BPKAD memerlukan revisi sebelum 12 Juni 2025.', time: new Date(Date.now() - 3600000), unread: true },
      { id: 3, type: 'info',    icon: 'ti-mail',           msg: 'Pesan baru dari tim audit internal — mohon segera dibalas.', time: new Date(Date.now() - 10800000), unread: true },
      { id: 4, type: 'error',   icon: 'ti-alert-circle',   msg: 'Gagal sinkronisasi data dengan SIMDA. Hubungi admin.', time: new Date(Date.now() - 86400000), unread: false },
    ];

    const iconClass = { success: 'ni-green', warning: 'ni-amber', info: 'ni-blue', error: 'ni-red' };

    let state = [];
    let dropdowns = [];

    function getUnreadCount() { return state.filter(n => n.unread).length; }

    function updateBadges() {
      const count = getUnreadCount();
      $$('.notif-badge, .notif-badge-pill, .notif-unread-chip').forEach(badge => {
        if (count > 0) {
          badge.textContent = count > 99 ? '99+' : count;
          show(badge);
        } else {
          hide(badge);
        }
      });
    }

    function renderList(listEl) {
      if (!listEl) return;
      if (state.length === 0) {
        listEl.innerHTML = `
          <div class="notif-empty">
            <div class="notif-empty-icon"><i class="ti ti-bell-off" aria-hidden="true"></i></div>
            <p class="notif-empty-title">Tidak ada notifikasi</p>
            <p class="notif-empty-sub">Semua notifikasi sudah dibaca.</p>
          </div>`;
        return;
      }
      listEl.innerHTML = state.map(n => `
        <div class="notif-item${n.unread ? ' unread notif-item--unread' : ''}" data-id="${n.id}" role="listitem" tabindex="0"
          aria-label="${n.msg}${n.unread ? ' (belum dibaca)' : ''}">
          <div class="notif-item-icon notif-icon--${n.type} ${iconClass[n.type]}">
            <i class="ti ${n.icon}" aria-hidden="true"></i>
          </div>
          <div class="notif-item-body">
            <p class="notif-item-msg${n.unread ? ' notif-item-text' : ''}">${n.msg}</p>
            <span class="notif-item-time notif-item-meta">${relativeTime(n.time)}</span>
          </div>
          ${n.unread ? '<div class="notif-unread-dot" aria-hidden="true"></div>' : ''}
        </div>`).join('');

      // Klik item → tandai dibaca
      $$('.notif-item', listEl).forEach(item => {
        const handler = () => markRead(+item.dataset.id);
        on(item, 'click', handler);
        on(item, 'keydown', e => { if (e.key === 'Enter' || e.key === ' ') handler(); });
      });
    }

    function markRead(id) {
      const notif = state.find(n => n.id === id);
      if (notif) notif.unread = false;
      refresh();
      emit(document, 'notif:read', { id });
    }

    function markAllRead() {
      state.forEach(n => n.unread = false);
      refresh();
      emit(document, 'notif:readAll');
    }

    function refresh() {
      updateBadges();
      dropdowns.forEach(dd => {
        renderList($('.notif-list, .notif-list-inner', dd));
        // Update chip unread
        const chip = $('.notif-unread-chip, .notif-unread-count', dd);
        const count = getUnreadCount();
        if (chip) { chip.textContent = `${count} baru`; toggle(chip, count === 0); }
      });
    }

    /**
     * Tambah notifikasi baru secara programatik.
     * Contoh: Notification.add({ type: 'success', icon: 'ti-check', msg: 'Data tersimpan.', unread: true })
     */
    function add(notif) {
      state.unshift({ id: Date.now(), time: new Date(), ...notif });
      refresh();
    }

    function bindDropdown(wrap) {
      const btn = $('.notif-bell-btn, .header-icon-btn[aria-label*="otif"], .btn-icon[aria-label*="otif"]', wrap)
               || (wrap.dataset.notifTrigger != null ? wrap : null);
      const dd  = $('.notif-dropdown', wrap);
      if (!btn || !dd) return;

      dropdowns.push(dd);

      let open = false;
      const openDD  = () => { open = true;  dd.classList.add('open');  btn.setAttribute('aria-expanded', 'true'); };
      const closeDD = () => { open = false; dd.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); };

      on(btn, 'click', e => { e.stopPropagation(); open ? closeDD() : openDD(); });
      on(document, 'click', e => { if (open && !wrap.contains(e.target)) closeDD(); });
      on(document, 'keydown', e => { if (e.key === 'Escape' && open) closeDD(); });

      // Mark all read btn
      const markAllBtn = $('.notif-mark-read, .notif-mark-read-btn, .notif-read-all-btn', dd);
      on(markAllBtn, 'click', e => { e.stopPropagation(); markAllRead(); });

      renderList($('.notif-list, .notif-list-inner', dd));
    }

    function init(data) {
      state = data || defaultNotifs;
      updateBadges();
      // Bind semua wrapper notifikasi di halaman
      $$('.notif-bell-wrap, [data-notif]').forEach(bindDropdown);
    }

    return { init, add, markRead, markAllRead, refresh };
  })();


  /* ═══════════════════════════════════════════════════════
     4. FLASH / ALERT — auto-dismiss & restore
  ═══════════════════════════════════════════════════════ */

  const Flash = (() => {
    const DURATION = 5000; // ms sebelum auto-dismiss

    function bind(flashEl) {
      const closeBtn = $('.flash-close', flashEl);
      on(closeBtn, 'click', () => dismiss(flashEl));

      // Auto-dismiss jika ada data-auto-dismiss
      if (flashEl.dataset.autoDismiss !== undefined) {
        const ms = +flashEl.dataset.autoDismiss || DURATION;
        setTimeout(() => dismiss(flashEl), ms);
      }
    }

    function dismiss(el) {
      el.style.transition = 'opacity 0.3s, transform 0.3s';
      el.style.opacity = '0';
      el.style.transform = 'translateY(-4px)';
      setTimeout(() => el.remove(), 300);
      emit(el, 'flash:dismissed');
    }

    /**
     * Tampilkan flash baru secara programatik.
     * Contoh: Flash.show('success', 'Berkas berhasil diunggah!', 'Sukses')
     */
    function show(type, msg, title = '', container = document.body) {
      const icons = { success: 'ti-check', error: 'ti-x', warning: 'ti-alert-triangle', info: 'ti-info-circle' };
      const el = document.createElement('div');
      el.className = `flash flash-${type}`;
      el.setAttribute('role', 'alert');
      el.setAttribute('aria-live', 'polite');
      el.setAttribute('data-auto-dismiss', DURATION);
      el.innerHTML = `
        <div class="flash-icon" aria-hidden="true"><i class="ti ${icons[type] || icons.info}"></i></div>
        <div class="flash-body">
          ${title ? `<div class="flash-title">${title}</div>` : ''}
          <div class="flash-msg">${msg}</div>
        </div>
        <button class="flash-close" aria-label="Tutup notifikasi"><i class="ti ti-x" aria-hidden="true"></i></button>`;

      // Cari container flash yang sudah ada, atau gunakan body
      const fc = $('.flash-container', container) || container;
      fc.prepend(el);
      bind(el);
      return el;
    }

    function init() {
      $$('.flash').forEach(bind);
    }

    return { init, show, dismiss };
  })();


  /* ═══════════════════════════════════════════════════════
     5. TOGGLE SWITCH — aksesibel
  ═══════════════════════════════════════════════════════ */

  const Toggle = (() => {
    function bind(wrap) {
      const track = $('.toggle-track', wrap);
      const label = $('.toggle-label', wrap);
      if (!track) return;

      const input = $('input[type="checkbox"]', wrap);
      let on_ = track.classList.contains('on') || input?.checked || false;

      wrap.setAttribute('role', 'switch');
      wrap.setAttribute('tabindex', '0');
      wrap.setAttribute('aria-checked', on_);

      const update = (val) => {
        on_ = val;
        track.classList.toggle('on', on_);
        wrap.setAttribute('aria-checked', on_);
        if (input) input.checked = on_;
        if (label) label.dataset.on = on_ ? '1' : '0';
        emit(wrap, 'toggle:change', { checked: on_ });
      };

      on(wrap, 'click', () => update(!on_));
      on(wrap, 'keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); update(!on_); }
        if (e.key === 'ArrowRight') update(true);
        if (e.key === 'ArrowLeft')  update(false);
      });
    }

    function init() {
      $$('.toggle-wrap').forEach(bind);
    }

    return { init, bind };
  })();


  /* ═══════════════════════════════════════════════════════
     6. FORM VALIDASI — real-time & submit
  ═══════════════════════════════════════════════════════ */

  const FormValidation = (() => {
    const rules = {
      required: (v) => v.trim() !== '' || 'Kolom ini wajib diisi.',
      email: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Format email tidak valid.',
      nip: (v) => /^\d{18}$/.test(v.replace(/\s/g, '')) || 'NIP harus 18 digit angka.',
      phone: (v) => /^(\+62|0)\d{8,13}$/.test(v.replace(/\s/g, '')) || 'Nomor telepon tidak valid.',
      min: (len) => (v) => v.trim().length >= len || `Minimal ${len} karakter.`,
      max: (len) => (v) => v.trim().length <= len || `Maksimal ${len} karakter.`,
    };

    function validateField(input) {
      const ruleAttr = input.dataset.validate?.split('|') || [];
      if (input.required) ruleAttr.unshift('required');

      let error = null;
      for (const rule of ruleAttr) {
        const [name, arg] = rule.split(':');
        const fn = typeof rules[name] === 'function'
          ? (arg ? rules[name](+arg) : rules[name])
          : null;
        if (!fn) continue;
        const result = fn(input.value);
        if (result !== true) { error = result; break; }
      }

      const errorEl = input.parentElement.querySelector('.form-error');
      if (error) {
        input.classList.add('form-input-error', 'input-error');
        input.setAttribute('aria-invalid', 'true');
        if (errorEl) {
          errorEl.innerHTML = `<i class="ti ti-alert-circle" aria-hidden="true"></i> ${error}`;
          errorEl.style.display = 'flex';
        }
      } else {
        input.classList.remove('form-input-error', 'input-error');
        input.setAttribute('aria-invalid', 'false');
        if (errorEl) errorEl.style.display = 'none';
      }

      return !error;
    }

    function validateForm(form) {
      const inputs = $$('input, textarea, select', form).filter(i => i.dataset.validate || i.required);
      return inputs.map(validateField).every(Boolean);
    }

    function init() {
      // Real-time validasi saat blur
      $$('.form-input[data-validate], .form-input[required]').forEach(input => {
        on(input, 'blur', () => validateField(input));
        on(input, 'input', debounce(() => {
          if (input.getAttribute('aria-invalid') === 'true') validateField(input);
        }, 400));
      });

      // Validasi saat submit
      $$('form[data-validate-form]').forEach(form => {
        on(form, 'submit', e => {
          if (!validateForm(form)) {
            e.preventDefault();
            const first = form.querySelector('[aria-invalid="true"]');
            first?.focus();
            Flash.show('error', 'Mohon lengkapi kolom yang bertanda merah sebelum mengirim.');
          }
        });
      });
    }

    return { init, validateField, validateForm };
  })();


  /* ═══════════════════════════════════════════════════════
     7. DOKUMEN TOOLBAR — filter pills, search, view toggle
  ═══════════════════════════════════════════════════════ */

  const DocToolbar = (() => {
    function initPills(toolbar) {
      const pills = $$('.doc-pill[data-filter]', toolbar);
      pills.forEach(pill => {
        on(pill, 'click', () => {
          const group = pill.dataset.group || 'default';
          pills.filter(p => p.dataset.group === group || !p.dataset.group)
               .forEach(p => p.classList.remove('doc-pill--active', 'doc-pill--filter-active'));
          pill.classList.add('doc-pill--active');
          emit(toolbar, 'filter:change', { filter: pill.dataset.filter, group });
        });
      });
    }

    function initSearch(toolbar) {
      const input = $('.doc-search-input', toolbar);
      if (!input) return;
      on(input, 'input', debounce(e => {
        emit(toolbar, 'search:change', { query: e.target.value.trim() });
      }, 350));

      // Shortcut: / atau Ctrl+F fokus ke search
      on(document, 'keydown', e => {
        if ((e.key === '/' || (e.ctrlKey && e.key === 'f')) && document.activeElement.tagName !== 'INPUT') {
          e.preventDefault();
          input.focus();
          input.select();
        }
      });
    }

    function initViewToggle(toolbar) {
      const btns = $$('.doc-vt-btn', toolbar);
      btns.forEach(btn => {
        on(btn, 'click', () => {
          btns.forEach(b => b.classList.remove('doc-vt-btn--active'));
          btn.classList.add('doc-vt-btn--active');
          emit(toolbar, 'view:change', { view: btn.dataset.view });
        });
      });
    }

    function initAdvancedFilter(toolbar) {
      const toggle_ = $('.doc-advanced-toggle', toolbar);
      const form    = $('.doc-advanced-form', toolbar);
      const divider = $('.doc-filter-divider', toolbar);
      if (!toggle_ || !form) return;
      let open = false;
      on(toggle_, 'click', () => {
        open = !open;
        form.style.display   = open ? '' : 'none';
        divider?.style && (divider.style.display = open ? '' : 'none');
        toggle_.classList.toggle('doc-pill--filter-active', open);
        toggle_.setAttribute('aria-expanded', open);
      });
      form.style.display   = 'none';
      if (divider) divider.style.display = 'none';
    }

    function init() {
      $$('.doc-toolbar').forEach(tb => {
        initPills(tb);
        initSearch(tb);
        initViewToggle(tb);
        initAdvancedFilter(tb);
      });
    }

    return { init };
  })();


  /* ═══════════════════════════════════════════════════════
     8. TABEL — sortir kolom & seleksi baris
  ═══════════════════════════════════════════════════════ */

  const Table = (() => {
    function sortTable(table, colIdx, asc) {
      const tbody = $('tbody', table);
      const rows  = $$('tr', tbody);
      rows.sort((a, b) => {
        const av = a.cells[colIdx]?.textContent.trim() || '';
        const bv = b.cells[colIdx]?.textContent.trim() || '';
        const num_a = parseFloat(av.replace(/[^\d.-]/g, ''));
        const num_b = parseFloat(bv.replace(/[^\d.-]/g, ''));
        const cmp = isNaN(num_a) || isNaN(num_b)
          ? av.localeCompare(bv, 'id')
          : num_a - num_b;
        return asc ? cmp : -cmp;
      });
      rows.forEach(r => tbody.appendChild(r));
    }

    function bindSortHeaders(table) {
      $$('th[data-sort]', table).forEach((th, idx) => {
        th.style.cursor = 'pointer';
        th.setAttribute('tabindex', '0');
        th.setAttribute('aria-sort', 'none');
        let asc = true;
        const sort = () => {
          $$('th[data-sort]', table).forEach(h => {
            h.setAttribute('aria-sort', 'none');
            h.dataset.sortDir = '';
          });
          asc = th.dataset.sortDir !== 'asc';
          th.dataset.sortDir = asc ? 'asc' : 'desc';
          th.setAttribute('aria-sort', asc ? 'ascending' : 'descending');
          sortTable(table, idx, asc);
        };
        on(th, 'click', sort);
        on(th, 'keydown', e => { if (e.key === 'Enter') sort(); });
      });
    }

    function bindRowSelect(table) {
      const checkAll = $('thead input[type="checkbox"]', table);
      const checks   = $$('tbody input[type="checkbox"]', table);
      if (!checkAll) return;

      const update = () => {
        const checked = checks.filter(c => c.checked).length;
        checkAll.indeterminate = checked > 0 && checked < checks.length;
        checkAll.checked = checked === checks.length;
        emit(table, 'table:select', { count: checked, ids: checks.filter(c => c.checked).map(c => c.value) });
      };

      on(checkAll, 'change', () => {
        checks.forEach(c => { c.checked = checkAll.checked; c.closest('tr')?.classList.toggle('selected', checkAll.checked); });
        emit(table, 'table:select', { count: checkAll.checked ? checks.length : 0 });
      });
      checks.forEach(c => {
        on(c, 'change', () => { c.closest('tr')?.classList.toggle('selected', c.checked); update(); });
      });
    }

    function init() {
      $$('.table-base[data-sortable]').forEach(t => { bindSortHeaders(t); bindRowSelect(t); });
      $$('.table-base').forEach(bindRowSelect);
    }

    return { init };
  })();


  /* ═══════════════════════════════════════════════════════
     9. MODAL — buka/tutup, fokus trap, aksesibilitas
  ═══════════════════════════════════════════════════════ */

  const Modal = (() => {
    let stack = []; // modal stack (untuk modal bertingkat)

    const FOCUSABLE = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';

    function trapFocus(modal) {
      const els = $$(`${FOCUSABLE}:not([disabled])`, modal);
      if (!els.length) return;
      const first = els[0], last = els[els.length - 1];
      const handler = e => {
        if (e.key !== 'Tab') return;
        if (e.shiftKey ? document.activeElement === first : document.activeElement === last) {
          e.preventDefault();
          (e.shiftKey ? last : first).focus();
        }
      };
      on(modal, 'keydown', handler);
      first.focus();
      return handler;
    }

    function open(modalId, data = {}) {
      const backdrop = typeof modalId === 'string' ? $(`#${modalId}`) : modalId;
      if (!backdrop) return;

      backdrop.classList.remove('hidden');
      backdrop.style.display = 'flex';
      backdrop.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';

      const box = $('.modal-box', backdrop);
      const focusHandler = box ? trapFocus(box) : null;

      stack.push({ backdrop, focusHandler });
      emit(backdrop, 'modal:open', data);

      // Inisialisasi isi modal jika ada data
      Object.entries(data).forEach(([key, val]) => {
        const el = $(`[data-modal-field="${key}"]`, backdrop);
        if (el) el.textContent = val;
      });

      // Tutup saat klik backdrop (di luar .modal-box)
      const clickHandler = e => { if (e.target === backdrop) close(backdrop); };
      on(backdrop, 'click', clickHandler);

      return backdrop;
    }

    function close(backdrop) {
      if (!backdrop) {
        const top = stack[stack.length - 1];
        if (top) close(top.backdrop);
        return;
      }
      backdrop.classList.add('hidden');
      backdrop.style.display = '';
      backdrop.setAttribute('aria-hidden', 'true');
      emit(backdrop, 'modal:close');

      stack = stack.filter(s => s.backdrop !== backdrop);
      if (stack.length === 0) document.body.style.overflow = '';
    }

    function confirm(msg, title = 'Konfirmasi') {
      return new Promise(resolve => {
        const el = $('[data-modal="confirm"]');
        if (!el) { resolve(window.confirm(msg)); return; }
        const titleEl = $('[data-modal-field="title"]', el);
        const msgEl   = $('[data-modal-field="message"]', el);
        const okBtn   = $('[data-modal-action="ok"]', el);
        const cancelBtn = $('[data-modal-action="cancel"]', el);
        if (titleEl) titleEl.textContent = title;
        if (msgEl)   msgEl.textContent   = msg;
        open(el);
        const cleanup = (result) => { close(el); off(okBtn, 'click', ok_); off(cancelBtn, 'click', cancel_); resolve(result); };
        const ok_ = () => cleanup(true), cancel_ = () => cleanup(false);
        on(okBtn, 'click', ok_);
        on(cancelBtn, 'click', cancel_);
      });
    }

    function init() {
      // Trigger tombol buka modal
      $$('[data-modal-open]').forEach(btn => {
        on(btn, 'click', () => {
          const id = btn.dataset.modalOpen;
          const data = btn.dataset.modalData ? JSON.parse(btn.dataset.modalData) : {};
          open(id, data);
        });
      });

      // Tombol tutup modal
      $$('[data-modal-close]').forEach(btn => {
        on(btn, 'click', () => close());
      });

      // Escape menutup modal teratas
      on(document, 'keydown', e => {
        if (e.key === 'Escape' && stack.length) close();
      });
    }

    return { init, open, close, confirm };
  })();


  /* ═══════════════════════════════════════════════════════
     10. DROPZONE — drag & drop file upload
  ═══════════════════════════════════════════════════════ */

  const Dropzone = (() => {
    const MAX_SIZE_MB = 10;
    const ALLOWED_TYPES = ['application/pdf', 'image/jpeg', 'image/png',
      'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    function formatSize(bytes) {
      if (bytes < 1024) return `${bytes} B`;
      if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`;
      return `${(bytes / 1048576).toFixed(1)} MB`;
    }

    function validateFile(file) {
      if (!ALLOWED_TYPES.includes(file.type) && file.type !== '') {
        return `Tipe file tidak didukung: ${file.name}`;
      }
      if (file.size > MAX_SIZE_MB * 1048576) {
        return `Ukuran file ${file.name} melebihi batas ${MAX_SIZE_MB} MB.`;
      }
      return null;
    }

    function renderFileList(dz, files) {
      let list = $('.dropzone-filelist', dz.parentElement);
      if (!list) {
        list = document.createElement('ul');
        list.className = 'dropzone-filelist';
        list.style.cssText = 'list-style:none;padding:0;margin:8px 0 0;display:flex;flex-direction:column;gap:6px;';
        dz.insertAdjacentElement('afterend', list);
      }
      list.innerHTML = '';
      files.forEach((file, i) => {
        const err = validateFile(file);
        const li = document.createElement('li');
        li.style.cssText = `display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:6px;font-size:13px;
          background:${err ? 'var(--error-dim)' : 'var(--success-dim)'};border:1px solid ${err ? 'var(--error-soft)' : 'var(--success-soft)'};`;
        li.innerHTML = `
          <i class="ti ${err ? 'ti-alert-circle' : 'ti-file'}" aria-hidden="true" style="font-size:16px;color:${err ? 'var(--error)' : 'var(--success)'}"></i>
          <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${file.name}</span>
          <span style="color:var(--text-3);white-space:nowrap;">${formatSize(file.size)}</span>
          ${err ? `<span style="color:var(--error);font-size:12px;">${err}</span>` : ''}
          <button onclick="this.closest('li').remove()" aria-label="Hapus file" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:16px;padding:2px;">
            <i class="ti ti-x" aria-hidden="true"></i>
          </button>`;
        list.appendChild(li);
      });
    }

    function bind(dz) {
      const input = $('input[type="file"]', dz) || dz.parentElement.querySelector('input[type="file"]');
      let allFiles = [];

      const prevent = e => { e.preventDefault(); e.stopPropagation(); };
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => on(dz, ev, prevent));

      on(dz, 'dragover',  () => dz.classList.add('doc-dropzone--over'));
      on(dz, 'dragleave', () => dz.classList.remove('doc-dropzone--over'));

      on(dz, 'drop', e => {
        dz.classList.remove('doc-dropzone--over');
        const files = [...(e.dataTransfer?.files || [])];
        if (!files.length) return;
        allFiles = [...allFiles, ...files];
        renderFileList(dz, allFiles);
        emit(dz, 'dropzone:files', { files: allFiles });
        if (input) {
          const dt = new DataTransfer();
          allFiles.forEach(f => dt.items.add(f));
          input.files = dt.files;
        }
        const errors = files.map(validateFile).filter(Boolean);
        if (errors.length) Flash.show('error', errors.join(' '));
        else Flash.show('success', `${files.length} berkas siap diunggah.`);
      });

      on(dz, 'click', () => input?.click());
      on(dz, 'keydown', e => { if (e.key === 'Enter' || e.key === ' ') input?.click(); });

      if (input) {
        on(input, 'change', () => {
          allFiles = [...input.files];
          renderFileList(dz, allFiles);
          emit(dz, 'dropzone:files', { files: allFiles });
        });
      }
    }

    function init() {
      $$('.doc-dropzone').forEach(bind);
    }

    return { init, bind };
  })();


  /* ═══════════════════════════════════════════════════════
     11. SKELETON LOADER — tampilkan/sembunyikan
  ═══════════════════════════════════════════════════════ */

  const Skeleton = (() => {
    /**
     * Ganti konten dengan skeleton, lalu kembalikan setelah fetch selesai.
     * Contoh:
     *   const restore = Skeleton.wrap(cardEl);
     *   await fetch(...);
     *   restore(newHTML);
     */
    function wrap(el, lines = 3) {
      const original = el.innerHTML;
      const skels = Array.from({ length: lines }, (_, i) =>
        `<div class="skeleton" style="height:14px;width:${70 + (i % 3) * 10}%;margin-bottom:8px;border-radius:4px;"></div>`
      ).join('');
      el.innerHTML = skels;
      return (newContent) => { el.innerHTML = newContent ?? original; };
    }

    /** Sembunyikan semua skeleton di halaman setelah data dimuat */
    function hideAll() {
      $$('.skeleton').forEach(s => {
        s.classList.remove('skeleton');
        s.style.animation = '';
      });
    }

    return { wrap, hideAll };
  })();


  /* ═══════════════════════════════════════════════════════
     12. PROGRESS BAR — animasi & update
  ═══════════════════════════════════════════════════════ */

  const Progress = (() => {
    /**
     * Set nilai progress bar.
     * @param {Element|string} el   - Elemen .progress-fill atau selector
     * @param {number} value        - Nilai 0–100
     */
    function set(el, value) {
      const bar = typeof el === 'string' ? $(el) : el;
      if (!bar) return;
      const pct = Math.min(100, Math.max(0, Math.round(value)));
      bar.style.width = `${pct}%`;
      bar.setAttribute('aria-valuenow', pct);
      const track = bar.closest('.progress-track');
      if (track) track.setAttribute('aria-label', `${pct}%`);
      const label = bar.parentElement?.querySelector('.progress-label-value');
      if (label) label.textContent = `${pct}%`;
      emit(bar, 'progress:change', { value: pct });
    }

    /**
     * Animasi upload/proses palsu (indeterminate).
     * Berguna untuk operasi yang durasinya tidak diketahui.
     */
    function fake(bar, onComplete) {
      let v = 0;
      const iv = setInterval(() => {
        // Melambat saat mendekati 90%
        const step = v < 30 ? 8 : v < 70 ? 4 : v < 90 ? 1.5 : 0.3;
        v = Math.min(v + step, 95);
        set(bar, v);
        if (v >= 95) clearInterval(iv);
      }, 150);

      return () => { clearInterval(iv); set(bar, 100); setTimeout(() => onComplete?.(), 400); };
    }

    function init() {
      // Animasikan progress bar yang punya data-value saat masuk viewport
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (!entry.isIntersecting) return;
          const bar = entry.target;
          const target = parseFloat(bar.dataset.value || bar.style.width) || 0;
          bar.style.width = '0%';
          requestAnimationFrame(() => { set(bar, target); });
          observer.unobserve(bar);
        });
      }, { threshold: 0.3 });

      $$('.progress-fill[data-value]').forEach(bar => observer.observe(bar));
    }

    return { init, set, fake };
  })();


  /* ═══════════════════════════════════════════════════════
     13. DOKUMEN REVISI — riwayat & form revisi
  ═══════════════════════════════════════════════════════ */

  const DocRevision = (() => {
    function initRevisionForm() {
      const forms = $$('[data-revision-form]');
      forms.forEach(form => {
        const textarea  = $('textarea', form);
        const counter   = $('[data-char-count]', form);
        const maxLen    = +textarea?.dataset.maxlength || 1000;

        if (textarea && counter) {
          const update = () => {
            const len = textarea.value.length;
            counter.textContent = `${len}/${maxLen}`;
            counter.style.color = len > maxLen * 0.9 ? 'var(--error)' : 'var(--text-3)';
          };
          on(textarea, 'input', update);
          update();
        }
      });
    }

    function initRevisionItems() {
      // Collapse/expand revision history items
      $$('.doc-revision-item[data-collapsible]').forEach(item => {
        const header  = $('.doc-revision-item-header', item);
        const content = $('.doc-revision-content', item);
        if (!header || !content) return;
        header.style.cursor = 'pointer';
        let open = item.classList.contains('doc-revision-item--latest');
        content.style.display = open ? '' : 'none';
        on(header, 'click', () => {
          open = !open;
          content.style.display = open ? '' : 'none';
          emit(item, 'revision:toggle', { open });
        });
      });
    }

    function init() {
      initRevisionForm();
      initRevisionItems();
    }

    return { init };
  })();


  /* ═══════════════════════════════════════════════════════
     14. UTILITIES — focus ring, scroll-to-top, konfirmasi hapus
  ═══════════════════════════════════════════════════════ */

  const Utilities = (() => {
    function initFocusRing() {
      // Tampilkan ring fokus hanya saat navigasi keyboard
      document.body.addEventListener('mousedown', () => document.body.classList.add('no-focus-ring'));
      document.body.addEventListener('keydown',   () => document.body.classList.remove('no-focus-ring'));
    }

    function initScrollTop() {
      const btn = $('.scroll-to-top, [data-scroll-top]');
      if (!btn) return;
      const content = $('.app-content') || window;
      const scroller = content === window ? document.documentElement : content;

      const onScroll = debounce(() => {
        toggle(btn, scroller.scrollTop < 300);
      }, 100);

      on(content, 'scroll', onScroll);
      on(btn, 'click', () => scroller.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    function initConfirmDelete() {
      $$('[data-confirm]').forEach(el => {
        on(el, 'click', async e => {
          e.preventDefault();
          const msg = el.dataset.confirm || 'Apakah Anda yakin ingin menghapus data ini?';
          const ok  = await Modal.confirm(msg, 'Konfirmasi Penghapusan');
          if (!ok) return;
          // Lanjutkan aksi asli
          if (el.tagName === 'A') location.href = el.href;
          else if (el.form) el.form.submit();
          else emit(el, 'confirm:ok');
        });
      });
    }

    function initCopyToClipboard() {
      $$('[data-copy]').forEach(btn => {
        on(btn, 'click', async () => {
          const text = btn.dataset.copy || $(`#${btn.dataset.copyTarget}`)?.textContent || '';
          try {
            await navigator.clipboard.writeText(text);
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="ti ti-check" aria-hidden="true"></i>';
            setTimeout(() => { btn.innerHTML = orig; }, 1500);
            Flash.show('success', 'Disalin ke clipboard.');
          } catch {
            Flash.show('error', 'Gagal menyalin teks.');
          }
        });
      });
    }

    function initPrintBtn() {
      $$('[data-print]').forEach(btn => {
        on(btn, 'click', () => window.print());
      });
    }

    function init() {
      initFocusRing();
      initScrollTop();
      initConfirmDelete();
      initCopyToClipboard();
      initPrintBtn();
    }

    return { init };
  })();


  /* ═══════════════════════════════════════════════════════
     15. ANIMASI CSS — keyframes via JS (pulse-badge, spin)
  ═══════════════════════════════════════════════════════ */

  function injectKeyframes() {
    const style = document.createElement('style');
    style.textContent = `
      @keyframes pulse-badge {
        0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
        50% { transform: scale(1.05); box-shadow: 0 0 0 4px rgba(239,68,68,0); }
      }
      .no-focus-ring *:focus { outline: none !important; }
      .notif-dropdown.open { display: block; }
      .notif-item.selected,
      .notif-item:focus-visible { outline: 2px solid var(--accent); outline-offset: -2px; }
      tr.selected td { background: var(--accent-dim) !important; }
    `;
    document.head.appendChild(style);
  }


  /* ═══════════════════════════════════════════════════════
     INIT — jalankan semua modul setelah DOM siap
  ═══════════════════════════════════════════════════════ */

  function init() {
    injectKeyframes();
    Sidebar.init();
    Header.init();
    Notification.init();   // Pass array data dari API jika ada: Notification.init(window.__notifs__)
    Flash.init();
    Toggle.init();
    FormValidation.init();
    DocToolbar.init();
    Table.init();
    Modal.init();
    Dropzone.init();
    Progress.init();
    DocRevision.init();
    Utilities.init();

    console.info('[Portal Gov] Semua komponen berhasil diinisialisasi.');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  /* ── Public API — akses via window.Portal ── */
  window.Portal = {
    Sidebar, Header, Notification, Flash,
    Toggle, Form: FormValidation,
    DocToolbar, Table, Modal, Dropzone,
    Skeleton, Progress, DocRevision,
  };

})();