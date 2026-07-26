{{-- Componente: resources/views/components/privacy-modal.blade.php --}}
<div id="privacyModal" class="privacy-modal">
    <div class="privacy-modal-content">
        <div class="privacy-modal-header">
            <h2 class="privacy-modal-title">Política de Privacidad</h2>
            <button class="privacy-modal-close" onclick="closePrivacyModal()" aria-label="Cerrar">&times;</button>
        </div>
        <div class="privacy-modal-body">
            @include('partials.privacy-content')
        </div>
        <div class="privacy-modal-footer">
            <button class="privacy-modal-btn privacy-modal-btn-secondary" onclick="closePrivacyModal()">No acepto</button>
            <button class="privacy-modal-btn privacy-modal-btn-primary" onclick="acceptPrivacyReservation()">Acepto y continuar</button>
        </div>
    </div>
</div>

<style>
    /* ── MODAL POP-UP ── */
    .privacy-modal {
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

    .privacy-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .privacy-modal-content {
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

    .privacy-modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .privacy-modal-title {
        font-family: var(--font-serif);
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--ink);
        margin: 0;
    }

    .privacy-modal-close {
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

    .privacy-modal-close:hover {
        background: var(--cloud);
        color: var(--ink);
    }

    .privacy-modal-body {
        overflow-y: auto;
        padding: 1.5rem;
        flex: 1;
    }

    .privacy-modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .privacy-modal-btn {
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }

    .privacy-modal-btn-primary {
        background: var(--orange);
        color: #fff;
    }

    .privacy-modal-btn-primary:hover {
        background: #d68029;
        transform: translateY(-2px);
    }

    .privacy-modal-btn-secondary {
        background: var(--cloud);
        color: var(--ink);
    }

    .privacy-modal-btn-secondary:hover {
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
        .privacy-modal-content {
            max-width: 95%;
            max-height: 90vh;
        }

        .privacy-modal-btn {
            flex: 1;
        }
    }
</style>

<script>
    function openPrivacyModal() {
        const modal = document.getElementById('privacyModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closePrivacyModal() {
        const modal = document.getElementById('privacyModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    function acceptPrivacyReservation() {
        // Marcar como aceptada
        const checkbox = document.querySelector('input[name="accept_privacy"]');
        if (checkbox) {
            checkbox.checked = true;
        }
        localStorage.setItem('privacyAccepted', 'true');
        closePrivacyModal();
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', (event) => {
        const modal = document.getElementById('privacyModal');
        if (modal && event.target === modal) {
            closePrivacyModal();
        }
    });
</script>
