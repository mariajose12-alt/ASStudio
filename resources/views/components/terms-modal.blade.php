{{-- Componente: resources/views/components/terms-modal.blade.php --}}
<div id="termsModal" class="terms-modal">
    <div class="terms-modal-content">
        <div class="terms-modal-header">
            <h2 class="terms-modal-title">Términos y Condiciones</h2>
            <button class="terms-modal-close" onclick="closeTermsModal()" aria-label="Cerrar">&times;</button>
        </div>
        <div class="terms-modal-body">
            @include('partials.terms-content')
        </div>
        <div class="terms-modal-footer">
            <button class="terms-modal-btn terms-modal-btn-secondary" onclick="closeTermsModal()">No acepto</button>
            <button class="terms-modal-btn terms-modal-btn-primary" onclick="acceptTermsReservation()">Acepto y continuar</button>
        </div>
    </div>
</div>

<style>
    /* ── MODAL POP-UP ── */
    .terms-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s ease-in-out;
    }

    .terms-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .terms-modal-content {
        background-color: #fff;
        border-radius: 16px;
        max-height: 85vh;
        max-width: 700px;
        width: 90%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease-out;
    }

    .terms-modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .terms-modal-title {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--ink);
        margin: 0;
    }

    .terms-modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--muted);
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .terms-modal-close:hover {
        background: var(--cloud);
        color: var(--ink);
    }

    .terms-modal-body {
        overflow-y: auto;
        padding: 1.5rem;
        flex: 1;
    }

    .terms-modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .terms-modal-btn {
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }

    .terms-modal-btn-primary {
        background: var(--orange);
        color: #fff;
    }

    .terms-modal-btn-primary:hover {
        background: #d68029;
        transform: translateY(-2px);
    }

    .terms-modal-btn-secondary {
        background: var(--cloud);
        color: var(--ink);
    }

    .terms-modal-btn-secondary:hover {
        background: #e5e5e5;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 600px) {
        .terms-modal-content {
            max-width: 95%;
            max-height: 90vh;
        }

        .terms-modal-btn {
            flex: 1;
        }
    }
</style>

<script>
    function openTermsModal() {
        const modal = document.getElementById('termsModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeTermsModal() {
        const modal = document.getElementById('termsModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    function acceptTermsReservation() {
        // Marcar como aceptados
        const checkbox = document.querySelector('input[name="accept_terms"]');
        if (checkbox) {
            checkbox.checked = true;
        }
        localStorage.setItem('termsAccepted', 'true');
        closeTermsModal();
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', (event) => {
        const modal = document.getElementById('termsModal');
        if (modal && event.target === modal) {
            closeTermsModal();
        }
    });
</script>
