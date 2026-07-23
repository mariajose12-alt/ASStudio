@extends('layouts.fotografo')
@section('title', 'Calendario de Reservas')

@section('content')
    {{-- ── LAYOUT 2 COLUMNAS ───────────────────────────────── --}}
    <div class="cal-page">

        {{-- ── COLUMNA PRINCIPAL: CALENDARIO CUSTOM ───────────── --}}
        <div>
            <div class="cal-main-card">
                <div class="cal-main-card__inner">

                    <div class="cal-toolbar">
                        <button class="cal-nav-btn" id="btn-mes-anterior" aria-label="Mes anterior">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"/>
                            </svg>
                        </button>
                        <div class="cal-toolbar__title" id="cal-titulo-mes"></div>
                        <button class="cal-nav-btn" id="btn-mes-siguiente" aria-label="Mes siguiente">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </button>
                    </div>

                    <div class="cal-weekdays">
                        <div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div><div>Dom</div>
                    </div>

                    <div class="cal-grid" id="cal-grid"></div>

                </div>
            </div>
        </div>

        {{-- ── COLUMNA LATERAL: DETAIL + STATS (sin cambios) ──── --}}
        <div class="cal-sidebar">

            <div class="cal-empty-panel" id="panel-vacio">
                <div class="cal-empty-panel__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                </div>
                <p class="cal-empty-panel__title">Selecciona una reserva</p>
                <p class="cal-empty-panel__text">Haz clic en cualquier evento del calendario para ver sus detalles aquí.</p>
            </div>

            <div class="cal-detail-panel" id="panel-detalle">
                <div class="cal-detail-panel__accent"></div>
                <div class="cal-detail-panel__body">
                    <div class="cal-detail-panel__header">
                        <div class="cal-detail-panel__eyebrow">Detalle de reserva</div>
                        <div class="cal-detail-panel__title" id="detail-titulo"></div>
                        <button class="cal-detail-panel__close" onclick="cerrarDetalle()" title="Cerrar">×</button>
                    </div>
                    <div class="cal-detail-panel__rows">
                        <div class="cal-detail-row">
                            <div class="cal-detail-row__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <div>
                                <div class="cal-detail-row__label">Cliente</div>
                                <div class="cal-detail-row__value" id="detail-cliente"></div>
                            </div>
                        </div>
                        <div class="cal-detail-row">
                            <div class="cal-detail-row__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                </svg>
                            </div>
                            <div>
                                <div class="cal-detail-row__label">Tipo de sesión</div>
                                <div class="cal-detail-row__value" id="detail-tipo"></div>
                            </div>
                        </div>
                        <div class="cal-detail-row">
                            <div class="cal-detail-row__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </div>
                            <div>
                                <div class="cal-detail-row__label">Lugar</div>
                                <div class="cal-detail-row__value" id="detail-lugar"></div>
                            </div>
                        </div>
                        <div class="cal-detail-row">
                            <div class="cal-detail-row__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <div class="cal-detail-row__label">Fecha y hora</div>
                                <div class="cal-detail-row__value" id="detail-inicio"></div>
                            </div>
                        </div>
                        <div class="cal-detail-row">
                            <div class="cal-detail-row__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <div>
                                <div class="cal-detail-row__label">Estado</div>
                                <div id="detail-estado"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cal-summary-panel">
                <div class="cal-summary-panel__header">
                    <div class="cal-summary-panel__title">Resumen del mes</div>
                </div>
                <div class="cal-summary-panel__body">
                    <div class="cal-stat-row">
                        <div class="cal-stat-row__left">
                            <span class="cal-stat-row__dot" style="background:#059669;"></span>
                            <span class="cal-stat-row__label">Confirmadas</span>
                        </div>
                        <span class="cal-stat-row__count" id="count-confirmadas">—</span>
                    </div>
                    <div class="cal-stat-row">
                        <div class="cal-stat-row__left">
                            <span class="cal-stat-row__dot" style="background:#d97706;"></span>
                            <span class="cal-stat-row__label">Pendientes</span>
                        </div>
                        <span class="cal-stat-row__count" id="count-pendientes">—</span>
                    </div>
                    <div class="cal-stat-row">
                        <div class="cal-stat-row__left">
                            <span class="cal-stat-row__dot" style="background:#9ca3af;"></span>
                            <span class="cal-stat-row__label">Completadas</span>
                        </div>
                        <span class="cal-stat-row__count" id="count-completadas">—</span>
                    </div>
                    <div class="cal-stat-row">
                        <div class="cal-stat-row__left">
                            <span class="cal-stat-row__dot" style="background:#dc2626;"></span>
                            <span class="cal-stat-row__label">Canceladas</span>
                        </div>
                        <span class="cal-stat-row__count" id="count-canceladas">—</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── HEADER ──────────────────────────────────────────── --}}
    <div class="cal-header">
        <div class="cal-header__actions">
            <div class="cal-legend">
                <span class="cal-legend__item">
                    <span class="cal-legend__dot" style="background:#059669;"></span> Confirmada
                </span>
                <span class="cal-legend__item">
                    <span class="cal-legend__dot" style="background:#d97706;"></span> Pendiente
                </span>
                <span class="cal-legend__item">
                    <span class="cal-legend__dot" style="background:#9ca3af;"></span> Completada
                </span>
                <span class="cal-legend__item">
                    <span class="cal-legend__dot" style="background:#dc2626;"></span> Cancelada
                </span>
            </div>
        </div>
    </div>

    {{-- ── MODAL (mobile) ── --}}
    <div id="cal-modal-overlay" class="cal-modal-overlay" role="dialog" aria-modal="true">
        <div class="cal-modal">
            <div class="cal-modal__accent"></div>
            <div class="cal-modal__header">
                <div>
                    <div class="cal-modal__eyebrow">Detalle de reserva</div>
                    <div class="cal-modal__title" id="modal-titulo-v2"></div>
                </div>
                <button class="cal-modal__close" onclick="cerrarModal()" aria-label="Cerrar">×</button>
            </div>
            <div class="cal-modal__body">
                <div class="cal-modal__grid">
                    <div class="cal-modal__field">
                        <div class="cal-modal__field-label">Cliente</div>
                        <div class="cal-modal__field-value" id="modal-cliente-v2"></div>
                    </div>
                    <div class="cal-modal__field">
                        <div class="cal-modal__field-label">Estado</div>
                        <div id="modal-estado-v2"></div>
                    </div>
                    <div class="cal-modal__field">
                        <div class="cal-modal__field-label">Tipo de sesión</div>
                        <div class="cal-modal__field-value" id="modal-tipo-v2"></div>
                    </div>
                    <div class="cal-modal__field">
                        <div class="cal-modal__field-label">Fecha y hora</div>
                        <div class="cal-modal__field-value" id="modal-inicio-v2"></div>
                    </div>
                    <div class="cal-modal__field cal-modal__field--full">
                        <div class="cal-modal__field-label">Lugar</div>
                        <div class="cal-modal__field-value" id="modal-lugar-v2"></div>
                    </div>
                </div>
            </div>
            <div class="cal-modal__footer">
                <button class="cal-btn cal-btn--ghost" onclick="cerrarModal()">Cerrar</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        (function() {
            const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

            let fechaActual = new Date();
            fechaActual.setDate(1);

            const ESTADO_COLORS = {
                confirmada: { bg: '#059669', text: '#fff' },
                pendiente:  { bg: '#f59e0b', text: '#fff' },
                completada: { bg: '#9ca3af', text: '#fff' },
                cancelada:  { bg: '#ef4444', text: '#fff' },
            };

            function colorForEvent(estado) {
                const key = (estado || '').toLowerCase();
                return ESTADO_COLORS[key] || { bg: '#e87722', text: '#fff' };
            }

            function estadoBadgeHTML(estado) {
                const map = { confirmada:'estado-confirmada', pendiente:'estado-pendiente', completada:'estado-completada', cancelada:'estado-cancelada' };
                const key   = (estado || '').toLowerCase();
                const cls   = map[key] || 'estado-default';
                const label = estado ? estado.charAt(0).toUpperCase() + estado.slice(1) : '—';
                return `<span class="estado-badge ${cls}">${label}</span>`;
            }

            function isMobile() { return window.innerWidth <= 1100; }

            function abrirDetalle(ev) {
                const fecha  = ev.start ? new Date(ev.start).toLocaleString('es-DO', { dateStyle:'long', timeStyle:'short' }) : '—';
                const titulo = ev.title || '—';
                const p      = ev.extendedProps || {};

                if (isMobile()) {
                    document.getElementById('modal-titulo-v2').textContent  = titulo;
                    document.getElementById('modal-cliente-v2').textContent = p.cliente || '—';
                    document.getElementById('modal-tipo-v2').textContent    = p.tipo    || '—';
                    document.getElementById('modal-lugar-v2').textContent   = p.lugar   || '—';
                    document.getElementById('modal-inicio-v2').textContent  = fecha;
                    document.getElementById('modal-estado-v2').innerHTML    = estadoBadgeHTML(p.estado);
                    document.getElementById('cal-modal-overlay').classList.add('is-open');
                    document.body.style.overflow = 'hidden';
                } else {
                    document.getElementById('detail-titulo').textContent  = titulo;
                    document.getElementById('detail-cliente').textContent = p.cliente || '—';
                    document.getElementById('detail-tipo').textContent    = p.tipo    || '—';
                    document.getElementById('detail-lugar').textContent   = p.lugar   || '—';
                    document.getElementById('detail-inicio').textContent  = fecha;
                    document.getElementById('detail-estado').innerHTML    = estadoBadgeHTML(p.estado);

                    const panelDetalle = document.getElementById('panel-detalle');
                    document.getElementById('panel-vacio').style.display = 'none';
                    panelDetalle.style.display = 'block';
                    requestAnimationFrame(() => panelDetalle.classList.add('is-visible'));
                }
            }

            window.cerrarDetalle = function() {
                const panelDetalle = document.getElementById('panel-detalle');
                panelDetalle.classList.remove('is-visible');
                setTimeout(() => {
                    panelDetalle.style.display = 'none';
                    document.getElementById('panel-vacio').style.display = '';
                }, 260);
            };

            window.cerrarModal = function() {
                document.getElementById('cal-modal-overlay').classList.remove('is-open');
                document.body.style.overflow = '';
            };

            document.getElementById('cal-modal-overlay').addEventListener('click', function(e) {
                if (e.target === this) cerrarModal();
            });

            function actualizarResumen(eventosDelMes) {
                const counts = { confirmada: 0, pendiente: 0, completada: 0, cancelada: 0 };
                eventosDelMes.forEach(ev => {
                    const key = (ev.extendedProps?.estado || '').toLowerCase();
                    if (counts.hasOwnProperty(key)) counts[key]++;
                });
                document.getElementById('count-confirmadas').textContent = counts.confirmada;
                document.getElementById('count-pendientes').textContent  = counts.pendiente;
                document.getElementById('count-completadas').textContent = counts.completada;
                document.getElementById('count-canceladas').textContent  = counts.cancelada;
            }

            function fechaISO(d) {
                return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
            }

            function renderCalendario() {
                const anio = fechaActual.getFullYear();
                const mes  = fechaActual.getMonth();

                document.getElementById('cal-titulo-mes').textContent = MESES[mes] + ' ' + anio;

                const primerDia       = new Date(anio, mes, 1);
                const diasEnMes       = new Date(anio, mes + 1, 0).getDate();
                const diasMesAnterior = new Date(anio, mes, 0).getDate();

                let offset = primerDia.getDay() - 1; // lunes = 0
                if (offset < 0) offset = 6;

                const totalCeldas = Math.ceil((offset + diasEnMes) / 7) * 7;

                const rangoInicio = new Date(anio, mes, 1 - offset);
                const rangoFin    = new Date(anio, mes, diasEnMes + (totalCeldas - offset - diasEnMes));

                fetch(`{{ route('fotografo.calendario.json') }}?start=${fechaISO(rangoInicio)}&end=${fechaISO(rangoFin)}`)
                    .then(r => r.json())
                    .then(data => {
                        const eventosPorDia = {};
                        const eventosDelMesActual = [];

                        data.forEach(ev => {
                            const c = colorForEvent(ev.extendedProps?.estado || ev.estado);
                            const evento = { ...ev, backgroundColor: c.bg, textColor: c.text };
                            const fEv = fechaISO(new Date(ev.start));

                            if (!eventosPorDia[fEv]) eventosPorDia[fEv] = [];
                            eventosPorDia[fEv].push(evento);

                            const d = new Date(ev.start);
                            if (d.getMonth() === mes && d.getFullYear() === anio) {
                                eventosDelMesActual.push(evento);
                            }
                        });

                        actualizarResumen(eventosDelMesActual);
                        pintarGrid(anio, mes, offset, diasEnMes, diasMesAnterior, totalCeldas, eventosPorDia);
                    })
                    .catch(err => console.error('Error cargando calendario:', err));
            }

            function pintarGrid(anio, mes, offset, diasEnMes, diasMesAnterior, totalCeldas, eventosPorDia) {
                const grid = document.getElementById('cal-grid');
                grid.innerHTML = '';

                const hoyISO = fechaISO(new Date());
                const maxVisible = 3;

                for (let i = 0; i < totalCeldas; i++) {
                    let diaNum, esOtroMes = false, fechaCelda;

                    if (i < offset) {
                        diaNum = diasMesAnterior - offset + i + 1;
                        fechaCelda = new Date(anio, mes - 1, diaNum);
                        esOtroMes = true;
                    } else if (i < offset + diasEnMes) {
                        diaNum = i - offset + 1;
                        fechaCelda = new Date(anio, mes, diaNum);
                    } else {
                        diaNum = i - offset - diasEnMes + 1;
                        fechaCelda = new Date(anio, mes + 1, diaNum);
                        esOtroMes = true;
                    }

                    const fISO = fechaISO(fechaCelda);
                    const eventosDia = eventosPorDia[fISO] || [];

                    const celda = document.createElement('div');
                    celda.className = 'cal-day' + (esOtroMes ? ' cal-day--otro-mes' : '') + (fISO === hoyISO ? ' cal-day--hoy' : '');

                    let eventosHTML = '';
                    eventosDia.forEach((ev, idx) => {
                        const oculto = idx >= maxVisible ? ' cal-day__evento--oculto' : '';
                        eventosHTML += `<div class="cal-day__evento${oculto}" data-idx="${idx}" data-fecha="${fISO}" style="background:${ev.backgroundColor};color:${ev.textColor}">${ev.title || 'Reserva'}</div>`;
                    });
                    if (eventosDia.length > maxVisible) {
                        eventosHTML += `<div class="cal-day__mas" data-fecha="${fISO}">+${eventosDia.length - maxVisible} más</div>`;
                    }

                    celda.innerHTML = `<div class="cal-day__numero">${diaNum}</div><div class="cal-day__eventos">${eventosHTML}</div>`;
                    grid.appendChild(celda);
                }

                grid.querySelectorAll('.cal-day__evento').forEach(el => {
                    el.addEventListener('click', function() {
                        const fecha = this.dataset.fecha;
                        const idx   = parseInt(this.dataset.idx, 10);
                        const ev    = (eventosPorDia[fecha] || [])[idx];
                        if (ev) abrirDetalle(ev);
                    });
                });

                grid.querySelectorAll('.cal-day__mas').forEach(el => {
                    el.addEventListener('click', function() {
                        const contenedor = this.previousElementSibling ? this.parentElement : this.parentElement;
                        this.parentElement.querySelectorAll('.cal-day__evento--oculto').forEach(ev => ev.classList.remove('cal-day__evento--oculto'));
                        this.remove();
                    });
                });
            }

            document.getElementById('btn-mes-anterior').addEventListener('click', function() {
                fechaActual.setMonth(fechaActual.getMonth() - 1);
                renderCalendario();
            });

            document.getElementById('btn-mes-siguiente').addEventListener('click', function() {
                fechaActual.setMonth(fechaActual.getMonth() + 1);
                renderCalendario();
            });

            renderCalendario();
        })();
    </script>
@endpush
