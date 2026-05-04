@extends('layouts.fotografo')
@section('title', 'Calendario de Reservas')

@section('content')

    <div class="card">
        <div class="card-header"><h2>Mis Reservas</h2></div>
        <div class="card-body">
            <div id="calendario"></div>
        </div>
    </div>

    {{-- Modal detalle --}}
    <div id="modal-reserva" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:12px; padding:32px; min-width:320px; max-width:480px; width:90%;">
            <h3 id="modal-titulo" style="margin:0 0 16px; font-family:'Playfair Display',serif;"></h3>
            <table style="width:100%; font-size:14px; border-collapse:collapse;">
                <tr><td style="padding:6px 0; color:#888; width:100px;">Cliente</td><td id="modal-cliente" style="font-weight:500;"></td></tr>
                <tr><td style="padding:6px 0; color:#888;">Tipo</td><td id="modal-tipo"></td></tr>
                <tr><td style="padding:6px 0; color:#888;">Lugar</td><td id="modal-lugar"></td></tr>
                <tr><td style="padding:6px 0; color:#888;">Inicio</td><td id="modal-inicio"></td></tr>
                <tr><td style="padding:6px 0; color:#888;">Estado</td><td id="modal-estado"></td></tr>
            </table>
            <button onclick="cerrarModal()" style="margin-top:24px; padding:10px 24px; background:#a07820; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:14px;">
                Cerrar
            </button>
        </div>
    </div>

@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/es.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cal = new FullCalendar.Calendar(document.getElementById('calendario'), {
                locale: 'es',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left:   'prev,next today',
                    center: 'title',
                    right:  'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '{{ route('fotografo.calendario.json') }}',
                eventClick: function(info) {
                    const p = info.event.extendedProps;
                    document.getElementById('modal-titulo').textContent  = info.event.title;
                    document.getElementById('modal-cliente').textContent = p.cliente  || '—';
                    document.getElementById('modal-tipo').textContent    = p.tipo     || '—';
                    document.getElementById('modal-lugar').textContent   = p.lugar    || '—';
                    document.getElementById('modal-estado').textContent  = p.estado   || '—';
                    document.getElementById('modal-inicio').textContent  =
                        info.event.start ? info.event.start.toLocaleString('es-DO') : '—';

                    const modal = document.getElementById('modal-reserva');
                    modal.style.display = 'flex';
                },
                height: 'auto',
            });
            cal.render();
        });

        function cerrarModal() {
            document.getElementById('modal-reserva').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modal-reserva').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });
    </script>
@endpush
