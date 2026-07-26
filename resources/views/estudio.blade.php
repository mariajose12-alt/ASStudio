@extends('layouts.landing')
@php
    $navLinks = [
        ['href' => '#hero',     'label' => 'Inicio'],
        ['href' => '#tarifas',  'label' => 'Tarifas'],
        ['href' => '#reglas',   'label' => 'Reglas'],
        ['href' => '#reserva', 'label' => 'Reservar'],
    ];
    $logoOverride = 'zehcnas.png';
@endphp

@section('title', 'Estudio para Renta — Zehcnas Studio')

@section('content')

    {{-- ══ HERO ESTUDIO ══ --}}
    <section class="es-hero" id="hero">
        <div class="es-hero-bg">
            <img src="{{ asset('images/medium/studio-interior.webp') }}" alt="Interior de Zehcnas Studio">
            <div class="es-hero-overlay"></div>
        </div>
        <div class="es-hero-content">

            <p class="es-hero-eyebrow">Zehcnas Studio</p>
            <h1 class="es-hero-title">El estudio es tuyo.<br><em>Hazlo tuyo.</em></h1>
            <p class="es-hero-sub">
                Espacio profesional equipado para fotografía y videografía, disponible por horas.
            </p>
            <div class="es-hero-pills">
                <span>Fotografía</span>
                <span>Videografía</span>
                <span>Ciclograma</span>
                <span>Fondos de color</span>
                <span>Iluminación</span>
                <span>Props &amp; Sets</span>
            </div>
        </div>
    </section>

    {{-- ══ MOSAICO ══ --}}
    <section class="es-mosaic-section">
        <div class="es-mosaic">
            <div class="es-mosaic-item es-tall">
                <img src="{{ asset('images/medium/studio-ciclorama.webp') }}" alt="Ciclograma">
                <span class="es-mosaic-label">Ciclograma</span>
            </div>
            <div class="es-mosaic-item">
                <img src="{{ asset('images/medium/studio-fondos.webp') }}" alt="Fondos de color">
                <span class="es-mosaic-label">Fondos</span>
            </div>
            <div class="es-mosaic-item">
                <img src="{{ asset('images/medium/studio-luces.webp') }}" alt="Iluminación">
                <span class="es-mosaic-label">Iluminación</span>
            </div>
            <div class="es-mosaic-item es-wide">
                <img src="{{ asset('images/medium/studio-set1.webp') }}" alt="Set completo">
                <span class="es-mosaic-label">Set completo</span>
            </div>
        </div>

        {{-- Franja de amenidades --}}
        <div class="es-amenities">
            <p class="es-amenities-label">Incluido en tu renta —</p>
            <div class="es-amenity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/>
                </svg>
                Reserva por horas
            </div>
            <div class="es-amenity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6M9 13h4"/>
                </svg>
                Fondos de colores
            </div>
            <div class="es-amenity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 2L15 8l6 1-4.5 4.5 1 6L12 17l-5.5 2.5 1-6L3 9l6-1z"/>
                </svg>
                Ciclograma
            </div>
            <div class="es-amenity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="5"/>
                    <path d="M12 1v3M12 20v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M1 12h3M20 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12"/>
                </svg>
                Flashes &amp; luces
            </div>
            <div class="es-amenity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Encargado presente
            </div>
            <div class="es-amenity">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <circle cx="7" cy="7" r="1.5"/>
                </svg>
                Props &amp; sets
            </div>
        </div>
    </section>

    {{-- ══ TARIFAS ══ --}}
    <section class="es-pricing-section" id="tarifas">
        <div class="container">
            <div class="es-pricing-header reveal">
                <p class="section-eyebrow">Tarifas</p>
                <h2 class="section-heading">Elige tu <em>modalidad</em></h2>
                <p class="section-subtext">Selecciona el tipo de producción y la cantidad de fondos que necesitas.</p>
            </div>

            {{-- Tabs --}}
            <div class="es-tabs reveal">
                <button class="es-tab active" data-tab="foto">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="2" y="7" width="20" height="15" rx="2"/>
                        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                        <circle cx="12" cy="14" r="3"/>
                    </svg>
                    Fotografía
                </button>
                <button class="es-tab" data-tab="video">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <polygon points="23 7 16 12 23 17 23 7"/>
                        <rect x="1" y="5" width="15" height="14" rx="2"/>
                    </svg>
                    Videografía
                </button>
            </div>

            {{-- Grid Fotografía --}}
            <div class="es-price-grid" id="es-tab-foto">
                <div class="es-price-card">
                    <div class="es-price-badge">Más popular</div>
                    <p class="es-price-label">1 hora · 1 fondo</p>
                    <p class="es-price-desc">Un fondo de color de tu elección con todos los equipos disponibles.</p>
                    <p class="es-price-val">1,500 <span>RD$</span></p>
                    <ul class="es-price-includes">
                        <li>1 fondo de color</li>
                        <li>Equipos de iluminación</li>
                        <li>Props &amp; sets básicos</li>
                        <li>Encargado presente</li>
                    </ul>
                </div>
                <div class="es-price-card featured">
                    <p class="es-price-label">1 hora · 2 fondos</p>
                    <p class="es-price-desc">Dos fondos de color de tu elección con todos los equipos disponibles.</p>
                    <p class="es-price-val">2,000 <span>RD$</span></p>
                    <ul class="es-price-includes">
                        <li>2 fondos de color</li>
                        <li>Equipos de iluminación</li>
                        <li>Props &amp; sets básicos</li>
                        <li>Encargado presente</li>
                    </ul>
                </div>
                <div class="es-price-card">
                    <p class="es-price-label">1 hora · 3 fondos</p>
                    <p class="es-price-desc">Tres fondos de color de tu elección con todos los equipos disponibles.</p>
                    <p class="es-price-val">2,200 <span>RD$</span></p>
                    <ul class="es-price-includes">
                        <li>3 fondos de color</li>
                        <li>Equipos de iluminación</li>
                        <li>Props &amp; sets básicos</li>
                        <li>Encargado presente</li>
                    </ul>
                </div>
            </div>

            {{-- Grid Videografía --}}
            <div class="es-price-grid" id="es-tab-video" style="display:none">
                <div class="es-price-card">
                    <p class="es-price-label">1 hora · 1 fondo</p>
                    <p class="es-price-desc">Un fondo de color de tu elección con todos los equipos disponibles.</p>
                    <p class="es-price-val">2,500 <span>RD$</span></p>
                    <ul class="es-price-includes">
                        <li>1 fondo de color</li>
                        <li>Equipos de iluminación</li>
                        <li>Props &amp; sets básicos</li>
                        <li>Encargado presente</li>
                    </ul>
                </div>
                <div class="es-price-card featured">
                    <p class="es-price-label">1 hora · 2 fondos</p>
                    <p class="es-price-desc">Dos fondos de color de tu elección con todos los equipos disponibles.</p>
                    <p class="es-price-val">3,000 <span>RD$</span></p>
                    <ul class="es-price-includes">
                        <li>2 fondos de color</li>
                        <li>Equipos de iluminación</li>
                        <li>Props &amp; sets básicos</li>
                        <li>Encargado presente</li>
                    </ul>
                </div>
                <div class="es-price-card">
                    <p class="es-price-label">1 hora · 3 fondos</p>
                    <p class="es-price-desc">Tres fondos de color de tu elección con todos los equipos disponibles.</p>
                    <p class="es-price-val">3,200 <span>RD$</span></p>
                    <ul class="es-price-includes">
                        <li>3 fondos de color</li>
                        <li>Equipos de iluminación</li>
                        <li>Props &amp; sets básicos</li>
                        <li>Encargado presente</li>
                    </ul>
                </div>
            </div>

            {{-- Extras --}}
            <div class="es-extras reveal">
                <div class="es-extra-card dark">
                    <div class="es-extra-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="es-extra-label">Hora extra — Fotografía</p>
                        <p class="es-extra-val">1,000 <span>RD$ / hora</span></p>
                    </div>
                </div>
                <div class="es-extra-card dark">
                    <div class="es-extra-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="es-extra-label">Hora extra — Videografía</p>
                        <p class="es-extra-val">2,000 <span>RD$ / hora</span></p>
                    </div>
                </div>
                <div class="es-extra-card warm">
                    <div class="es-extra-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4M12 8h.01"/>
                        </svg>
                    </div>
                    <div>
                        <p class="es-extra-label">Asesoría durante la renta</p>
                        <p class="es-extra-val accent">750 <span>RD$</span></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ REGLAS ══ --}}
    <section class="es-rules-section" id="reglas">
        <div class="container">
            <div class="es-rules-header reveal">
                <p class="section-eyebrow">Antes de reservar</p>
                <h2 class="section-heading">Reglas y <em>términos</em></h2>
                <p class="section-subtext">Léelas antes de reservar. Están pensadas para garantizar la mejor experiencia para todos.</p>
            </div>

            <div class="es-rules-grid reveal">

                <div class="es-rule-card">
                    <span class="es-rule-num">01</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Pago previo obligatorio</h4>
                        <p class="es-rule-text">El pago total de la renta debe realizarse para poder agendar la fecha y hora deseada.</p>
                    </div>
                </div>

                <div class="es-rule-card">
                    <span class="es-rule-num">02</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Puntualidad estricta</h4>
                        <p class="es-rule-text">Tienes 15 minutos previos a tu reserva para preparar equipos. La sesión debe concluir puntualmente. Tiempo adicional implica rentar horas extras.</p>
                    </div>
                </div>

                <div class="es-rule-card es-rule-alert">
                    <span class="es-rule-num">03</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Prohibido fumar</h4>
                        <p class="es-rule-text">Prohibido fumar cigarrillos y cigarrillos electrónicos dentro del estudio.</p>
                    </div>
                </div>

                <div class="es-rule-card">
                    <span class="es-rule-num">04</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Notifica tu grupo</h4>
                        <p class="es-rule-text">Informa la cantidad de personas. Para más de 15 personas se aplica un fee de limpieza de RD$500.</p>
                    </div>
                </div>

                <div class="es-rule-card es-rule-alert">
                    <span class="es-rule-num">05</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Cuida el ciclograma</h4>
                        <p class="es-rule-text">NO saltar, pisar, sentarse ni acostarse en la curva del ciclograma. La reposición tiene un valor de RD$50,000 hasta RD$250,000.</p>
                    </div>
                </div>

                <div class="es-rule-card">
                    <span class="es-rule-num">06</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Cuida los equipos</h4>
                        <p class="es-rule-text">Cuida los equipos, props, sets y espacios. En caso de daños, debes reponer el artículo con su valor total.</p>
                    </div>
                </div>

                <div class="es-rule-card">
                    <span class="es-rule-num">07</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Limpieza del espacio</h4>
                        <p class="es-rule-text">No dejar basura fuera del zafacón. Deja el espacio como lo encontraste.</p>
                    </div>
                </div>

                <div class="es-rule-card">
                    <span class="es-rule-num">08</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Responsabilidad con menores</h4>
                        <p class="es-rule-text">Cuando haya niños presentes, los padres o tutores son responsables por ellos y sus acciones dentro del estudio.</p>
                    </div>
                </div>

                <div class="es-rule-card">
                    <span class="es-rule-num">09</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Encargado siempre presente</h4>
                        <p class="es-rule-text">Siempre habrá un encargado de Zehcnas Studio durante toda la reserva para seguridad y asistencia con props, sets y luces.</p>
                    </div>
                </div>

                <div class="es-rule-card">
                    <span class="es-rule-num">10</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Objetos olvidados</h4>
                        <p class="es-rule-text">Objetos dejados en el estudio se guardan por 3 días. Pasado ese tiempo, serán desechados.</p>
                    </div>
                </div>

                <div class="es-rule-card">
                    <span class="es-rule-num">11</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Reprogramación</h4>
                        <p class="es-rule-text">Se permite reprogramar 1 sola vez sin costo adicional. A partir de la segunda reprogramación se cobra RD$500.</p>
                    </div>
                </div>

                <div class="es-rule-card es-rule-alert">
                    <span class="es-rule-num">12</span>
                    <div class="es-rule-body">
                        <h4 class="es-rule-title">Incumplimiento</h4>
                        <p class="es-rule-text">El incumplimiento de cualquier regla da derecho a Zehcnas Studio a cancelar y expulsar de la renta de forma inmediata.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ══ CTA FINAL ══ --}}
    <section class="es-cta-section" id="reserva">
        <div class="es-cta-inner">
            <div class="es-cta-text reveal">
                <p class="section-eyebrow" style="color:var(--blue-lt)">¿Todo listo?</p>
                <h2 class="es-cta-heading">Reserva tu espacio<br><em>hoy mismo</em></h2>
                <p class="es-cta-sub">Paga el total de la renta para confirmar tu fecha y hora. El estudio te espera.</p>
            </div>
            <div class="es-cta-actions reveal">
                <a href="{{ route('estudio.reservar') }}" class="es-btn-primary">
                    Reservar estudio
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        // ── Scroll reveal ──
        const esRevealEls = document.querySelectorAll('.reveal');
        const esObserver  = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    esObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        esRevealEls.forEach(el => esObserver.observe(el));

        // ── Tabs de precio ──
        document.querySelectorAll('.es-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.es-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                var tipo = tab.dataset.tab;
                document.getElementById('es-tab-foto').style.display  = tipo === 'foto'  ? 'grid' : 'none';
                document.getElementById('es-tab-video').style.display = tipo === 'video' ? 'grid' : 'none';
            });
        });
    </script>
@endpush
