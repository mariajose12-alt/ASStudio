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
    $originales    = $fotos->where('estado', 'ORIGINAL')->values();
    $editadas      = $fotos->where('estado', 'EDITADA')->values();
    $hayEditadas   = $editadas->isNotEmpty();
    $hayOriginales = $originales->isNotEmpty();
@endphp

{{-- Pestañas de tipo de foto --}}
@section('tabs')
    @if($hayEditadas)
        <button class="gal-tab gal-tab--active" data-tab="editadas" onclick="cambiarTab('editadas')">
            Editadas
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
                @foreach($editadas as $i => $foto)
                    @php
                        $altos = [300, 400, 500, 350, 450, 380, 420, 320];
                        $alto  = $altos[$i % count($altos)];
                    @endphp
                    <div class="gf-item">
                        <img src="https://picsum.photos/seed/edit{{ $foto->id }}/400/{{ $alto }}"
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
                @foreach($originales as $i => $foto)
                    @php
                        $altos = [450, 320, 400, 500, 350, 420, 300, 480];
                        $alto  = $altos[$i % count($altos)];
                    @endphp
                    <div class="gf-item">
                        <img src="https://picsum.photos/seed/orig{{ $foto->id }}/400/{{ $alto }}"
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

        function descargarZip(tipo) {
            cerrarModalZip();
            if (tipo === 'todas') {
                setTimeout(() => window.location.href = zipRoutes.editadas, 0);
                setTimeout(() => window.location.href = zipRoutes.originales, 1500);
            } else {
                window.location.href = zipRoutes[tipo];
            }
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') document.getElementById('modalZip').classList.remove('open');
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
