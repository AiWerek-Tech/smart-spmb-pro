/**
 * Smart SPMB Pro — Command Palette (Ctrl+K Global Search)
 * Provides quick navigation and search across the application.
 */
(function () {
    'use strict';

    const overlay = document.getElementById('commandPaletteOverlay');
    const input   = document.getElementById('commandPaletteInput');
    const results = document.getElementById('commandPaletteResults');

    if (!overlay || !input || !results) return;

    let activeIndex = -1;
    let debounceTimer = null;
    let lastTrigger = null;

    // ── Static menu items for quick navigation ──────────────────────
    const role = document.body.dataset.userRole || '';
    
    const menuItems = [
        // Admin
        { title: 'Dashboard Admin',      url: '/admin/dashboard',      icon: 'layout-dashboard', roles: ['admin'], keywords: 'beranda home' },
        { title: 'Kelola Pengguna',       url: '/admin/users',          icon: 'users',            roles: ['admin'], keywords: 'user akun' },
        { title: 'Jalur Pendaftaran',     url: '/admin/jalur',          icon: 'git-fork',         roles: ['admin'], keywords: 'pathway track' },
        { title: 'Gelombang',             url: '/admin/gelombang',      icon: 'calendar',         roles: ['admin'], keywords: 'wave batch periode' },
        { title: 'Hasil Seleksi',         url: '/admin/seleksi',        icon: 'award',            roles: ['admin'], keywords: 'selection result' },
        { title: 'Pengumuman',            url: '/admin/announcements',  icon: 'megaphone',        roles: ['admin'], keywords: 'announcement berita' },
        { title: 'Profil & Galeri',       url: '/admin/content',        icon: 'image',            roles: ['admin'], keywords: 'content galeri foto' },
        { title: 'Kelola FAQ',            url: '/admin/faq',            icon: 'help-circle',      roles: ['admin'], keywords: 'faq pertanyaan' },
        { title: 'Konfigurasi Sistem',    url: '/admin/settings',       icon: 'settings',         roles: ['admin'], keywords: 'setting config pengaturan' },
        { title: 'Backup & Restore',      url: '/admin/backup',         icon: 'database',         roles: ['admin'], keywords: 'backup database' },

        // Operator
        { title: 'Dashboard Operator',    url: '/operator/dashboard',   icon: 'layout-dashboard', roles: ['operator'], keywords: 'beranda home' },
        { title: 'Daftar Pendaftar',      url: '/operator/registrants', icon: 'graduation-cap',   roles: ['operator'], keywords: 'registrant siswa murid' },
        { title: 'Validasi Dapodik',      url: '/operator/dapodik',     icon: 'check-square',     roles: ['operator'], keywords: 'dapodik validasi' },

        // Pendaftar
        { title: 'Dashboard Pendaftar',   url: '/pendaftar/dashboard',  icon: 'layout-dashboard', roles: ['pendaftar'], keywords: 'beranda home' },
        { title: 'Formulir Pendaftaran',  url: '/pendaftar/daftar',     icon: 'file-text',        roles: ['pendaftar'], keywords: 'form isian data' },
        { title: 'Unggah Dokumen',        url: '/pendaftar/dokumen',    icon: 'folder-open',      roles: ['pendaftar'], keywords: 'document upload berkas' },
    ];

    // ── Open / Close ────────────────────────────────────────────────
    function openPalette() {
        lastTrigger = lastTrigger || document.activeElement;
        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
        input.value = '';
        activeIndex = -1;
        renderStaticMenu();
        input.focus();
        document.body.style.overflow = 'hidden';

        // Re-initialize lucide icons inside palette
        if (typeof lucide !== 'undefined') {
            setTimeout(() => lucide.createIcons(), 50);
        }
    }

    function closePalette() {
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        input.value = '';
        results.innerHTML = '';
        activeIndex = -1;
        document.body.style.overflow = '';
        if (lastTrigger && typeof lastTrigger.focus === 'function') {
            setTimeout(() => lastTrigger.focus(), 0);
        }
    }

    // ── Render static menu items ────────────────────────────────────
    function renderStaticMenu() {
        const filtered = menuItems.filter(item => 
            item.roles.length === 0 || item.roles.includes(role)
        );

        let html = '<div class="sp-command-group-label">Navigasi Cepat</div>';
        filtered.forEach((item, idx) => {
            html += renderItem(item, idx);
        });
        results.innerHTML = html;
        bindItemEvents();
    }

    // ── Render a single result item ─────────────────────────────────
    function renderItem(item, idx) {
        return `
            <a href="${item.url}" class="sp-command-item" data-index="${idx}">
                <div class="sp-command-item-icon">
                    <i data-lucide="${item.icon}"></i>
                </div>
                <div class="sp-command-item-content">
                    <div class="sp-command-item-title">${escapeHtml(item.title)}</div>
                    ${item.subtitle ? `<div class="sp-command-item-subtitle">${escapeHtml(item.subtitle)}</div>` : ''}
                </div>
                ${item.badge ? `<span class="sp-command-item-badge">${escapeHtml(item.badge)}</span>` : ''}
            </a>
        `;
    }

    // ── Search handler ──────────────────────────────────────────────
    function handleSearch(query) {
        if (!query || query.length < 1) {
            renderStaticMenu();
            return;
        }

        const q = query.toLowerCase();

        // Filter static menu items
        const menuMatches = menuItems
            .filter(item => {
                if (item.roles.length > 0 && !item.roles.includes(role)) return false;
                const searchable = (item.title + ' ' + (item.keywords || '')).toLowerCase();
                return searchable.includes(q);
            })
            .map(item => ({ ...item, type: 'menu' }));

        let html = '';

        if (menuMatches.length > 0) {
            html += '<div class="sp-command-group-label">Menu</div>';
            menuMatches.forEach((item, idx) => {
                html += renderItem(item, idx);
            });
        }

        // AJAX search for registrants (only for admin/operator)
        if ((role === 'admin' || role === 'operator') && query.length >= 2) {
            // Show searching state
            html += '<div class="sp-command-group-label">Pencarian Data...</div>';
            results.innerHTML = html;
            bindItemEvents();
            initLucide();

            // Debounced AJAX call
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchSearchResults(query, menuMatches.length);
            }, 300);
        } else {
            if (html === '') {
                html = '<div class="sp-command-empty"><i data-lucide="search-x" style="width:24px;height:24px;margin-bottom:8px;display:inline-block;"></i><br>Tidak ada hasil untuk "<strong>' + escapeHtml(query) + '</strong>"</div>';
            }
            results.innerHTML = html;
            bindItemEvents();
            initLucide();
        }
    }

    function fetchSearchResults(query, offset) {
        fetch(`/api/search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    let html = results.innerHTML;
                    // Remove "Pencarian Data..." label
                    html = html.replace(/<div class="sp-command-group-label">Pencarian Data\.\.\.<\/div>/, '');
                    html += '<div class="sp-command-group-label">Hasil Pencarian</div>';
                    data.results.forEach((item, idx) => {
                        html += renderItem({
                            title: item.title,
                            subtitle: item.subtitle || '',
                            url: item.url,
                            icon: item.icon || 'user',
                            badge: item.badge || ''
                        }, offset + idx);
                    });
                    results.innerHTML = html;
                } else {
                    let html = results.innerHTML;
                    html = html.replace(/<div class="sp-command-group-label">Pencarian Data\.\.\.<\/div>/, '');
                    if (!html.includes('sp-command-item')) {
                        html += '<div class="sp-command-empty"><i data-lucide="search-x" style="width:24px;height:24px;margin-bottom:8px;display:inline-block;"></i><br>Tidak ada hasil untuk "<strong>' + escapeHtml(query) + '</strong>"</div>';
                    }
                    results.innerHTML = html;
                }
                bindItemEvents();
                initLucide();
            })
            .catch(() => {
                // Silently fail for search
                let html = results.innerHTML;
                html = html.replace(/<div class="sp-command-group-label">Pencarian Data\.\.\.<\/div>/, '');
                results.innerHTML = html;
                initLucide();
            });
    }

    // ── Keyboard navigation ─────────────────────────────────────────
    function getItems() {
        return results.querySelectorAll('.sp-command-item');
    }

    function setActiveItem(index) {
        const items = getItems();
        items.forEach(el => el.classList.remove('active'));
        if (index >= 0 && index < items.length) {
            items[index].classList.add('active');
            items[index].scrollIntoView({ block: 'nearest' });
            activeIndex = index;
        } else {
            activeIndex = -1;
        }
    }

    function bindItemEvents() {
        const items = getItems();
        items.forEach((item, idx) => {
            item.addEventListener('mouseenter', () => setActiveItem(idx));
        });
    }

    function initLucide() {
        if (typeof lucide !== 'undefined') {
            try { lucide.createIcons(); } catch(e) { /* ignore */ }
        }
    }

    // ── Event Listeners ─────────────────────────────────────────────

    // Keyboard shortcut: Ctrl+K / Cmd+K
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (overlay.classList.contains('active')) {
                closePalette();
            } else {
                openPalette();
            }
        }

        // ESC to close
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            e.preventDefault();
            closePalette();
        }

        // Arrow navigation inside palette
        if (overlay.classList.contains('active')) {
            const items = getItems();
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                setActiveItem(activeIndex < items.length - 1 ? activeIndex + 1 : 0);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                setActiveItem(activeIndex > 0 ? activeIndex - 1 : items.length - 1);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    window.location.href = items[activeIndex].getAttribute('href');
                }
            }
        }
    });

    // Click overlay to close
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closePalette();
        }
    });

    // Search input handler
    input.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        handleSearch(query);
    });

    // Search box button click
    const openBtn = document.getElementById('open-command-palette');
    if (openBtn) {
        openBtn.addEventListener('click', () => {
            lastTrigger = openBtn;
            openPalette();
        });
        openBtn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                lastTrigger = openBtn;
                openPalette();
            }
        });
    }

    // ── Utility ─────────────────────────────────────────────────────
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
})();
