@extends('layouts.admin')
@section('title', 'Estudio')

@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet'>
@endpush

@section('content')

    <div class="cal-header">
        <div class="cal-header__left">
            <p class="cal-header__eyebrow">Disponibilidad</p>
            <p class="cal-header__subtitle">Gestiona los períodos en que el estudio está ocupado.</p>
        </div>
        <div class="cal-header__actions">
            <div class="cal-legend">
                <div class="cal-legend__item">
                    <div class="cal-legend__dot" style="background:#e87722;"></div>
                    Ocupado
                </div>
            </div>
            <button class="cal-btn cal-btn--primary" onclick="abrirModal()">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Marcar ocupado
            </button>
        </div>
    </div>

    <div class="cal-page">

        {{-- Calendario --}}
        <div class="cal-main-card">
            <div class="cal-main-card__inner">
                <div id="calendario"></div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="cal-sidebar">

            {{-- Panel detalle bloqueo seleccionado --}}
            <div class="cal-detail-panel" id="detailPanel">
                <div class="cal-detail-panel__accent"></div>
                <div class="cal-detail-panel__body">
                    <div class="cal-detail-panel__header">
                        <p class="cal-detail-panel__eyebrow">Bloqueo seleccionado</p>
                        <h3 class="cal-detail-panel__title" id="detail-motivo">—</h3>
                        <button class="cal-detail-panel__close" onclick="cerrarDetalle()">×</button>
                    </div>
                    <div class="cal-detail-panel__rows">
                        <div class="cal-detail-row">
                            <div class="cal-detail-row__icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="cal-detail-row__label">Inicio</div>
                                <div class="cal-detail-row__value" id="detail-inicio">—</div>
                            </div>
                        </div>
                        <div class="cal-detail-row">
                            <div class="cal-detail-row__icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <div class="cal-detail-row__label">Fin</div>
                                <div class="cal-detail-row__value" id="detail-fin">—</div>
                            </div>
                        </div>
                    </div>
                    <form method="POST" id="form-eliminar" action="" style="margin-top:20px;"
                          onsubmit="return confirm('¿Eliminar este bloqueo?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="cal-btn" style="background:#fef2f2;color:#dc2626;width:100%;justify-content:center;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Eliminar bloqueo
                        </button>
                    </form>
                </div>
            </div>

            {{-- Panel vacío --}}
            <div class="cal-empty-panel" id="emptyPanel">
                <div class="cal-empty-panel__icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="cal-empty-panel__title">Sin selección</p>
                <p class="cal-empty-panel__text">Haz clic en un bloqueo del calendario para ver sus detalles.</p>
            </div>

        </div>
    </div>

    {{-- Modal marcar ocupado --}}
    <div class="cal-modal-overlay" id="modalOverlay">
        <div class="cal-modal">
            <div class="cal-modal__accent"></div>
            <div class="cal-modal__header">
                <div>
                    <p class="cal-modal__eyebrow">Estudio</p>
                    <h3 class="cal-modal__title">Marcar como ocupado</h3>
                </div>
                <button class="cal-modal__close" onclick="cerrarModal()">×</button>
            </div>
            <form method="POST" action="{{ route('admin.estudio.store') }}">
                @csrf
                <div class="cal-modal__body">
                    <div class="cal-modal__grid">
                        <div>
                            <label class="cal-modal__field-label" style="display:block;margin-bottom:6px;">Inicio *</label>
                            <input type="datetime-local" name="inicio" id="input-inicio" required
                                   class="cal-modal__field"
                                   style="width:100%;border:1px solid var(--border);border-radius:10px;padding:10px 14px;font-family:var(--font-sans);font-size:13px;background:var(--snow);color:var(--navy);">
                        </div>
                        <div>
                            <label class="cal-modal__field-label" style="display:block;margin-bottom:6px;">Fin *</label>
                            <input type="datetime-local" name="fin" id="input-fin" required
                                   class="cal-modal__field"
                                   style="width:100%;border:1px solid var(--border);border-radius:10px;padding:10px 14px;font-family:var(--font-sans);font-size:13px;background:var(--snow);color:var(--navy);">
                        </div>
                        <div style="grid-column:1/-1;">
                            <label class="cal-modal__field-label" style="display:block;margin-bottom:6px;">Motivo (opcional)</label>
                            <input type="text" name="motivo" placeholder="Ej: Mantenimiento, reserva externa…"
                                   class="cal-modal__field"
                                   style="width:100%;border:1px solid var(--border);border-radius:10px;padding:10px 14px;font-family:var(--font-sans);font-size:13px;background:var(--snow);color:var(--navy);">
                        </div>
                    </div>
                    @error('inicio')
                    <p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>
                <div class="cal-modal__footer">
                    <button type="button" class="cal-btn cal-btn--ghost" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="cal-btn cal-btn--primary">Guardar bloqueo</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const calendar = new FullCalendar.Calendar(document.getElementById('calendario'), {
                locale: 'es',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left:   'prev,next today',
                    center: 'title',
                    right:  'dayGridMonth,timeGridWeek',
                },
                height: 'auto',
                events: '{{ route('admin.estudio.eventos') }}',
                eventClick: function (info) {
                    const e = info.event;
                    const fmt = dt => new Date(dt).toLocaleString('es-DO', {
                        day: '2-digit', month: 'long', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });

                    document.getElementById('detail-motivo').textContent = e.title;
                    document.getElementById('detail-inicio').textContent  = fmt(e.start);
                    document.getElementById('detail-fin').textContent     = fmt(e.end ?? e.start);

                    const formEliminar = document.getElementById('form-eliminar');
                    formEliminar.action = `/admin/estudio/${e.id}`;

                    document.getElementById('emptyPanel').style.display  = 'none';
                    document.getElementById('detailPanel').classList.add('is-visible');
                },

                // Al hacer clic en un día vacío, pre-rellena el modal con esa fecha
                dateClick: function (info) {
                    const fecha = info.dateStr; // YYYY-MM-DD
                    document.getElementById('input-inicio').value = fecha + 'T09:00';
                    document.getElementById('input-fin').value    = fecha + 'T18:00';
                    abrirModal();
                },
            });

            calendar.render();
        });

        function abrirModal() {
            document.getElementById('modalOverlay').classList.add('is-open');
        }
        function cerrarModal() {
            document.getElementById('modalOverlay').classList.remove('is-open');
        }
        function cerrarDetalle() {
            document.getElementById('detailPanel').classList.remove('is-visible');
            document.getElementById('emptyPanel').style.display = '';
        }

        // Cerrar modal con Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') cerrarModal();
        });

        // Cerrar modal al hacer clic en el overlay
        document.getElementById('modalOverlay').addEventListener('click', function (e) {
            if (e.target === this) cerrarModal();
        });

        // Si hubo error de validación, reabrir el modal automáticamente
        @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => abrirModal());
        @endif
    </script>
@endpush
