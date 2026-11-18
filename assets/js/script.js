// LaundryKu - Shared frontend scripts

document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu toggle
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    if (btn && menu) {
        btn.addEventListener('click', () => menu.classList.toggle('hidden'));
    }

    // Auto-dismiss flash messages
    const flash = document.getElementById('flash-container');
    if (flash) setTimeout(() => { flash.style.transition = 'opacity .4s'; flash.style.opacity = '0'; setTimeout(()=>flash.remove(), 400); }, 5000);

    // Cancel form on customer detail if exists
    const cancelForm = document.getElementById('cancel-form');
    if (cancelForm) {
        cancelForm.addEventListener('submit', (ev) => {
            if (!confirm('Yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dibatalkan.')) {
                ev.preventDefault();
            }
        });
    }

    // Admin delete/important confirmations
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (ev) => {
            if (!confirm(el.dataset.confirm)) ev.preventDefault();
        });
    });
});

// ---- Pemesanan: hitung estimasi otomatis ----
function initOrderCalculator() {
    const select = document.getElementById('service-select');
    const qtyInput = document.getElementById('quantity');
    const estimateEl = document.getElementById('estimate-total');
    const unitLabel = document.getElementById('unit-label');
    if (!select || !qtyInput || !estimateEl) return;

    function recalc() {
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.dataset.price) { estimateEl.textContent = 'Rp0'; return; }
        const price = parseFloat(opt.dataset.price);
        const qty = Math.max(0, parseFloat(qtyInput.value) || 0);
        const total = price * qty;
        estimateEl.textContent = 'Rp' + total.toLocaleString('id-ID');
        if (unitLabel && opt.dataset.unit) unitLabel.textContent = opt.dataset.unit;
    }
    select.addEventListener('change', recalc);
    qtyInput.addEventListener('input', recalc);
    recalc();
}
document.addEventListener('DOMContentLoaded', initOrderCalculator);
