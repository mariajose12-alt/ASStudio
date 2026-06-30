{{-- Trigger: cualquier botón con data-modal="cuentas-banco" lo abre --}}

<div id="modal-cuentas-banco" class="mcb-overlay" role="dialog" aria-modal="true" aria-label="Cuentas disponibles" hidden>
    <div class="mcb-panel">

        <div class="mcb-header">
            <h2 class="mcb-titulo">Cuentas disponibles</h2>
            <button type="button" class="mcb-cerrar" aria-label="Cerrar">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div class="mcb-body">
            @foreach($cuentas as $banco => $grupo)
                <div class="mcb-grupo">
                    <span class="mcb-banco">{{ $banco }}</span>
                    @foreach($grupo as $cuenta)
                        <div class="mcb-fila">
                            <div class="mcb-info">
                                <span class="mcb-numero">{{ $cuenta->numero_cuenta }}</span>
                                <span class="mcb-meta">
                                {{ ucfirst($cuenta->tipo) }} ·
                                <span class="mcb-moneda mcb-moneda--{{ strtolower($cuenta->moneda) }}">{{ $cuenta->moneda }}</span>
                            </span>
                                <span class="mcb-titular">{{ $cuenta->titular }}</span>
                            </div>
                            <button type="button"
                                    class="mcb-copiar"
                                    data-numero="{{ $cuenta->numero_cuenta }}"
                                    title="Copiar número">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2"/>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>
</div>

<style>
    .mcb-overlay {
        position: fixed;
        inset: 0;
        background: rgba(26,15,0,0.45);
        z-index: 900;
        display: flex;
        align-items: flex-end;        /* sheet desde abajo en mobile */
        justify-content: center;
        padding: 0;
        backdrop-filter: blur(2px);
    }
    .mcb-overlay[hidden] { display: none; }

    .mcb-panel {
        background: #ffffff;
        border-radius: 20px 20px 0 0;
        width: 100%;
        max-width: 480px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        animation: mcb-slide-up 0.25s cubic-bezier(0.32,0.72,0,1);
    }
    @keyframes mcb-slide-up {
        from { transform: translateY(100%); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }

    .mcb-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px 14px;
        border-bottom: 1px solid rgba(232,119,34,0.15);
        background: #fff3e8;
        border-radius: 20px 20px 0 0;
        flex-shrink: 0;
    }
    .mcb-titulo {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 18px;
        font-weight: 600;
        color: #1a0f00;
        margin: 0;
    }
    .mcb-cerrar {
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        display: flex;
        transition: color 0.15s;
    }
    .mcb-cerrar:hover { color: #1a0f00; }

    .mcb-body {
        overflow-y: auto;
        padding: 8px 0 24px;
        -webkit-overflow-scrolling: touch;
    }

    .mcb-grupo {
        padding: 14px 20px;
        border-bottom: 1px solid rgba(26,15,0,0.06);
    }
    .mcb-grupo:last-child { border-bottom: none; }

    .mcb-banco {
        display: block;
        font-family: Arial, sans-serif;
        font-size: 10px;
        font-weight: 700;
        color: #e87722;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 10px;
    }

    .mcb-fila {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #fafafa;
        border: 1px solid #f0e6da;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 8px;
    }
    .mcb-fila:last-child { margin-bottom: 0; }

    .mcb-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .mcb-numero {
        font-family: 'Courier New', monospace;
        font-size: 17px;
        font-weight: 700;
        color: #1a0f00;
        letter-spacing: 1px;
    }

    .mcb-meta {
        font-family: Arial, sans-serif;
        font-size: 12px;
        color: #9ca3af;
    }

    .mcb-titular {
        font-family: Arial, sans-serif;
        font-size: 12px;
        font-weight: 600;
        color: #6b5c4e;
    }

    .mcb-copiar {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        gap: 5px;
        background: #fff3e8;
        border: 1px solid rgba(232,119,34,0.35);
        border-radius: 999px;
        padding: 8px 14px;
        color: #e87722;
        font-family: Arial, sans-serif;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
        white-space: nowrap;
    }
    .mcb-copiar:hover { background: #ffe8cc; }
    .mcb-copiar.copiado {
        background: #eafaf1;
        border-color: #16a34a;
        color: #16a34a;
    }

    @media (min-width: 520px) {
        .mcb-overlay { align-items: center; padding: 20px; }
        .mcb-panel   { border-radius: 16px; max-height: 75vh; }
        @keyframes mcb-slide-up {
            from { transform: translateY(16px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
    }
</style>

<script>
    (function () {
        const modal   = document.getElementById('modal-cuentas-banco');
        const overlay = modal;

        function abrirModal() {
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
        }
        function cerrarModal() {
            modal.hidden = true;
            document.body.style.overflow = '';
        }

        // Cualquier botón con data-modal="cuentas-banco" abre el modal
        document.addEventListener('click', e => {
            if (e.target.closest('[data-modal="cuentas-banco"]')) abrirModal();
        });

        modal.querySelector('.mcb-cerrar').addEventListener('click', cerrarModal);

        // Click en el overlay (fuera del panel) cierra
        overlay.addEventListener('click', e => {
            if (e.target === overlay) cerrarModal();
        });

        // Escape cierra
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && !modal.hidden) cerrarModal();
        });

        // Copiar número
        modal.querySelectorAll('.mcb-copiar').forEach(btn => {
            btn.addEventListener('click', () => {
                navigator.clipboard.writeText(btn.dataset.numero).then(() => {
                    btn.classList.add('copiado');
                    btn.title = '¡Copiado!';
                    setTimeout(() => {
                        btn.classList.remove('copiado');
                        btn.title = 'Copiar número';
                    }, 2000);
                });
            });
        });
    })();
</script>
