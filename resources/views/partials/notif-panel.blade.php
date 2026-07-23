{{-- Panel de notificaciones — dropdown en desktop, pantalla completa en mobile.
     Se incluye UNA sola vez en el layout, fuera del topbar/navbar, para que
     no quede oculto por los display:none responsivos de esos contenedores. --}}

<div class="notif-overlay" id="notifOverlay"></div>

<div class="notif-dropdown" id="notifDropdown"
     data-url-index="{{ route('notificaciones.index') }}"
     data-url-marcar-leida-template="{{ route('notificaciones.marcar-leida', ':id') }}">
    <div class="notif-dropdown__header">
        <span class="notif-dropdown__title">Notificaciones</span>
        <button type="button" class="notif-dropdown__close" id="notifClose" aria-label="Cerrar notificaciones">
            <i class="ti ti-x" aria-hidden="true"></i>
        </button>
    </div>

    <div class="notif-dropdown__list" id="notifList"></div>
    <div class="notif-dropdown__empty" id="notifEmpty" style="display:none;">
        <p>No tienes notificaciones.</p>
    </div>

    <div class="notif-dropdown__toolbar">
        <button type="button" class="notif-dropdown__mark-all" id="notifMarkAll"
                data-url-marcar-leidas="{{ route('notificaciones.marcar-leidas') }}">
            Marcar todas como leídas
        </button>
    </div>
</div>
