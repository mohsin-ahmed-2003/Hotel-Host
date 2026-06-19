/**
 * global.js
 * 1. Mark required labels with .required class
 * 2. Custom select dropdown (replaces all <select> elements)
 * 3. Country+code display helper for signup form
 */

/* ── 1. Required label asterisk ─────────────────────────────────────────── */
function markRequiredLabels() {
    document.querySelectorAll('input[required], select[required], textarea[required]').forEach(el => {
        // Find the associated label
        let label = null;
        if (el.id) label = document.querySelector(`label[for="${el.id}"]`);
        if (!label) label = el.closest('.form-group, .input-group, .field-group')?.querySelector('label');
        if (label && !label.classList.contains('required')) {
            label.classList.add('required');
        }
    });

    // Also mark labels that already have a data-required attribute
    document.querySelectorAll('[data-required]').forEach(el => el.classList.add('required'));
}

/* ── 2. Custom Select ────────────────────────────────────────────────────── */

/**
 * Build a custom dropdown from a native <select>.
 * Options:
 *   showSearch  — show search box (default: true if > 6 options)
 *   formatOption(opt) — returns HTML string for each option
 *   formatSelected(opt) — returns HTML string for the trigger display
 */
function buildCustomSelect(nativeSelect, opts = {}) {
    if (!nativeSelect || nativeSelect.dataset.csBuilt) return;
    nativeSelect.dataset.csBuilt = '1';

    const options     = Array.from(nativeSelect.options);
    const showSearch  = opts.showSearch ?? options.length > 6;
    const formatOpt   = opts.formatOption   || (o => escHtml(o.text));
    const formatSel   = opts.formatSelected || (o => escHtml(o.text));

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // Wrap the native select
    const wrap = document.createElement('div');
    wrap.className = 'custom-select-wrap';
    // Copy any extra classes from the select (e.g. form-control, field-input)
    if (nativeSelect.className) {
        nativeSelect.className.split(' ').forEach(c => {
            if (c && c !== 'form-control' && c !== 'field-input') wrap.classList.add(c);
        });
    }
    nativeSelect.parentNode.insertBefore(wrap, nativeSelect);
    wrap.appendChild(nativeSelect);

    // Trigger
    const trigger = document.createElement('div');
    trigger.className = 'cs-trigger';
    trigger.setAttribute('tabindex', '0');
    trigger.setAttribute('role', 'combobox');
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');

    const triggerText = document.createElement('span');
    triggerText.className = 'cs-text';

    const arrow = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    arrow.setAttribute('class', 'cs-arrow');
    arrow.setAttribute('viewBox', '0 0 24 24');
    arrow.setAttribute('fill', 'none');
    arrow.setAttribute('stroke', 'currentColor');
    arrow.setAttribute('stroke-width', '2.5');
    arrow.innerHTML = '<polyline points="6 9 12 15 18 9"/>';

    trigger.appendChild(triggerText);
    trigger.appendChild(arrow);

    // Panel
    const panel = document.createElement('div');
    panel.className = 'cs-panel';
    panel.setAttribute('role', 'listbox');

    // Search
    let searchInput = null;
    if (showSearch) {
        const searchWrap = document.createElement('div');
        searchWrap.className = 'cs-search';
        searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Search…';
        searchInput.autocomplete = 'off';
        searchWrap.appendChild(searchInput);
        panel.appendChild(searchWrap);
    }

    // Options list
    const list = document.createElement('div');
    list.className = 'cs-options';
    panel.appendChild(list);

    wrap.appendChild(trigger);
    wrap.appendChild(panel);

    // Render options
    function renderOptions(filter = '') {
        list.innerHTML = '';
        const q = filter.toLowerCase();
        options.forEach(opt => {
            if (opt.value === '' && !filter) {
                // placeholder
                const item = document.createElement('div');
                item.className = 'cs-option disabled';
                item.textContent = opt.text;
                list.appendChild(item);
                return;
            }
            if (q && !opt.text.toLowerCase().includes(q)) return;

            const item = document.createElement('div');
            item.className = 'cs-option' + (opt.value === nativeSelect.value ? ' selected' : '');
            item.dataset.value = opt.value;
            item.innerHTML = formatOpt(opt);
            item.addEventListener('mousedown', e => {
                e.preventDefault();
                selectOption(opt.value);
                close();
            });
            list.appendChild(item);
        });
    }

    function updateTrigger() {
        const sel = options.find(o => o.value === nativeSelect.value);
        triggerText.innerHTML = sel && sel.value ? formatSel(sel) : `<span style="color:#94a3b8">${options[0]?.text || 'Select…'}</span>`;
    }

    function selectOption(value) {
        nativeSelect.value = value;
        updateTrigger();
        renderOptions(searchInput?.value || '');
        nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function open() {
        wrap.classList.add('open');
        trigger.setAttribute('aria-expanded', 'true');
        renderOptions();
        if (searchInput) { searchInput.value = ''; searchInput.focus(); }
        // Scroll selected into view
        setTimeout(() => {
            const sel = list.querySelector('.selected');
            if (sel) sel.scrollIntoView({ block: 'nearest' });
        }, 50);
    }

    function close() {
        wrap.classList.remove('open');
        trigger.setAttribute('aria-expanded', 'false');
    }

    trigger.addEventListener('click', () => wrap.classList.contains('open') ? close() : open());
    trigger.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
        if (e.key === 'Escape') close();
    });

    if (searchInput) {
        searchInput.addEventListener('input', () => renderOptions(searchInput.value));
        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Escape') { close(); trigger.focus(); }
        });
    }

    // Close on outside click
    document.addEventListener('mousedown', e => {
        if (!wrap.contains(e.target)) close();
    });

    // Close on scroll
    window.addEventListener('scroll', () => {
        if (wrap.classList.contains('open')) close();
    }, { passive: true });

    updateTrigger();
}

/* ── 3. Country + code format for signup ────────────────────────────────── */
function initCountryCodeSelect(selectId) {
    const sel = document.getElementById(selectId);
    if (!sel) return;

    buildCustomSelect(sel, {
        showSearch: true,
        formatOption: opt => {
            if (!opt.value) return `<span style="color:#94a3b8">${opt.text}</span>`;
            const code = opt.dataset.phoneCode || opt.dataset.code || '';
            return code ? `${opt.dataset.name || opt.text} <span style="color:#94a3b8;font-size:12px;">(${code})</span>` : opt.text;
        },
        formatSelected: opt => {
            const code = opt.dataset.phoneCode || opt.dataset.code || '';
            return code ? `${opt.dataset.name || opt.text} <span style="color:#94a3b8;font-size:12px;">(${code})</span>` : opt.text;
        },
    });
}

/* ── Init all selects on page ────────────────────────────────────────────── */
function countryCodeOpts() {
    return {
        showSearch: true,
        formatOption: opt => {
            if (!opt.value) return `<span style="color:#94a3b8">${opt.text}</span>`;
            const code = opt.dataset.phoneCode || opt.dataset.code || '';
            const name = opt.dataset.name || opt.text;
            return code
                ? `${name} <span style="color:#94a3b8;font-size:12px;">(${code})</span>`
                : name;
        },
        formatSelected: opt => {
            const code = opt.dataset.phoneCode || opt.dataset.code || '';
            const name = opt.dataset.name || opt.text;
            return code
                ? `${name} <span style="color:#94a3b8;font-size:12px;">(${code})</span>`
                : name;
        },
    };
}

function initAllCustomSelects() {
    // Country selects with phone codes
    document.querySelectorAll('select[data-type="country-code"]').forEach(sel => {
        if (sel.dataset.csBuilt) return;
        buildCustomSelect(sel, countryCodeOpts());
    });

    // All other selects — skip ones inside .input-wrapper (rotating border animation)
    document.querySelectorAll('select:not([data-cs-skip]):not([data-cs-built]):not([data-type="country-code"])').forEach(sel => {
        if (sel.closest('.custom-select-wrap')) return;
        if (sel.closest('.input-wrapper')) return;
        buildCustomSelect(sel);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    markRequiredLabels();
    initAllCustomSelects();
});
