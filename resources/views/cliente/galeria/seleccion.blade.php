@extends('layouts.galeria')

@section('title', $sesion->reserva->paquete->nombre)
@section('topbar_title', $sesion->reserva->cliente->usuario->persona->nombre . ' ' . $sesion->reserva->cliente->usuario->persona->apellido)
@section('topbar_subtitle', $sesion->reserva->catalogo->nombre)

@section('topbar_action')
    <a href="{{ route('cliente.galeria') }}" class="gal-btn-nav" id="btnVolver">⁝</a>
@endsection


@section('tabs')
    {{-- Banner de contador de favoritos --}}
    <div class="fav-banner" id="favBanner">
        <div class="fav-banner__text">
            <strong id="favLabel">Favoritos seleccionados</strong>
            <span id="favSub">Debes elegir exactamente {{ $limite }} fotografías</span>
        </div>
        <div class="fav-banner__counter" id="favCounter">
            <span class="fav-banner__num" id="favCount">0</span>
            <span class="fav-banner__den">/{{ $limite }}</span>
        </div>
    </div>
@endsection

@section('content')

    {{-- CTA principal --}}
    <button class="fav-btn-main" id="btnConfirmar" onclick="manejarBotonPrincipal()" disabled>
        Ver favoritos
    </button>

    {{-- Grid de fotos --}}
    <div class="fotos-grid gf-masonry" id="fotosGrid">
        @foreach($fotos as $foto)
            <div class="foto-item foto-sel gf-item" data-id="{{ $foto->id }}" onclick="toggleFavorito(this)">
                <img src="{{ $foto->url_firmada }}"
                     alt="Foto {{ $foto->id }}"
                     loading="lazy"
                     onload="this.closest('.gf-item').classList.add('gf-item--loaded')">
                <div class="foto-check" id="check-{{ $foto->id }}">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="2,6 5,9 10,3"/>
                    </svg>
                </div>
                <div class="foto-num" id="num-{{ $foto->id }}"></div>
            </div>
        @endforeach
    </div>

    {{-- Modal de confirmación (bottom-sheet en móvil, centrado en desktop) --}}
    <div class="modal-gal" id="modalOverlay" aria-hidden="true">
        <div class="modal-gal__sheet">
            <div class="modal-gal__pill"></div>
            <h3 class="modal-gal__titulo">¡Selección lista!</h3>
            <p class="modal-gal__sub">¿Qué pasa ahora?</p>
            <ol class="modal-gal__pasos">
                <li>Notificamos al <strong>fotógrafo</strong> de tu elección.</li>
                <li>Editará las fotos en los próximos <strong>3–5 días hábiles.</strong></li>
                <li>Te avisaremos por <strong>email</strong> cuando estén listas para revisar.</li>
                <li>Pasarán a tu <strong>galería final</strong> para descargar.</li>
            </ol>
            <form method="POST" action="{{ route('cliente.galeria.confirmar', $sesion->id) }}">
                @csrf
                <input type="hidden" name="fotos_seleccionadas" id="fotosInput">
                <button type="submit" class="modal-gal__btn">Confirmar y enviar</button>
            </form>
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
                <button class="lb-btn lb-btn--fav" id="lbBtnFav" onclick="lbToggleFav()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span id="lbFavLabel">Agregar a favoritos</span>
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const limite      = {{ $limite }};
        const totalFotos  = {{ count($fotos) }};
        let seleccionadas = new Set();
        let orden         = [];
        let modoFavoritos = false;

        /* ── Selección ── */

        function toggleFavorito(el) {
            const id = el.dataset.id;
            if (seleccionadas.has(id)) {
                seleccionadas.delete(id);
                orden = orden.filter(x => x !== id);
                el.classList.remove('seleccionada', 'exceso-sel');
            } else {
                seleccionadas.add(id);
                orden.push(id);
                el.classList.add('seleccionada');
            }
            actualizarNumeracion();
            actualizarBanner();
        }

        function actualizarNumeracion() {
            orden.forEach((id, i) => {
                const num = document.getElementById('num-' + id);
                if (num) num.textContent = i + 1;
            });
        }

        function actualizarBanner() {
            const count  = seleccionadas.size;
            const exceso = count > limite;
            const exacto = count === limite;
            const banner = document.getElementById('favBanner');
            const btn    = document.getElementById('btnConfirmar');
            const label  = document.getElementById('favSub');

            document.getElementById('favCount').textContent = count;

            banner.classList.toggle('exceso', exceso);
            banner.classList.toggle('lleno',  exacto && !exceso);
            document.getElementById('favCounter').classList.toggle('exceso', exceso);

            document.querySelectorAll('.foto-sel.seleccionada').forEach(el =>
                el.classList.toggle('exceso-sel', exceso)
            );

            if (modoFavoritos) {
                btn.classList.remove('lleno');
                btn.classList.toggle('exceso', exceso);
                if (exceso) {
                    btn.textContent   = 'Tienes ' + (count - limite) + ' foto' + (count - limite !== 1 ? 's' : '') + ' de más';
                    btn.disabled     = true;
                    label.textContent = 'Selecciona justamente ' + limite + ' fotos en total para continuar';
                } else if (exacto) {
                    btn.textContent  = 'Confirmar selección';
                    btn.disabled     = false;
                    btn.classList.add('lleno');
                    label.textContent = '¡Perfecto! Revisá tus ' + limite + ' favoritas';
                } else {
                    btn.textContent  = 'Confirmar selección';
                    btn.disabled     = true;
                    label.textContent = 'Faltan ' + (limite - count) + ' fotos para completar';
                }
            } else {
                btn.classList.remove('exceso', 'lleno');
                btn.textContent   = 'Ver favoritos';
                btn.disabled      = count === 0;
                label.textContent = 'Debes elegir exactamente ' + limite + ' fotografías';
            }
        }

        /* ── Modos (todas / favoritos) ── */

        function manejarBotonPrincipal() {
            if (!modoFavoritos) {
                entrarModoFavoritos();
            } else if (seleccionadas.size === limite) {
                abrirModal();
            }
        }

        function entrarModoFavoritos() {
            modoFavoritos = true;
            document.getElementById('fotosGrid').classList.add('modo-favoritos');

            const seccionLabel = document.getElementById('seccionLabel');
            if (seccionLabel) seccionLabel.textContent = 'Tus favoritas · ' + seleccionadas.size;

            const btnVolver = document.getElementById('btnVolver');
            btnVolver.style.display = 'none';

            let btnVerTodas = document.getElementById('btnVerTodas');
            if (!btnVerTodas) {
                btnVerTodas           = document.createElement('button');
                btnVerTodas.id        = 'btnVerTodas';
                btnVerTodas.className = 'gal-btn-nav gal-btn-nav--active';
                btnVerTodas.textContent = '← Ver todas';
                btnVerTodas.onclick   = salirModoFavoritos;
                btnVolver.parentNode.insertBefore(btnVerTodas, btnVolver);
            }
            btnVerTodas.style.display = 'inline-block';
            actualizarBanner();
        }

        function salirModoFavoritos() {
            modoFavoritos = false;
            document.getElementById('fotosGrid').classList.remove('modo-favoritos');

            const seccionLabel = document.getElementById('seccionLabel');
            if (seccionLabel) seccionLabel.textContent = 'Todas las fotos · ' + totalFotos;

            document.getElementById('btnVerTodas').style.display = 'none';
            document.getElementById('btnVolver').style.display   = 'inline-block';
            actualizarBanner();
        }

        /* ── Modal de confirmación ── */

        function abrirModal() {
            if (seleccionadas.size !== limite) return;
            document.getElementById('fotosInput').value = JSON.stringify([...seleccionadas]);
            const overlay = document.getElementById('modalOverlay');
            overlay.classList.add('abierto');
            overlay.setAttribute('aria-hidden', 'false');
        }

        document.getElementById('modalOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('abierto');
                this.setAttribute('aria-hidden', 'true');
            }
        });

        /* ── Lightbox ── */

        let lbFotos = [];
        let lbIndex = 0;

        function lbInicializar() {
            lbFotos = Array.from(document.querySelectorAll('.foto-item')).map(el => ({
                src:    el.querySelector('img')?.src || '',
                fotoId: el.dataset.id,
                itemEl: el,
            }));

            lbFotos.forEach((f, i) => {
                f.itemEl?.querySelector('img')?.addEventListener('click', e => {
                    e.stopPropagation();
                    lbAbrir(i);
                });
            });
        }

        function lbAbrir(index) {
            lbIndex = index;
            document.getElementById('lightbox').classList.add('open');
            document.body.style.overflow = 'hidden';
            lbCargar();
        }

        function lbCargar() {
            const foto    = lbFotos[lbIndex];
            const img     = document.getElementById('lbImg');
            const spinner = document.getElementById('lbSpinner');

            img.style.opacity     = '0';
            spinner.style.display = 'block';
            img.onload = () => { spinner.style.display = 'none'; img.style.opacity = '1'; };
            img.src = foto.src;

            document.getElementById('lbCounter').textContent = `${lbIndex + 1} / ${lbFotos.length}`;

            const esFav = seleccionadas.has(foto.fotoId);
            const btn   = document.getElementById('lbBtnFav');
            btn.classList.toggle('activo', esFav);
            document.getElementById('lbFavLabel').textContent = esFav ? 'Quitar de favoritos' : 'Agregar a favoritos';
        }

        function lbNavegar(dir) {
            lbIndex = (lbIndex + dir + lbFotos.length) % lbFotos.length;
            lbCargar();
        }

        function lbCerrar(e) {
            if (e && e.target !== document.getElementById('lightbox') && !e.target.closest('.lb-close')) return;
            document.getElementById('lightbox').classList.remove('open');
            document.body.style.overflow = '';
        }

        function lbToggleFav() {
            const itemEl = lbFotos[lbIndex].itemEl;
            if (itemEl) toggleFavorito(itemEl);
            lbCargar();
        }

        document.addEventListener('keydown', e => {
            if (!document.getElementById('lightbox').classList.contains('open')) return;
            if (e.key === 'ArrowRight') lbNavegar(1);
            if (e.key === 'ArrowLeft')  lbNavegar(-1);
            if (e.key === 'Escape')     lbCerrar({ target: document.getElementById('lightbox') });
        });

        lbInicializar();
    </script>
@endpush
