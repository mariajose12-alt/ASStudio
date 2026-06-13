@extends('layouts.galeria')

@section('title', $sesion->reserva->paquete->nombre)

@section('topbar_title')
    {{ $sesion->reserva->cliente->usuario->persona->nombre }}
    {{ $sesion->reserva->cliente->usuario->persona->apellido }}
@endsection
@section('topbar_subtitle', $sesion->reserva->paquete->nombre)

@section('topbar_action')
    <a href="{{ route('cliente.galeria') }}" class="gal-btn-nav">⁝</a>
@endsection

@php
    $originales    = $fotos->whereIn('estado', ['ORIGINAL', 'PENDIENTE_EDICION'])->values();
    $editadas      = $fotos->where('estado', 'EDITADA')->values();
    $hayEditadas   = $editadas->isNotEmpty();
    $hayOriginales = $originales->isNotEmpty();
@endphp

{{-- Pestañas de tipo de foto --}}
@section('tabs')
    @if($hayEditadas)
        <button class="gal-tab gal-tab--active" data-tab="editadas" onclick="cambiarTab('editadas')">
            Finales
            <span class="gal-tab__count">{{ $editadas->count() }}</span>
        </button>
    @endif
    @if($hayOriginales)
        <button class="gal-tab {{ !$hayEditadas ? 'gal-tab--active' : '' }}"
                data-tab="originales" onclick="cambiarTab('originales')">
            Originales
            <span class="gal-tab__count">{{ $originales->count() }}</span>
        </button>
    @endif
@endsection

@section('content')

    {{-- Cabecera con descarga masiva --}}
    <div class="gf-header">
        <button class="gf-btn-zip" id="btnZip" onclick="abrirModalZip()">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <span>Descargar todas</span>
        </button>
    </div>

    {{-- Panel: fotos editadas --}}
    @if($hayEditadas)
        <div class="gf-panel" id="panel-editadas">
            <div class="gf-masonry">
                @foreach($editadas as $foto)
                    <div class="gf-item">
                        <img src="{{ $foto->url_firmada }}"
                             alt="Foto editada {{ $foto->id }}"
                             loading="lazy"
                             onload="this.closest('.gf-item').classList.add('gf-item--loaded')">
                        <div class="gf-item__overlay">
                            <a href="{{ route('cliente.galeria.descargar', $foto->id) }}"
                               class="gf-item__dl" title="Descargar">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Panel: fotos originales --}}
    @if($hayOriginales)
        <div class="gf-panel" id="panel-originales" @if($hayEditadas) style="display:none" @endif>
            <div class="gf-masonry">
                @foreach($originales as $foto)
                    <div class="gf-item">
                        <img src="{{ $foto->url_firmada }}"
                             alt="Foto original {{ $foto->id }}"
                             loading="lazy"
                             onload="this.closest('.gf-item').classList.add('gf-item--loaded')">
                        <div class="gf-item__overlay">
                            <a href="{{ route('cliente.galeria.descargar', $foto->id) }}"
                               class="gf-item__dl" title="Descargar">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Estado vacío --}}
    @if(!$hayEditadas && !$hayOriginales)
        <div class="gf-empty">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <path d="m21 15-5-5L5 21"/>
            </svg>
            <p>Aún no hay fotos disponibles en esta galería.</p>
        </div>
    @endif

    {{-- Modal de descarga ZIP --}}
    <div class="gf-modal-backdrop" id="modalZip" onclick="cerrarModalZip(event)">
        <div class="gf-modal">
            <div class="gf-modal__header">
                <h3>Descargar fotos</h3>
                <button class="gf-modal__close" onclick="cerrarModalZip()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="gf-modal__desc">Elige qué fotos quieres descargar en ZIP:</p>
            <div class="gf-modal__opciones">
                @if($hayEditadas)
                    <button class="gf-modal__opcion" onclick="descargarZip('editadas')">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                        </svg>
                        <span class="gf-modal__opcion-label">Editadas</span>
                        <span class="gf-modal__opcion-count">{{ $editadas->count() }} fotos</span>
                    </button>
                @endif
                @if($hayOriginales)
                    <button class="gf-modal__opcion" onclick="descargarZip('originales')">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <path d="m21 15-5-5L5 21"/>
                        </svg>
                        <span class="gf-modal__opcion-label">Originales</span>
                        <span class="gf-modal__opcion-count">{{ $originales->count() }} fotos</span>
                    </button>
                @endif
                @if($hayEditadas && $hayOriginales)
                    <button class="gf-modal__opcion gf-modal__opcion--all" onclick="descargarZip('todas')">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span class="gf-modal__opcion-label">Todas</span>
                        <span class="gf-modal__opcion-count">{{ $fotos->count() }} fotos</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de confirmación: ZIP en camino --}}
    <div class="gf-modal-backdrop" id="modalZipConfirm" onclick="cerrarModalConfirm(event)">
        <div class="gf-modal gf-modal--confirm">
            <div class="gf-modal__header">
                <h3>¡Ya estamos preparando tu ZIP!</h3>
                <button class="gf-modal__close" onclick="cerrarModalConfirm()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="gf-modal__confirm-body">
                {{-- Ícono animado --}}
                <div class="gf-modal__confirm-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>

                <p class="gf-modal__confirm-text">
                    Estamos comprimiendo tus fotos
                    <strong id="confirmTipoLabel"></strong>.
                    Cuando el archivo esté listo, te enviaremos el enlace de descarga a:
                </p>

                <p class="gf-modal__confirm-email">
                    {{ Auth::user()->email }}
                </p>

                <p class="gf-modal__confirm-hint">
                    El enlace estará disponible por <strong>24 horas</strong>. Revisa también tu carpeta de spam si no ves el correo en unos minutos.
                </p>
            </div>

            <button class="gf-modal__confirm-btn" onclick="cerrarModalConfirm()">
                Entendido
            </button>
        </div>
    </div>

    {{-- Lightbox --}}
    <div class="lb-backdrop" id="lightbox" onclick="lbCerrar(event)">
        <button class="lb-close" onclick="lbCerrar()">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
        <button class="lb-nav lb-nav--prev" onclick="lbNavegar(-1)">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
        <div class="lb-img-wrap">
            <img id="lbImg" src="" alt="">
            <div class="lb-spinner" id="lbSpinner"></div>
        </div>
        <button class="lb-nav lb-nav--next" onclick="lbNavegar(1)">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </button>
        <div class="lb-toolbar">
            <span class="lb-counter" id="lbCounter"></span>
            <div class="lb-actions">
                <a class="lb-btn lb-btn--dl" id="lbBtnDl" href="#">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar
                </a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <style>
        .gf-modal--confirm {
            max-width: 420px;
            text-align: center;
        }
        .gf-modal--confirm .gf-modal__header {
            justify-content: space-between;
        }
        .gf-modal--confirm .gf-modal__header h3 {
            font-size: 1rem;
        }
        .gf-modal__confirm-body {
            padding: 8px 0 20px;
        }
        .gf-modal__confirm-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background-color: color-mix(in srgb, var(--orange, #e87722) 12%, transparent);
            color: var(--orange, #e87722);
            margin: 0 auto 20px;
            animation: confirmPulse 1.8s ease-in-out infinite;
        }
        @keyframes confirmPulse {
            0%, 100% { transform: scale(1);   opacity: 1;    }
            50%       { transform: scale(1.06); opacity: 0.8; }
        }
        .gf-modal__confirm-text {
            font-size: 0.875rem;
            color: var(--text-secondary, #6b6b6b);
            line-height: 1.6;
            margin: 0 0 12px;
        }
        .gf-modal__confirm-email {
            display: inline-block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--orange, #e87722);
            background: color-mix(in srgb, var(--orange, #e87722) 8%, transparent);
            border: 1px solid color-mix(in srgb, var(--orange, #e87722) 25%, transparent);
            border-radius: 8px;
            padding: 6px 14px;
            margin: 0 0 16px;
            word-break: break-all;
        }
        .gf-modal__confirm-hint {
            font-size: 0.78rem;
            color: var(--text-secondary, #9ca3af);
            line-height: 1.6;
            margin: 0;
        }
        .gf-modal__confirm-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--orange, #e87722);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.15s;
        }
        .gf-modal__confirm-btn:hover { opacity: 0.88; }
    </style>
    <script>
        const zipRoutes = {
            editadas:   '{{ route("cliente.galeria.zip", ["id" => $sesion->id, "tipo" => "editadas"]) }}',
            originales: '{{ route("cliente.galeria.zip", ["id" => $sesion->id, "tipo" => "originales"]) }}',
        };

        let tabActual = '{{ $hayEditadas ? "editadas" : "originales" }}';

        /* ── Pestañas ── */

        function cambiarTab(tab) {
            if (tab === tabActual) return;
            tabActual = tab;
            document.querySelectorAll('.gal-tab').forEach(btn =>
                btn.classList.toggle('gal-tab--active', btn.dataset.tab === tab)
            );
            document.querySelectorAll('.gf-panel').forEach(panel =>
                panel.style.display = panel.id === 'panel-' + tab ? '' : 'none'
            );
        }

        /* ── Modal ZIP ── */

        function abrirModalZip() {
            document.getElementById('modalZip').classList.add('open');
        }

        function cerrarModalZip(e) {
            if (!e || e.target === document.getElementById('modalZip')) {
                document.getElementById('modalZip').classList.remove('open');
            }
        }

        async function descargarZip(tipo) {
            cerrarModalZip();

            const tiposLabel = { editadas: 'editadas', originales: 'originales', todas: 'editadas y originales' };
            document.getElementById('confirmTipoLabel').textContent = tiposLabel[tipo] ?? tipo;

            // Mostrar modal inmediatamente, sin esperar al backend
            document.getElementById('modalZipConfirm').classList.add('open');

            // Disparar el fetch en segundo plano (fire and forget)
            if (tipo === 'todas') {
                fetch(zipRoutes.editadas);
                fetch(zipRoutes.originales);
            } else {
                fetch(zipRoutes[tipo]);
            }
        }

        function cerrarModalConfirm(e) {
            if (!e || e.target === document.getElementById('modalZipConfirm')) {
                document.getElementById('modalZipConfirm').classList.remove('open');
            }
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.getElementById('modalZip').classList.remove('open');
                document.getElementById('modalZipConfirm').classList.remove('open');
            }
        });

        /* ── Lightbox ── */

        let lbFotos = [];
        let lbIndex = 0;

        // Delegación: captura clicks en items del panel visible
        document.addEventListener('click', e => {
            const item = e.target.closest('.gf-item');
            if (!item || e.target.closest('.gf-item__dl')) return;

            const panel = Array.from(document.querySelectorAll('.gf-panel'))
                .find(p => p.style.display !== 'none') ?? document.querySelector('.gf-panel');
            if (!panel) return;

            lbFotos = Array.from(panel.querySelectorAll('.gf-item')).map(el => ({
                src:    el.querySelector('img')?.src || '',
                dlHref: el.querySelector('a.gf-item__dl')?.href || null,
                itemEl: el,
            }));

            lbIndex = lbFotos.findIndex(f => f.itemEl === item);
            if (lbIndex === -1) lbIndex = 0;

            document.getElementById('lightbox').classList.add('open');
            document.body.style.overflow = 'hidden';
            lbCargar();
        });

        function lbCargar() {
            const foto    = lbFotos[lbIndex];
            const img     = document.getElementById('lbImg');
            const spinner = document.getElementById('lbSpinner');

            img.style.opacity     = '0';
            spinner.style.display = 'block';
            img.onload = () => { spinner.style.display = 'none'; img.style.opacity = '1'; };
            img.src = foto.src;

            document.getElementById('lbCounter').textContent = `${lbIndex + 1} / ${lbFotos.length}`;
            document.getElementById('lbBtnDl').href          = foto.dlHref || '#';
        }

        function lbNavegar(dir) {
            lbIndex = (lbIndex + dir + lbFotos.length) % lbFotos.length;
            lbCargar();
        }

        function lbCerrar(e) {
            if (e && !e.target.closest('.lb-close') && e.target.id !== 'lightbox') return;
            document.getElementById('lightbox').classList.remove('open');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => {
            const lb = document.getElementById('lightbox');
            if (!lb.classList.contains('open')) return;
            if (e.key === 'ArrowRight') lbNavegar(1);
            if (e.key === 'ArrowLeft')  lbNavegar(-1);
            if (e.key === 'Escape') { lb.classList.remove('open'); document.body.style.overflow = ''; }
        });
    </script>
@endpush
