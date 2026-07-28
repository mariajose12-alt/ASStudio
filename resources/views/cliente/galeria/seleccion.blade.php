@extends('layouts.galeria')

@section('title', $sesion->reserva->paquete->nombre)
@section('topbar_title', $sesion->reserva->cliente->usuario->persona->nombre . ' ' . $sesion->reserva->cliente->usuario->persona->apellido)
@section('topbar_subtitle', $sesion->reserva->catalogo->nombre)

@section('topbar_action')
    <a href="{{ route('cliente.galeria') }}" class="gal-btn-nav" id="btnVolver">⁝</a>
@endsection

@push('styles')
    <style>
        /* ── Modal exceso: lista de fotos ── */
        .modal-exceso__lista {
            list-style: none;
            margin: 0.75rem 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-height: 260px;
            overflow-y: auto;
        }
        .modal-exceso__item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 0.4rem 0.5rem;
        }
        .modal-exceso__thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }
        .modal-exceso__num {
            font-size: 0.78rem;
            opacity: 0.55;
            flex: 1;
        }
        .modal-exceso__quitar {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: 0.6;
            padding: 0.25rem;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.15s;
        }
        .modal-exceso__quitar:hover { opacity: 1; }

        /* ── Botón secundario en modal ── */
        .modal-gal__btn--secondary {
            background: transparent;
            border: 1px solid currentColor;
            opacity: 0.7;
        }
        .modal-gal__btn--secondary:hover { opacity: 1; }
    </style>
@endpush


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
                <li>El <strong>segundo pago</strong> ya está disponible y deberá realizarse para desbloquear el acceso a las fotos editadas.</li>
                <li>Te avisaremos por <strong>email</strong> cuando las fotos estén listas para revisar.</li>
                <li>Una vez confirmado el pago, pasarán a tu <strong>galería final</strong> para descargarlas.</li>
            </ol>
            <form method="POST" action="{{ route('cliente.galeria.confirmar', $sesion->id) }}">
                @csrf
                <input type="hidden" name="fotos_seleccionadas" id="fotosInput">
                <button type="submit" class="modal-gal__btn">Confirmar y enviar</button>
            </form>
        </div>
    </div>

    {{-- Modal de bienvenida (primera vez) --}}
    <div class="modal-gal" id="modalBienvenida" aria-hidden="true">
        <div class="modal-gal__sheet">
            <div class="modal-gal__pill"></div>
            <h3 class="modal-gal__titulo">¡Bienvenida a tu galería!</h3>
            <p class="modal-gal__sub">Cómo funciona la selección</p>
            <ol class="modal-gal__pasos">
                <li>Explora todas tus fotos y toca las que más te gusten para marcarlas como <strong>favoritas</strong>.</li>
                <li>Tu paquete incluye <strong>{{ $limite }} {{ $limite === 1 ? 'fotografía' : 'fotografías' }}</strong>. Debes elegir exactamente ese número.</li>
                <li>Cuando tengas tus favoritas listas, presiona <strong>"Ver favoritos"</strong> para revisar tu selección y confirmarla.</li>
            </ol>
            <button type="button" class="modal-gal__btn" onclick="cerrarModalBienvenida()">
                ¡Entendido, empezar!
            </button>
        </div>
    </div>

    {{-- Modal de exceso --}}
    <div class="modal-gal" id="modalExceso" aria-hidden="true">
        <div class="modal-gal__sheet">
            <div class="modal-gal__pill"></div>
            <h3 class="modal-gal__titulo">Tienes fotos de más</h3>
            <p class="modal-gal__sub" id="modalExcesoSub"></p>
            <ul class="modal-exceso__lista" id="modalExcesoLista"></ul>
            <div class="modal-gal__pasos" style="margin-top:0.5rem">
                <p style="margin:0;font-size:0.85rem;opacity:0.7">Toca el <strong>×</strong> para quitar una foto de la selección.</p>
            </div>
            <button type="button" class="modal-gal__btn modal-gal__btn--secondary" onclick="cerrarModalExceso()">
                Seguir editando
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
    @include('partials.zoom')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        });

        const limite      = {{ $limite }};
        const totalFotos  = {{ count($fotos) }};
        const sesionId    = {{ $sesion->id }};
        let seleccionadas = new Set();
        let orden         = [];
        let modoFavoritos = false;

        /* ── Modal de bienvenida ── */

        function mostrarModalBienvenidaSiPrimera() {
            const clave = 'galeria_vista_' + sesionId;
            if (!localStorage.getItem(clave)) {
                const overlay = document.getElementById('modalBienvenida');
                overlay.classList.add('abierto');
                overlay.setAttribute('aria-hidden', 'false');
            }
        }

        function cerrarModalBienvenida() {
            const clave = 'galeria_vista_' + sesionId;
            localStorage.setItem(clave, '1');
            const overlay = document.getElementById('modalBienvenida');
            overlay.classList.remove('abierto');
            overlay.setAttribute('aria-hidden', 'true');
        }

        /* ── Modal de exceso ── */

        function abrirModalExceso() {
            const excedente = seleccionadas.size - limite;
            document.getElementById('modalExcesoSub').textContent =
                'Seleccionaste ' + excedente + ' foto' + (excedente !== 1 ? 's' : '') +
                ' de más. Quita las que no quieras conservar.';

            const lista = document.getElementById('modalExcesoLista');
            lista.innerHTML = '';

            // Mostrar las fotos seleccionadas (las últimas en orden = las "de más" primero)
            [...orden].reverse().forEach(id => {
                const itemEl = document.querySelector('.foto-item[data-id="' + id + '"]');
                const imgSrc = itemEl ? itemEl.querySelector('img')?.src : '';

                const li = document.createElement('li');
                li.className = 'modal-exceso__item';
                li.dataset.id = id;
                li.innerHTML =
                    '<img src="' + imgSrc + '" alt="Foto ' + id + '" class="modal-exceso__thumb">' +
                    '<span class="modal-exceso__num">#' + (orden.indexOf(id) + 1) + '</span>' +
                    '<button class="modal-exceso__quitar" onclick="quitarDesdeExceso(\'' + id + '\')" title="Quitar">' +
                    '  <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>' +
                    '</button>';
                lista.appendChild(li);
            });

            const overlay = document.getElementById('modalExceso');
            overlay.classList.add('abierto');
            overlay.setAttribute('aria-hidden', 'false');
        }

        function cerrarModalExceso() {
            const overlay = document.getElementById('modalExceso');
            overlay.classList.remove('abierto');
            overlay.setAttribute('aria-hidden', 'true');
        }

        function quitarDesdeExceso(id) {
            const itemEl = document.querySelector('.foto-item[data-id="' + id + '"]');
            if (itemEl) toggleFavorito(itemEl);

            // Quitar item de la lista del modal
            const li = document.querySelector('#modalExcesoLista .modal-exceso__item[data-id="' + id + '"]');
            if (li) li.remove();

            // Actualizar subtítulo
            const excedente = seleccionadas.size - limite;
            if (excedente <= 0) {
                cerrarModalExceso();
                return;
            }
            document.getElementById('modalExcesoSub').textContent =
                'Seleccionaste ' + excedente + ' foto' + (excedente !== 1 ? 's' : '') +
                ' de más. Quita las que no quieras conservar.';

            // Actualizar numeración en la lista
            document.querySelectorAll('#modalExcesoLista .modal-exceso__item').forEach(item => {
                const itemId = item.dataset.id;
                const pos = orden.indexOf(itemId);
                const numEl = item.querySelector('.modal-exceso__num');
                if (numEl) numEl.textContent = '#' + (pos + 1);
            });
        }

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
                    btn.disabled     = false;
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
            } else if (seleccionadas.size > limite) {
                abrirModalExceso();
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
                btnVerTodas.className = 'gal-btn-nav';
                btnVerTodas.textContent = '⁝';
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

            // Reset zoom al cambiar de foto
            img.style.transform = 'scale(1) translate(0, 0)';
            img.dataset.zoomInit = ''; // permite reinicializar en cada foto

            img.style.opacity     = '0';
            spinner.style.display = 'block';
            img.onload = () => {
                spinner.style.display = 'none';
                img.style.opacity = '1';
                aplicarZoom(img); // ← aquí
            };
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
        mostrarModalBienvenidaSiPrimera();
    </script>
@endpush
