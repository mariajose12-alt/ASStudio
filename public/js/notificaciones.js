document.addEventListener('DOMContentLoaded', function () {
    // ── Triggers (pueden coexistir o no, según la vista) ──
    const notifBtn        = document.getElementById('notifBtn');          // campanita desktop (topbar)
    const mobileNotifLink = document.getElementById('mobileNotifLink');   // ítem dentro del drawer mobile
    const hamburgerBadge  = document.getElementById('hamburgerBadge');    // puntito rojo sobre el hamburger

    // ── Panel compartido (dropdown en desktop, pantalla completa en mobile) ──
    const dropdown    = document.getElementById('notifDropdown');
    const overlay     = document.getElementById('notifOverlay');
    const closeBtn     = document.getElementById('notifClose');
    const badge        = document.getElementById('notifBadge');
    const list         = document.getElementById('notifList');
    const empty        = document.getElementById('notifEmpty');
    const markAll       = document.getElementById('notifMarkAll');
    const mobileBadge  = document.getElementById('mobileNotifBadge');

    if (!dropdown) return; // esta vista no tiene sistema de notificaciones

    const urlIndex              = dropdown.dataset.urlIndex;
    const urlMarcarLeidas       = markAll?.dataset.urlMarcarLeidas;
    const urlMarcarLeidaTemplate = dropdown.dataset.urlMarcarLeidaTemplate;
    const csrfMeta        = document.querySelector('meta[name="csrf-token"]');
    const csrfToken       = csrfMeta?.content;

    if (!csrfToken) {
        console.warn('Falta <meta name="csrf-token"> en el <head> — "marcar todas como leídas" fallará.');
    }

    let isOpen = false;
    let marcandoLeidas = false;

    function render(data) {
        const hayNoLeidas = data.no_leidas > 0;

        // Badge desktop (puntito sobre la campanita del topbar)
        if (badge) badge.style.display = hayNoLeidas ? 'block' : 'none';

        // Badge sobre el hamburger (puntito, mobile)
        if (hamburgerBadge) hamburgerBadge.style.display = hayNoLeidas ? 'block' : 'none';

        // Badge dentro del drawer (número, mobile)
        if (mobileBadge) {
            if (hayNoLeidas) {
                mobileBadge.textContent = data.no_leidas > 99 ? '99+' : data.no_leidas;
                mobileBadge.style.display = 'inline-flex';
            } else {
                mobileBadge.style.display = 'none';
            }
        }

        if (data.notificaciones.length === 0) {
            list.innerHTML = '';
            empty.style.display = 'block';
            return;
        }
        empty.style.display = 'none';

        list.innerHTML = data.notificaciones.map(n => `
            <a href="${n.url || '#'}" class="notif-item ${n.leida ? '' : 'no-leida'}" data-id="${n.id}">
                <span class="notif-item__icon"><i class="ti ti-${n.icono}"></i></span>
                <span class="notif-item__body">
                    <span class="notif-item__titulo">${n.titulo}</span>
                    <span class="notif-item__mensaje">${n.mensaje}</span>
                    <span class="notif-item__fecha">${n.fecha}</span>
                </span>
            </a>
        `).join('');
    }

    function mostrarErrorCarga() {
        list.innerHTML = '';
        empty.style.display = 'block';
        empty.querySelector('p').textContent = 'No se pudieron cargar las notificaciones.';
    }

    function fetchNotificaciones() {
        fetch(urlIndex)
            .then(r => {
                if (!r.ok) throw new Error('Respuesta no OK: ' + r.status);
                return r.json();
            })
            .then(render)
            .catch(err => {
                console.error('Error cargando notificaciones:', err);
                mostrarErrorCarga();
            });
    }

    function esMobile() {
        return window.innerWidth <= 768;
    }

    function posicionarDropdown() {
        // En mobile el panel es full-screen (CSS via .is-open), no necesita coordenadas
        if (esMobile() || !notifBtn) {
            dropdown.style.top = '';
            dropdown.style.right = '';
            dropdown.style.left = '';
            return;
        }
        const rect = notifBtn.getBoundingClientRect();
        dropdown.style.top   = (rect.bottom + 12) + 'px';
        dropdown.style.right = (window.innerWidth - rect.right) + 'px';
        dropdown.style.left  = 'auto';
    }

    function openPanel() {
        isOpen = true;
        posicionarDropdown();
        dropdown.classList.add('is-open');
        overlay?.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        notifBtn?.setAttribute('aria-expanded', 'true');
        fetchNotificaciones();
    }

    // Reposicionar si cambia el tamaño de ventana mientras está abierto
    window.addEventListener('resize', function () {
        if (isOpen) posicionarDropdown();
    });

    function closePanel() {
        isOpen = false;
        dropdown.classList.remove('is-open');
        overlay?.classList.remove('is-open');
        document.body.style.overflow = '';
        notifBtn?.setAttribute('aria-expanded', 'false');
    }

    // ── Trigger: campanita en topbar (desktop) ──
    notifBtn?.addEventListener('click', function (e) {
        e.preventDefault();
        isOpen ? closePanel() : openPanel();
    });

    // ── Trigger: ítem "Notificaciones" dentro del drawer mobile ──
    mobileNotifLink?.addEventListener('click', function (e) {
        e.preventDefault();
        // Cierra el drawer (función global definida en navbar-scripts.blade.php)
        if (typeof closeMobileMenu === 'function') closeMobileMenu();
        openPanel();
    });

    // Cerrar con botón X (visible solo en mobile) y con el overlay
    closeBtn?.addEventListener('click', closePanel);
    overlay?.addEventListener('click', closePanel);

    // Cerrar al hacer clic afuera (comportamiento dropdown, desktop)
    document.addEventListener('click', function (e) {
        if (!isOpen) return;
        const clickedTrigger = notifBtn?.contains(e.target) || mobileNotifLink?.contains(e.target);
        if (!dropdown.contains(e.target) && !clickedTrigger) {
            closePanel();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen) closePanel();
    });

    markAll?.addEventListener('click', function () {
        if (marcandoLeidas || !csrfToken) return;

        marcandoLeidas = true;
        const textoOriginal = markAll.textContent;
        markAll.textContent = 'Marcando...';
        markAll.disabled = true;

        fetch(urlMarcarLeidas, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
            .then(r => {
                if (!r.ok) throw new Error('Respuesta no OK: ' + r.status);
                return fetchNotificaciones();
            })
            .catch(err => console.error('Error marcando como leídas:', err))
            .finally(() => {
                marcandoLeidas = false;
                markAll.textContent = textoOriginal;
                markAll.disabled = false;
            });
    });

    // Marcar como leída al hacer clic en una notificación individual
    list?.addEventListener('click', function (e) {
        const item = e.target.closest('.notif-item');
        if (!item) return;

        // Si ya estaba leída, no hay nada que marcar — deja que navegue normal
        if (!item.classList.contains('no-leida')) return;

        e.preventDefault();

        const id = item.dataset.id;
        const url = urlMarcarLeidaTemplate.replace(':id', id);

        fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
            .then(r => {
                if (!r.ok) throw new Error('Respuesta no OK: ' + r.status);
                // Navega después de marcar como leída (misma pestaña, comportamiento normal de <a>)
                window.location.href = item.getAttribute('href');
            })
            .catch(err => {
                console.error('Error marcando notificación como leída:', err);
                // Si falla, igual navega — no bloquear al usuario por esto
                window.location.href = item.getAttribute('href');
            });
    });

    // Polling para mantener los badges al día (se pausa en 2do plano)
    let intervalId = setInterval(fetchNotificaciones, 40000);

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            clearInterval(intervalId);
        } else {
            fetchNotificaciones();
            intervalId = setInterval(fetchNotificaciones, 40000);
        }
    });

    // Carga inicial: solo para poblar los badges, sin abrir el panel
    fetchNotificaciones();
});
