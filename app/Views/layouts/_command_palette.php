<!-- Command Palette — Global Search (Ctrl+K) -->
<div class="sp-command-overlay" id="commandPaletteOverlay" role="dialog" aria-label="Pencarian Global" aria-modal="true" aria-hidden="true">
    <div class="sp-command-palette">
        <div class="sp-command-input-wrapper">
            <i data-lucide="search"></i>
            <input type="text" id="commandPaletteInput" placeholder="Cari menu, pendaftar, atau aksi..." autocomplete="off" aria-label="Pencarian">
            <kbd>ESC</kbd>
        </div>
        <div class="sp-command-results" id="commandPaletteResults">
            <!-- Populated dynamically by JS -->
        </div>
        <div class="sp-command-footer">
            <span><kbd>↑↓</kbd> Navigasi</span>
            <span><kbd>↵</kbd> Buka</span>
            <span><kbd>ESC</kbd> Tutup</span>
        </div>
    </div>
</div>
