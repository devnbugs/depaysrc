import axios from 'axios';
import $ from 'jquery';
import { startAuthentication, startRegistration } from '@simplewebauthn/browser';
import { initializeApp } from "firebase/app";
import { getAnalytics, isSupported } from "firebase/analytics";

window.axios = axios;
window.$ = window.jQuery = $;
window.startAuthentication = startAuthentication;
window.startRegistration = startRegistration;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const THEME_STORAGE_KEY = 'depay-theme';
const prefersDarkMode = () => window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

const getBody = () => document.body || document.documentElement;

const escapeHtml = (value) => String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

const ensureToastHost = () => {
    let host = document.getElementById('depay-toast-host');

    if (!host) {
        host = document.createElement('div');
        host.id = 'depay-toast-host';
        host.className = 'app-toast-stack';
        getBody().appendChild(host);
    }

    return host;
};

const ensureBusyOverlay = () => {
    let overlay = document.getElementById('depay-busy-overlay');

    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'depay-busy-overlay';
        overlay.className = 'app-busy-overlay hidden';
        overlay.innerHTML = `
            <div class="app-busy-card">
                <span class="app-spinner" aria-hidden="true"></span>
                <div>
                    <p class="app-busy-title">Working</p>
                    <p class="app-busy-message">Please wait while we finish the request.</p>
                </div>
            </div>
        `;
        getBody().appendChild(overlay);
    }

    return overlay;
};

const applyTheme = (theme) => {
    const resolvedTheme = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.classList.toggle('dark', resolvedTheme === 'dark');
    document.documentElement.dataset.theme = resolvedTheme;
    document.querySelectorAll('[data-theme-icon="light"]').forEach((icon) => {
        icon.classList.toggle('hidden', resolvedTheme === 'dark');
    });
    document.querySelectorAll('[data-theme-icon="dark"]').forEach((icon) => {
        icon.classList.toggle('hidden', resolvedTheme !== 'dark');
    });
};

const readStoredTheme = () => {
    try {
        const stored = window.localStorage.getItem(THEME_STORAGE_KEY);

        if (stored === 'light' || stored === 'dark') {
            return stored;
        }
    } catch (_) {
        // Ignore storage access issues.
    }

    return prefersDarkMode() ? 'dark' : 'light';
};

const syncScrollLock = () => {
    const menuOpen = document.querySelector('[data-mobile-menu-panel][data-open="1"]');
    const confirmOpen = document.querySelector('[data-confirm-overlay][data-open="1"]');
    getBody().classList.toggle('overflow-hidden', Boolean(menuOpen || confirmOpen));
};

window.depayToast = (options = {}) => {
    const {
        title = 'Notice',
        message = '',
        tone = 'info',
        timeout = 5000,
    } = options;

    const host = ensureToastHost();
    const toast = document.createElement('div');
    toast.className = `app-toast app-toast-${tone}`;

    const copy = document.createElement('div');
    copy.className = 'app-toast-copy';

    const strong = document.createElement('strong');
    strong.textContent = title;

    const span = document.createElement('span');
    span.textContent = message;

    copy.append(strong, span);
    toast.append(copy);

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'app-toast-close';
    closeButton.setAttribute('aria-label', 'Dismiss notification');
    closeButton.textContent = '×';
    toast.append(closeButton);

    const close = () => {
        toast.classList.add('app-toast-leave');
        window.setTimeout(() => toast.remove(), 180);
    };

    closeButton.addEventListener('click', close);
    host.appendChild(toast);

    if (timeout > 0) {
        window.setTimeout(close, timeout);
    }
};

window.setAppBusy = (active = true, message = 'Please wait while we finish the request.') => {
    const overlay = ensureBusyOverlay();
    const title = overlay.querySelector('.app-busy-title');
    const copy = overlay.querySelector('.app-busy-message');

    if (copy) {
        copy.textContent = message;
    }

    if (title) {
        title.textContent = active ? 'Working' : 'Finished';
    }

    overlay.classList.toggle('hidden', !active);
};

const setConfirmIconTone = (icon, tone) => {
    const icons = {
        warning: `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 9v4"></path>
                <path d="M12 17h.01"></path>
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
            </svg>
        `,
        success: `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4"></path>
                <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
            </svg>
        `,
        danger: `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 9l-6 6"></path>
                <path d="M9 9l6 6"></path>
                <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
            </svg>
        `,
        info: `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 16v-4"></path>
                <path d="M12 8h.01"></path>
                <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
            </svg>
        `,
    };

    icon.dataset.tone = tone;
    icon.innerHTML = icons[tone] || icons.info;
};

let confirmOverlay = null;
let confirmResolver = null;

const closeConfirm = (value = false) => {
    if (!confirmOverlay || !confirmResolver) {
        return;
    }

    const resolver = confirmResolver;
    confirmResolver = null;
    confirmOverlay.dataset.open = '0';
    confirmOverlay.setAttribute('aria-hidden', 'true');
    syncScrollLock();
    window.setTimeout(() => {
        if (confirmOverlay) {
            confirmOverlay.removeAttribute('data-tone');
        }
    }, 180);
    resolver(value);
};

const ensureConfirmOverlay = () => {
    if (confirmOverlay) {
        return confirmOverlay;
    }

    confirmOverlay = document.createElement('div');
    confirmOverlay.id = 'depay-confirm-overlay';
    confirmOverlay.className = 'app-confirm-overlay';
    confirmOverlay.setAttribute('data-confirm-overlay', '');
    confirmOverlay.setAttribute('data-open', '0');
    confirmOverlay.setAttribute('aria-hidden', 'true');
    confirmOverlay.innerHTML = `
        <button type="button" class="app-confirm-backdrop" data-confirm-cancel aria-label="Close confirmation dialog"></button>
        <div class="app-confirm-card" role="dialog" aria-modal="true" aria-labelledby="depay-confirm-title">
            <div class="app-confirm-head">
                <div class="app-confirm-icon" data-confirm-icon data-tone="warning" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 9v4"></path>
                        <path d="M12 17h.01"></path>
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                    </svg>
                </div>
                <div class="app-confirm-copy">
                    <p class="app-confirm-kicker" data-confirm-kicker>Confirm action</p>
                    <h3 id="depay-confirm-title" class="app-confirm-title"></h3>
                    <p class="app-confirm-message" data-confirm-message></p>
                </div>
            </div>
            <dl class="app-confirm-summary hidden" data-confirm-summary></dl>
            <div class="app-confirm-actions">
                <button type="button" class="app-confirm-button app-confirm-cancel" data-confirm-cancel>Cancel</button>
                <button type="button" class="app-confirm-button app-confirm-accept" data-confirm-accept>Continue</button>
            </div>
        </div>
    `;

    const backdrop = confirmOverlay.querySelector('[data-confirm-cancel]');
    const acceptButton = confirmOverlay.querySelector('[data-confirm-accept]');
    const cancelButtons = confirmOverlay.querySelectorAll('[data-confirm-cancel]');

    const bindClose = (shouldProceed) => {
        closeConfirm(shouldProceed);
    };

    cancelButtons.forEach((button) => {
        button.addEventListener('click', () => bindClose(false));
    });

    acceptButton?.addEventListener('click', () => bindClose(true));
    backdrop?.addEventListener('click', () => bindClose(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && confirmResolver) {
            closeConfirm(false);
        }
    });

    getBody().appendChild(confirmOverlay);
    return confirmOverlay;
};

const readFieldValue = (field) => {
    if (!field) {
        return '';
    }

    if (field instanceof RadioNodeList) {
        return readFieldValue(field[0]);
    }

    const type = (field.type || '').toLowerCase();

    if (type === 'file') {
        return field.files && field.files.length ? field.files[0].name : '';
    }

    if (type === 'checkbox') {
        return field.checked ? (field.value || 'Yes') : '';
    }

    if (field.tagName === 'SELECT') {
        return field.options[field.selectedIndex]?.textContent?.trim() || field.value || '';
    }

    return field.value || field.textContent || '';
};

const buildPurchaseSummary = (form) => {
    const labels = {
        network: 'Network',
        company: 'Company',
        datatype: 'Data type',
        plan: 'Plan',
        beneficiary: 'Beneficiary',
        phone: 'Phone',
        mobile: 'Phone',
        username: 'Account number',
        number: 'Number',
        amount: 'Amount',
        pin_code: 'PIN',
        type: 'Type',
    };
    const fieldOrder = ['network', 'company', 'datatype', 'plan', 'beneficiary', 'phone', 'mobile', 'username', 'number', 'amount', 'pin_code'];

    return fieldOrder.reduce((items, name) => {
        const field = form.elements.namedItem(name);
        const value = readFieldValue(field);

        if (!value) {
            return items;
        }

        items.push({
            label: labels[name] || name,
            value: name === 'pin_code' ? '••••' : value,
        });

        return items;
    }, []);
};

const renderConfirmSummary = (summaryHost, items) => {
    summaryHost.innerHTML = items.map((item) => (
        `<div class="app-confirm-summary-item">
            <span>${escapeHtml(item.label)}</span>
            <strong>${escapeHtml(item.value)}</strong>
        </div>`
    )).join('');
    summaryHost.classList.toggle('hidden', items.length === 0);
};

window.depayConfirm = (options = {}) => {
    const overlay = ensureConfirmOverlay();
    const title = options.title || 'Confirm action';
    const message = options.message || 'Please review the details before continuing.';
    const tone = options.tone || 'warning';
    const confirmText = options.confirmText || 'Continue';
    const cancelText = options.cancelText || 'Cancel';
    const kicker = options.kicker || (tone === 'danger' ? 'Danger' : tone === 'success' ? 'Successful' : tone === 'info' ? 'Notice' : 'Warning');
    const showCancel = options.showCancel !== false;
    const summary = Array.isArray(options.summary) ? options.summary : [];

    const titleNode = overlay.querySelector('.app-confirm-title');
    const messageNode = overlay.querySelector('[data-confirm-message]');
    const kickerNode = overlay.querySelector('[data-confirm-kicker]');
    const summaryNode = overlay.querySelector('[data-confirm-summary]');
    const actionsNode = overlay.querySelector('.app-confirm-actions');
    const acceptButton = overlay.querySelector('[data-confirm-accept]');
    const cancelButton = overlay.querySelector('.app-confirm-cancel');
    const icon = overlay.querySelector('[data-confirm-icon]');

    if (titleNode) {
        titleNode.textContent = title;
    }

    if (messageNode) {
        messageNode.textContent = message;
    }

    if (kickerNode) {
        kickerNode.textContent = kicker;
    }

    if (summaryNode) {
        renderConfirmSummary(summaryNode, summary);
    }

    if (acceptButton) {
        acceptButton.textContent = confirmText;
    }

    if (cancelButton) {
        cancelButton.textContent = cancelText;
        cancelButton.classList.toggle('hidden', !showCancel);
    }

    if (actionsNode) {
        actionsNode.dataset.singleAction = showCancel ? '0' : '1';
    }

    if (icon) {
        setConfirmIconTone(icon, tone);
    }

    overlay.dataset.open = '1';
    overlay.dataset.tone = tone;
    overlay.setAttribute('aria-hidden', 'false');
    syncScrollLock();

    return new Promise((resolve) => {
        if (confirmResolver) {
            confirmResolver(false);
        }

        confirmResolver = resolve;

        window.setTimeout(() => {
            acceptButton?.focus();
        }, 0);
    });
};

const getConfirmOptionsFromElement = (element, fallback = {}) => {
    const tone = element.getAttribute('data-confirm-tone') || fallback.tone || 'warning';
    const title = element.getAttribute('data-confirm-title') || fallback.title || 'Confirm action';
    const message = element.getAttribute('data-confirm-message') || fallback.message || 'Please review the details before continuing.';
    const confirmText = element.getAttribute('data-confirm-accept-text') || fallback.confirmText || (tone === 'danger' ? 'Delete' : 'Continue');
    const cancelText = element.getAttribute('data-confirm-cancel-text') || fallback.cancelText || 'Cancel';
    return { tone, title, message, confirmText, cancelText };
};

const closeAnyMobileMenus = () => {
    document.querySelectorAll('[data-mobile-menu-panel][data-open="1"]').forEach((panel) => {
        panel.dataset.open = '0';
        panel.setAttribute('aria-hidden', 'true');
    });
    syncScrollLock();
};

const openMobileMenu = (panel, trigger) => {
    if (!panel) {
        return;
    }

    panel.dataset.open = '1';
    panel.setAttribute('aria-hidden', 'false');

    if (trigger) {
        trigger.setAttribute('aria-expanded', 'true');
    }

    syncScrollLock();
};

const toggleMobileMenu = (panel, trigger) => {
    if (!panel) {
        return;
    }

    const isOpen = panel.dataset.open === '1';
    closeAnyMobileMenus();

    if (!isOpen) {
        openMobileMenu(panel, trigger);
    }
};

const copyToClipboard = async (text) => {
    if (!text) {
        return false;
    }

    try {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(text);
            return true;
        }
    } catch (_) {
        // Fall through to the legacy method.
    }

    const helper = document.createElement('textarea');
    helper.value = text;
    helper.setAttribute('readonly', 'readonly');
    helper.style.position = 'fixed';
    helper.style.left = '-9999px';
    helper.style.top = '0';
    getBody().appendChild(helper);
    helper.select();
    helper.setSelectionRange(0, helper.value.length);

    let success = false;

    try {
        success = document.execCommand('copy');
    } catch (_) {
        success = false;
    }

    helper.remove();
    return success;
};

const resolveCopyValue = (trigger) => {
    const explicit = trigger.getAttribute('data-copy-value');
    if (explicit) {
        return explicit;
    }

    const targetSelector = trigger.getAttribute('data-copy-target');
    if (targetSelector) {
        const target = document.querySelector(targetSelector);
        if (target) {
            if ('value' in target && typeof target.value === 'string') {
                return target.value;
            }

            return target.textContent?.trim() || '';
        }
    }

    return trigger.textContent?.trim() || '';
};

const syncGlobalInteractions = () => {
    if (document.body.dataset.depayInteractionsBound === '1') {
        return;
    }

    document.body.dataset.depayInteractionsBound = '1';

    document.addEventListener('click', async (event) => {
        const mobileToggle = event.target.closest('[data-mobile-menu-toggle]');
        if (mobileToggle) {
            event.preventDefault();
            const selector = mobileToggle.getAttribute('data-mobile-menu-toggle');
            const panel = selector ? document.querySelector(selector) : null;
            toggleMobileMenu(panel, mobileToggle);
            return;
        }

        const mobileClose = event.target.closest('[data-mobile-menu-close]');
        if (mobileClose) {
            event.preventDefault();
            const panel = mobileClose.closest('[data-mobile-menu-panel]');
            if (panel) {
                panel.dataset.open = '0';
                panel.setAttribute('aria-hidden', 'true');
                syncScrollLock();
            }
            return;
        }

        const mobileLink = event.target.closest('[data-mobile-menu-panel] a[href]');
        if (mobileLink) {
            closeAnyMobileMenus();
        }

        const copyTrigger = event.target.closest('[data-copy-value], [data-copy-target]');
        if (copyTrigger) {
            event.preventDefault();
            const copied = await copyToClipboard(resolveCopyValue(copyTrigger));
            window.depayToast({
                title: copied ? 'Copied' : 'Copy failed',
                message: copied ? 'The selected value was copied to your clipboard.' : 'Your browser blocked clipboard access.',
                tone: copied ? 'success' : 'error',
                timeout: copied ? 2500 : 4500,
            });
            return;
        }

        const confirmLink = event.target.closest('[data-confirm-link]');
        if (confirmLink) {
            event.preventDefault();
            const options = getConfirmOptionsFromElement(confirmLink, {
                tone: 'danger',
                title: 'Confirm action',
                message: 'Please confirm that you want to continue.',
                confirmText: confirmLink.getAttribute('data-confirm-accept-text') || 'Continue',
            });

            const proceed = await window.depayConfirm(options);
            if (!proceed) {
                return;
            }

            const href = confirmLink.getAttribute('href');
            if (href && href !== '#') {
                window.location.href = href;
            }
            return;
        }
    }, true);

    document.addEventListener('submit', (event) => {
        const form = event.target instanceof HTMLFormElement ? event.target : null;
        if (!form) {
            return;
        }

        const needsConfirm = form.dataset.confirmForm === '1'
            || form.dataset.confirmForm === 'true'
            || form.id === 'purchase';

        if (form.dataset.confirmBypass === '1') {
            delete form.dataset.confirmBypass;
            return;
        }

        if (!needsConfirm) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        const submitter = event.submitter || null;
        const options = getConfirmOptionsFromElement(form, {
            tone: form.id === 'purchase' ? 'warning' : 'danger',
            title: form.getAttribute('data-confirm-title') || (form.id === 'purchase' ? 'Confirm purchase' : 'Confirm submission'),
            message: form.getAttribute('data-confirm-message') || (form.id === 'purchase'
                ? 'Please review the phone number, amount, and package before you continue.'
                : 'Please review the details before you continue.'),
            confirmText: form.getAttribute('data-confirm-accept-text') || (form.id === 'purchase' ? 'Proceed' : 'Continue'),
        });

        const summaryMode = form.getAttribute('data-confirm-summary') || 'auto';
        if (summaryMode !== 'none') {
            options.summary = buildPurchaseSummary(form);
        }

        window.depayConfirm(options).then((proceed) => {
            if (!proceed) {
                return;
            }

            form.dataset.confirmBypass = '1';

            if (typeof form.requestSubmit === 'function') {
                if (submitter && submitter.form === form) {
                    form.requestSubmit(submitter);
                } else {
                    form.requestSubmit();
                }
                return;
            }

            if (window.setAppBusy) {
                window.setAppBusy(true, form.dataset.busyMessage || 'Please wait while we finish the request.');
            }

            form.submit();
        });
    }, true);

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        if (confirmResolver) {
            closeConfirm(false);
            return;
        }

        closeAnyMobileMenus();
    });
};

const syncThemeButtons = () => {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            applyTheme(nextTheme);

            try {
                window.localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
            } catch (_) {
                // Ignore storage access issues.
            }
        });
    });
};

const processFlashQueue = async () => {
    const queue = Array.isArray(window.__depayFlashQueue) ? [...window.__depayFlashQueue] : [];
    window.__depayFlashQueue = [];

    for (const item of queue) {
        if (!item || typeof item !== 'object') {
            continue;
        }

        if (item.kind === 'notify') {
            await window.depayAlert({
                type: item.tone || 'info',
                title: item.title || 'Notice',
                message: item.message || '',
            });
            continue;
        }

        if (item.kind === 'alertx') {
            await window.depayAlert({
                type: item.type || 'info',
                title: item.title || 'Notice',
                message: item.message || '',
                receiptUrl: item.receiptUrl || '',
            });
        }
    }
};

const syncLoadingButtons = () => {
    document.querySelectorAll('[data-busy-form]').forEach((form) => {
        if (form.dataset.busyBound === '1') {
            return;
        }

        form.dataset.busyBound = '1';
        form.addEventListener('submit', () => {
            const message = form.getAttribute('data-busy-message') || 'Please wait while we finish the request.';
            window.setAppBusy(true, message);
        });
    });
};

window.depayAlert = (payload = {}) => {
    const {
        type = 'info',
        title = 'Notice',
        message = '',
        receiptUrl = '',
    } = payload;

    if (type === 'trxsuccess' && receiptUrl) {
        return window.depayConfirm({
            tone: 'success',
            kicker: 'Successful',
            title: title || 'Transaction successful',
            message: message || 'Your transaction completed successfully. Do you want to open the receipt now?',
            confirmText: 'Open receipt',
            cancelText: 'Later',
            summary: [],
        }).then((proceed) => {
            if (proceed) {
                window.location.href = receiptUrl;
            }
        });
    }

    const tone = type === 'error'
        ? 'danger'
        : type === 'warning'
            ? 'warning'
            : type === 'success'
                ? 'success'
                : 'info';

    return window.depayConfirm({
        tone,
        kicker: tone === 'danger'
            ? 'Danger'
            : tone === 'warning'
                ? 'Warning'
                : tone === 'success'
                    ? 'Successful'
                    : 'Notice',
        title: title || 'Notice',
        message,
        confirmText: 'Okay',
        showCancel: false,
        summary: [],
    });
};

const init = () => {
    applyTheme(readStoredTheme());
    syncThemeButtons();
    syncLoadingButtons();
    syncGlobalInteractions();
    processFlashQueue();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

const firebaseConfig = {
    apiKey: "AIzaSyAQFKNcCinBRuy2EK5ZNkbtyv5dRbQkSfY",
    authDomain: "depay-prod.firebaseapp.com",
    projectId: "depay-prod",
    storageBucket: "depay-prod.firebasestorage.app",
    messagingSenderId: "361119565907",
    appId: "1:361119565907:web:6856230cf1a148f88a1ea6",
    measurementId: "G-T524903Y5N"
};

const app = initializeApp(firebaseConfig);

isSupported().then((supported) => {
    if (supported) {
        getAnalytics(app);
    }
});
