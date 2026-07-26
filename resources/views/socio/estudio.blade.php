@extends('layouts.socio')
@section('title', 'Estudio')

@push('styles')
    <style>
        /* ── Sub-nav de pestañas, mismo lenguaje visual que .socio-header ── */
        .estudio-subnav {
            display: flex;
            gap: 2px;
            margin-bottom: 20px;
            padding: 4px;
            background: var(--socio-navy, #1c2740);
            border-radius: 10px;
            overflow-x: auto;
        }

        .estudio-subnav__tab {
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 11px 16px;
            background: none;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.62);
            white-space: nowrap;
        }

        .estudio-subnav__tab:hover {
            color: #fff;
        }

        .estudio-subnav__tab.is-active {
            color: #fff;
        }

        .estudio-subnav__tab.is-active::after {
            content: '';
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 4px;
            height: 2px;
            border-radius: 1px;
            background: var(--socio-steel, #5b7091);
        }

        .estudio-subnav__count {
            background: var(--socio-steel, #5b7091);
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 999px;
            line-height: 1.4;
        }

        .socio-tabpanel { display: none; }
        .socio-tabpanel.is-active { display: block; }

        /* ── Recoloreado de acentos: naranja → gris-navy ── */
        .socio-main .cal-btn--primary {
            background: var(--socio-navy, #1c2740);
            border-color: var(--socio-navy, #1c2740);
            color: #fff;
        }

        .socio-main .cal-btn--primary:hover {
            background: var(--socio-navy-2, #232f4d);
        }

        .socio-main .btn-outline {
            border-color: var(--socio-steel, #5b7091);
            color: var(--socio-steel, #5b7091);
        }

        .socio-main .btn-outline:hover {
            background: var(--socio-steel, #5b7091);
            color: #fff;
        }

        .socio-main .badge-foto {
            background: #e9edf2;
            color: var(--socio-ink, #14181f);
        }

        @media (max-width: 700px) {
            .estudio-subnav { margin: -4px -4px 20px; }
        }

        .socio-accion summary { list-style:none; cursor:pointer; }
        .socio-accion summary::-webkit-details-marker { display:none; }
    </style>
@endpush

@section('content')

    {{-- ═══ TAB: RESUMEN ═══ --}}
    <div class="socio-tabpanel is-active" data-tabpanel="resumen" id="dashboard">

        <div class="kpi-grid" style="margin-bottom:24px;">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <span class="kpi-delta {{ $deltaSolicitudes >= 0 ? 'up' : 'down' }}">
                        {{ $deltaSolicitudes >= 0 ? '↑' : '↓' }} {{ abs($deltaSolicitudes) }}%
                    </span>
                </div>
                <div class="kpi-value">{{ $totalMes }}</div>
                <div class="kpi-label">Solicitudes este mes</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                </div>
                <div class="kpi-value">{{ $pendientesCount }}</div>
                <div class="kpi-label">Pendientes de revisión</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                </div>
                <div class="kpi-value">{{ $tasaAprobacion !== null ? $tasaAprobacion . '%' : '—' }}</div>
                <div class="kpi-label">Tasa de aprobación</div>
                <div class="kpi-compare">{{ $aprobadasMes }} aprobadas · {{ $rechazadasMes }} rechazadas</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/>
                        </svg>
                    </div>
                </div>
                <div class="kpi-value">{{ rtrim(rtrim(number_format($horasConfirmadas, 1), '0'), '.') }}h</div>
                <div class="kpi-label">Horas confirmadas este mes</div>
            </div>
        </div>

        <div class="socio-dashboard-grid">
            <div class="card">
                <div class="card-header"><h2>Próximas reservas confirmadas</h2></div>
                <div class="card-body">
                    @if($proximasReservas->isEmpty())
                        <p class="socio-empty-note">No hay reservas aprobadas próximamente.</p>
                    @else
                        @foreach($proximasReservas as $r)
                            <div class="reserva-row">
                                <div>
                                    <div class="reserva-row__name">{{ $r->nombre }} {{ $r->apellido }}</div>
                                    <div class="reserva-row__finalidad">{{ ucfirst(str_replace('_', ' ', $r->finalidad)) }}</div>
                                </div>
                                <div class="reserva-row__date">
                                    {{ $r->fecha->format('d/m/Y') }}
                                    <small>{{ \Carbon\Carbon::parse($r->hora_inicio)->format('H:i') }}</small>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>Solicitudes por finalidad (este mes)</h2></div>
                <div class="card-body">
                    @if($porFinalidad->isEmpty())
                        <p class="socio-empty-note">Sin datos este mes.</p>
                    @else
                        @foreach($porFinalidad as $finalidad => $total)
                            <div class="finalidad-row">
                                <div class="finalidad-row__head">
                                    <span class="finalidad-row__label">{{ ucfirst(str_replace('_', ' ', $finalidad)) }}</span>
                                    <span class="finalidad-row__count">{{ $total }}</span>
                                </div>
                                <div class="finalidad-bar">
                                    <div class="finalidad-bar__fill" style="width:{{ ($total / $maxFinalidad) * 100 }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card card--span-full">
                <div class="card-header"><h2>Insights para tu negocio</h2></div>
                <div class="card-body insights-body">

                    @if($diaMasSolicitado)
                        <div class="insight-item">
                            <span class="insight-item__dot"></span>
                            <div class="insight-item__text">
                                <strong>{{ $diaMasSolicitado }}</strong> es tu día más solicitado
                                ({{ $diaMasSolicitadoPct }}% de las reservas este mes).
                            </div>
                        </div>
                    @endif

                    @if($franjaMasSolicitada)
                        <div class="insight-item">
                            <span class="insight-item__dot"></span>
                            <div class="insight-item__text">
                                El <strong>{{ $franjaMasSolicitada }}</strong> concentra el {{ $franjaMasSolicitadaPct }}% de las solicitudes.
                            </div>
                        </div>
                    @endif

                    @if($pctClientesRecurrentes !== null)
                        <div class="insight-item">
                            <span class="insight-item__dot"></span>
                            <div class="insight-item__text">
                                <strong>{{ $pctClientesRecurrentes }}%</strong> de tus clientes este mes ya habían reservado antes.
                            </div>
                        </div>
                    @endif

                    @if($anticipacionPromedio !== null)
                        <div class="insight-item">
                            <span class="insight-item__dot"></span>
                            <div class="insight-item__text">
                                En promedio, reservan con <strong>{{ $anticipacionPromedio }} días</strong> de anticipación.
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- ═══ TAB: CALENDARIO ═══ --}}
    <div class="socio-tabpanel" data-tabpanel="calendario" id="calendario">
        <div class="cal-page">
            <div>
                <div class="cal-main-card">
                    <div class="cal-main-card__inner">
                        <div class="cal-toolbar">
                            <button class="cal-nav-btn" id="btn-mes-anterior" aria-label="Mes anterior">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </button>
                            <div class="cal-toolbar__title" id="cal-titulo-mes"></div>
                            <button class="cal-nav-btn" id="btn-mes-siguiente" aria-label="Mes siguiente">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </div>
                        <div class="cal-weekdays">
                            <div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div><div>Dom</div>
                        </div>
                        <div class="cal-grid" id="cal-grid"></div>
                    </div>
                </div>
            </div>

            <div class="cal-sidebar">
                <button class="cal-btn cal-btn--primary cal-btn--block" id="btn-nuevo-bloqueo">
                    + Bloquear estudio
                </button>

                <div class="cal-empty-panel" id="panel-vacio">
                    <div class="cal-empty-panel__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>
                    <p class="cal-empty-panel__title">Selecciona un bloqueo</p>
                    <p class="cal-empty-panel__text">Haz clic en cualquier evento del calendario para ver sus detalles aquí, o crea uno nuevo.</p>
                </div>

                <div class="cal-detail-panel" id="panel-detalle">
                    <div class="cal-detail-panel__accent"></div>
                    <div class="cal-detail-panel__body">
                        <div class="cal-detail-panel__header">
                            <div class="cal-detail-panel__eyebrow">Estudio ocupado</div>
                            <div class="cal-detail-panel__title" id="detail-titulo"></div>
                            <button class="cal-detail-panel__close" onclick="cerrarDetalle()" title="Cerrar">×</button>
                        </div>
                        <div class="cal-detail-panel__rows">
                            <div class="cal-detail-row">
                                <div>
                                    <div class="cal-detail-row__label">Motivo</div>
                                    <div class="cal-detail-row__value" id="detail-motivo"></div>
                                </div>
                            </div>
                            <div class="cal-detail-row">
                                <div>
                                    <div class="cal-detail-row__label">Desde</div>
                                    <div class="cal-detail-row__value" id="detail-inicio"></div>
                                </div>
                            </div>
                            <div class="cal-detail-row">
                                <div>
                                    <div class="cal-detail-row__label">Hasta</div>
                                    <div class="cal-detail-row__value" id="detail-fin"></div>
                                </div>
                            </div>
                        </div>
                        <form id="form-eliminar-bloqueo" method="POST" class="form-mt">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cal-btn cal-btn--danger-soft cal-btn--block">
                                Eliminar bloqueo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ TAB: PROPUESTAS ═══ --}}
    <div class="socio-tabpanel" data-tabpanel="propuestas" id="solicitudes">
        @if($solicitudes->isEmpty())
            <div class="card" style="text-align:center; padding:60px; color:var(--muted);">
                No hay solicitudes de estudio registradas.
            </div>
        @endif

        <div class="solicitud-list">
            @foreach($solicitudes as $solicitud)
                <div class="solicitud-card">
                    <div class="solicitud-card__main">
                        <div>
                            <div class="solicitud-card__name">{{ $solicitud->nombre }} {{ $solicitud->apellido }}</div>
                            <div class="solicitud-card__meta">
                                <span>{{ $solicitud->fecha->format('d/m/Y') }}</span>
                                <span>{{ \Carbon\Carbon::parse($solicitud->hora_inicio)->format('H:i') }}–{{ \Carbon\Carbon::parse($solicitud->hora_fin)->format('H:i') }}</span>
                                <span>{{ ucfirst(str_replace('_', ' ', $solicitud->finalidad)) }}</span>
                            </div>
                        </div>
                        <span class="solicitud-badge solicitud-badge--{{ $solicitud->estado }}">{{ ucfirst($solicitud->estado) }}</span>
                    </div>

                    @if($solicitud->estado === 'pendiente')
                        <div class="solicitud-actions">
                            <button type="button" class="cal-btn cal-btn--confirm-aprobar" onclick="abrirModalDecision({{ $solicitud->id }}, 'aprobar')">Aprobar</button>
                            <button type="button" class="cal-btn cal-btn--confirm-rechazar" onclick="abrirModalDecision({{ $solicitud->id }}, 'rechazar')">Rechazar</button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div style="padding:20px 0;">
            {{ $solicitudes->links() }}
        </div>
    </div>

    {{-- ═══ MODAL: nuevo bloqueo ═══ --}}
    <div id="modal-bloqueo-overlay" class="cal-modal-overlay" role="dialog" aria-modal="true">
        <div class="cal-modal">
            <div class="cal-modal__accent"></div>
            <div class="cal-modal__header">
                <div>
                    <div class="cal-modal__eyebrow">Nuevo bloqueo</div>
                    <div class="cal-modal__title">Marcar estudio como ocupado</div>
                </div>
                <button class="cal-modal__close" onclick="cerrarModalBloqueo()" aria-label="Cerrar">×</button>
            </div>
            <form method="POST" action="{{ route('socio.estudio.bloqueos.store') }}">
                @csrf
                <div class="cal-modal__body">
                    @if($errors->any())
                        <div class="alert-error" style="margin-bottom:14px;">{{ $errors->first() }}</div>
                    @endif
                    <div class="cal-modal__grid">
                        <div class="cal-modal__field cal-modal__field--full">
                            <div class="cal-modal__field-label">Desde</div>
                            <input type="datetime-local" name="inicio" required class="cal-modal__input">
                        </div>
                        <div class="cal-modal__field cal-modal__field--full">
                            <div class="cal-modal__field-label">Hasta</div>
                            <input type="datetime-local" name="fin" required class="cal-modal__input">
                        </div>
                        <div class="cal-modal__field cal-modal__field--full">
                            <div class="cal-modal__field-label">Motivo (opcional)</div>
                            <input type="text" name="motivo" placeholder="Ej. mantenimiento" class="cal-modal__input">
                        </div>
                    </div>
                </div>
                <div class="cal-modal__footer">
                    <button type="button" class="cal-btn cal-btn--ghost" onclick="cerrarModalBloqueo()">Cancelar</button>
                    <button type="submit" class="cal-btn cal-btn--primary">Guardar bloqueo</button>
                </div>
            </form>
        </div>
    </div>

    {{-- un solo modal reutilizable para ambas acciones, junto al modal de bloqueo --}}
    <div id="modal-decision-overlay" class="cal-modal-overlay" role="dialog" aria-modal="true">
        <div class="cal-modal">
            <div class="cal-modal__accent" id="modal-decision-accent"></div>
            <div class="cal-modal__header">
                <div>
                    <div class="cal-modal__eyebrow" id="modal-decision-eyebrow">Solicitud</div>
                    <div class="cal-modal__title" id="modal-decision-title"></div>
                </div>
                <button class="cal-modal__close" onclick="cerrarModalDecision()" aria-label="Cerrar">×</button>
            </div>
            <form method="POST" id="form-decision">
                @csrf
                <input type="hidden" name="volver_tab" value="propuestas">
                <div class="cal-modal__body">
                    <textarea name="nota" rows="3" id="modal-decision-nota" class="solicitud-textarea" style="width:100%;"></textarea>
                </div>
                <div class="cal-modal__footer">
                    <button type="button" class="cal-btn cal-btn--ghost" onclick="cerrarModalDecision()">Cancelar</button>
                    <button type="submit" class="cal-btn" id="modal-decision-submit"></button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        (function() {
            // ── Tabs (ahora en .estudio-subnav) ──
            document.querySelectorAll('.estudio-subnav__tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    document.querySelectorAll('.estudio-subnav__tab').forEach(t => t.classList.remove('is-active'));
                    document.querySelectorAll('.socio-tabpanel').forEach(p => p.classList.remove('is-active'));
                    tab.classList.add('is-active');
                    document.querySelector(`[data-tabpanel="${tab.dataset.tab}"]`).classList.add('is-active');
                });
            });

            // ── Bloquear la acción contraria mientras una está abierta ──
            document.querySelectorAll('.solicitud-actions').forEach(group => {
                const acciones = group.querySelectorAll('.socio-accion');
                acciones.forEach(det => {
                    det.addEventListener('toggle', () => {
                        acciones.forEach(otro => {
                            if (otro === det) return;
                            if (det.open) {
                                otro.setAttribute('data-locked', 'true');
                            } else {
                                otro.removeAttribute('data-locked');
                            }
                        });
                    });
                });
            });

            // ── Modal nuevo bloqueo ──
            document.getElementById('btn-nuevo-bloqueo').addEventListener('click', () => {
                document.getElementById('modal-bloqueo-overlay').classList.add('is-open');
            });
            window.cerrarModalBloqueo = function() {
                document.getElementById('modal-bloqueo-overlay').classList.remove('is-open');
            };
            document.getElementById('modal-bloqueo-overlay').addEventListener('click', function(e) {
                if (e.target === this) cerrarModalBloqueo();
            });
            @if($errors->any())
            document.getElementById('modal-bloqueo-overlay').classList.add('is-open');
            @endif

            // ── Calendario (adaptado del de fotógrafo, sin FullCalendar) ──
            const MESES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
            let fechaActual = new Date();
            fechaActual.setDate(1);

            window.abrirModalDecision = function(id, accion) {
                const form = document.getElementById('form-decision');
                const esAprobar = accion === 'aprobar';

                form.action = esAprobar
                    ? `{{ url('/socio/estudio/solicitudes') }}/${id}/aprobar`
                    : `{{ url('/socio/estudio/solicitudes') }}/${id}/rechazar`;

                document.getElementById('modal-decision-title').textContent = esAprobar ? 'Aprobar solicitud' : 'Rechazar solicitud';
                document.getElementById('modal-decision-nota').placeholder = esAprobar ? 'Nota (opcional)' : 'Motivo del rechazo';
                document.getElementById('modal-decision-submit').textContent = esAprobar ? 'Confirmar aprobación' : 'Confirmar rechazo';
                document.getElementById('modal-decision-submit').className = 'cal-btn ' + (esAprobar ? 'cal-btn--confirm-aprobar' : 'cal-btn--confirm-rechazar');

                document.getElementById('modal-decision-overlay').classList.add('is-open');
            };
            window.cerrarModalDecision = function() {
                document.getElementById('modal-decision-overlay').classList.remove('is-open');
            };
            document.getElementById('modal-decision-overlay').addEventListener('click', function(e) {
                if (e.target === this) cerrarModalDecision();
            });

            function abrirDetalle(ev) {
                const inicio = ev.start ? new Date(ev.start).toLocaleString('es-DO', { dateStyle:'medium', timeStyle:'short' }) : '—';
                const fin    = ev.end   ? new Date(ev.end).toLocaleString('es-DO', { dateStyle:'medium', timeStyle:'short' }) : '—';

                document.getElementById('detail-titulo').textContent = ev.title || 'Ocupado';
                document.getElementById('detail-motivo').textContent = ev.title || '—';
                document.getElementById('detail-inicio').textContent = inicio;
                document.getElementById('detail-fin').textContent    = fin;

                const formEliminar = document.getElementById('form-eliminar-bloqueo');
                if (ev.tipo === 'bloqueo') {
                    formEliminar.style.display = '';
                    formEliminar.action = `{{ url('/socio/estudio/bloqueos') }}/${ev.id.replace('bloqueo-', '')}`;
                } else {
                    formEliminar.style.display = 'none';
                }

                const panelDetalle = document.getElementById('panel-detalle');
                document.getElementById('panel-vacio').style.display = 'none';
                panelDetalle.style.display = 'block';
                requestAnimationFrame(() => panelDetalle.classList.add('is-visible'));
            }

            window.cerrarDetalle = function() {
                const panelDetalle = document.getElementById('panel-detalle');
                panelDetalle.classList.remove('is-visible');
                setTimeout(() => {
                    panelDetalle.style.display = 'none';
                    document.getElementById('panel-vacio').style.display = '';
                }, 260);
            };

            function fechaISO(d) {
                return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
            }

            // Restaurar pestaña activa tras un aprobar/rechazar inline
            const tabParam = new URLSearchParams(window.location.search).get('tab');
            if (tabParam) {
                const btn = document.querySelector(`.socio-tab[data-tab="${tabParam}"]`);
                if (btn) btn.click();
            }

            function renderCalendario() {
                const anio = fechaActual.getFullYear();
                const mes  = fechaActual.getMonth();
                document.getElementById('cal-titulo-mes').textContent = MESES[mes] + ' ' + anio;

                const primerDia = new Date(anio, mes, 1);
                const diasEnMes = new Date(anio, mes + 1, 0).getDate();
                const diasMesAnterior = new Date(anio, mes, 0).getDate();

                let offset = primerDia.getDay() - 1;
                if (offset < 0) offset = 6;

                const totalCeldas = Math.ceil((offset + diasEnMes) / 7) * 7;

                fetch(`{{ route('socio.estudio.eventos') }}`)
                    .then(r => r.json())
                    .then(data => {
                        const eventosPorDia = {};
                        data.forEach(ev => {
                            const fEv = fechaISO(new Date(ev.start));
                            if (!eventosPorDia[fEv]) eventosPorDia[fEv] = [];
                            eventosPorDia[fEv].push(ev);
                        });
                        pintarGrid(anio, mes, offset, diasEnMes, diasMesAnterior, totalCeldas, eventosPorDia);
                    })
                    .catch(err => console.error('Error cargando calendario del estudio:', err));
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
                        eventosHTML += `<div class="cal-day__evento${oculto}" data-idx="${idx}" data-fecha="${fISO}" style="background:${ev.color || '#5b7091'};color:#fff">${ev.title || 'Ocupado'}</div>`;
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
                        this.parentElement.querySelectorAll('.cal-day__evento--oculto').forEach(ev => ev.classList.remove('cal-day__evento--oculto'));
                        this.remove();
                    });
                });
            }

            document.getElementById('btn-mes-anterior').addEventListener('click', () => { fechaActual.setMonth(fechaActual.getMonth() - 1); renderCalendario(); });
            document.getElementById('btn-mes-siguiente').addEventListener('click', () => { fechaActual.setMonth(fechaActual.getMonth() + 1); renderCalendario(); });

            renderCalendario();
        })();
    </script>
@endpush
