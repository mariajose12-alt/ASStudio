@extends('layouts.admin')
@section('title', 'Solicitudes de Estudio')

@push('styles')
    <style>
        .socio-tabpanel { display: none; }
        .socio-tabpanel.active { display: block; }
    </style>
@endpush

@section('content')
    {{-- Pestañas --}}
    <div class="reservas-tabs">
        <button class="tab active" onclick="switchTab('propuestas', this)">
            Solicitudes
        </button>
        <button class="tab" onclick="switchTab('calendario', this)">
            Calendario
        </button>
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
                <button class="cal-btn cal-btn--primary" id="btn-nuevo-bloqueo" style="width:100%; justify-content:center;">
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
                    <div class="cal-detail-panel__accent" style="background:var(--socio-steel,#5b7091);"></div>
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
                        <form id="form-eliminar-bloqueo" method="POST" style="margin-top:16px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cal-btn" style="background:#fef2f2; color:#dc2626; width:100%; justify-content:center;">
                                Eliminar bloqueo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ TAB: PROPUESTAS ═══ --}}
    <div class="socio-tabpanel active" data-tabpanel="propuestas" id="solicitudes">
        @if($solicitudes->isEmpty())
            <div class="card" style="text-align:center; padding:60px; color:var(--muted);">
                No hay solicitudes de estudio registradas.
            </div>
        @endif

        <div style="display:flex; flex-direction:column; gap:16px;">
            @foreach($solicitudes as $solicitud)
                @php
                    $badgeClass = match($solicitud->estado) {
                        'aprobada'  => 'badge-active',
                        'rechazada', 'cancelada' => 'badge-inactive',
                        default     => 'badge-foto',
                    };
                @endphp
                <div class="card" style="border-radius:14px;">
                    <div class="card-body" style="padding:24px 28px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
                            <div>
                                <h3 style="font-family:'Playfair Display',serif; font-size:17px; font-weight:400; margin-bottom:4px; color:var(--socio-ink);">
                                    {{ $solicitud->nombre }} {{ $solicitud->apellido }}
                                </h3>
                                <span style="font-size:12px; color:var(--muted);">
                                    {{ $solicitud->fecha->format('d/m/Y') }} ·
                                    {{ \Carbon\Carbon::parse($solicitud->hora_inicio)->format('H:i') }}–{{ \Carbon\Carbon::parse($solicitud->hora_fin)->format('H:i') }}
                                    · {{ ucfirst(str_replace('_', ' ', $solicitud->finalidad)) }}
                                </span>
                            </div>
                            <span class="badge {{ $badgeClass }}" style="font-size:11px; padding:5px 14px;">
                                {{ ucfirst($solicitud->estado) }}
                            </span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px; padding-top:16px; border-top:1px solid var(--border);">
                            <span style="font-size:13px; color:#555;">{{ $solicitud->cantidad_personas }} persona(s) · {{ $solicitud->email }}</span>
                            <a href="{{ route('socio.estudio.solicitudes.show', $solicitud) }}" class="btn btn-outline btn-sm">Ver detalle</a>
                        </div>
                    </div>
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
            <div class="cal-modal__accent" style="background:var(--socio-navy,#1c2740);"></div>
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
                            <input type="datetime-local" name="inicio" required style="border:none; background:none; width:100%; font-size:13px; font-weight:500; color:var(--socio-ink);">
                        </div>
                        <div class="cal-modal__field cal-modal__field--full">
                            <div class="cal-modal__field-label">Hasta</div>
                            <input type="datetime-local" name="fin" required style="border:none; background:none; width:100%; font-size:13px; font-weight:500; color:var(--socio-ink);">
                        </div>
                        <div class="cal-modal__field cal-modal__field--full">
                            <div class="cal-modal__field-label">Motivo (opcional)</div>
                            <input type="text" name="motivo" placeholder="Ej. mantenimiento" style="border:none; background:none; width:100%; font-size:13px; font-weight:500; color:var(--socio-ink);">
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

@endsection

@push('scripts')
    <script>
        (function() {
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

            function abrirDetalle(ev) {
                const inicio = ev.start ? new Date(ev.start).toLocaleString('es-DO', { dateStyle:'medium', timeStyle:'short' }) : '—';
                const fin    = ev.end   ? new Date(ev.end).toLocaleString('es-DO', { dateStyle:'medium', timeStyle:'short' }) : '—';

                document.getElementById('detail-titulo').textContent = ev.title || 'Ocupado';
                document.getElementById('detail-motivo').textContent = ev.title || '—';
                document.getElementById('detail-inicio').textContent = inicio;
                document.getElementById('detail-fin').textContent    = fin;

                const form = document.getElementById('form-eliminar-bloqueo');
                form.action = `{{ url('/socio/estudio/bloqueos') }}/${ev.id}`;

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

            window.switchTab = function(name, el) {
                document.querySelectorAll('.socio-tabpanel').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelector('[data-tabpanel="' + name + '"]').classList.add('active');
                el.classList.add('active');
            };

            document.getElementById('btn-mes-anterior').addEventListener('click', () => { fechaActual.setMonth(fechaActual.getMonth() - 1); renderCalendario(); });
            document.getElementById('btn-mes-siguiente').addEventListener('click', () => { fechaActual.setMonth(fechaActual.getMonth() + 1); renderCalendario(); });

            renderCalendario();
        })();
    </script>
@endpush
