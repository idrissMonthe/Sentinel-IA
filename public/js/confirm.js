let formEnAttente = null;

function creerModalConfirmation() {
    if (document.getElementById('confirmation-modal')) {
        return;
    }

    const modal = document.createElement('div');

    modal.id = 'confirmation-modal';
    modal.className = 'confirmation-modal';

    modal.innerHTML =
        '<div class="confirmation-overlay"></div>' +
        '<div class="confirmation-dialog" role="dialog" aria-modal="true" aria-labelledby="confirmation-title">' +
            '<div class="confirmation-icon">!</div>' +
            '<div class="confirmation-content">' +
                '<h2 id="confirmation-title">Confirmation</h2>' +
                '<p id="confirmation-message"></p>' +
            '</div>' +
            '<div class="confirmation-actions">' +
                '<button type="button" class="confirmation-btn confirmation-btn-cancel" id="confirmation-cancel">Annuler</button>' +
                '<button type="button" class="confirmation-btn confirmation-btn-confirm" id="confirmation-confirm">Confirmer</button>' +
            '</div>' +
        '</div>';

    document.body.appendChild(modal);

    const cancelButton = document.getElementById('confirmation-cancel');
    const confirmButton = document.getElementById('confirmation-confirm');
    const overlay = modal.querySelector('.confirmation-overlay');

    cancelButton.addEventListener('click', fermerModal);
    overlay.addEventListener('click', fermerModal);

    confirmButton.addEventListener('click', function () {
        if (!formEnAttente) {
            fermerModal();
            return;
        }

        const form = formEnAttente;

        formEnAttente = null;
        fermerModal();

        form.dataset.confirmed = 'true';
        form.requestSubmit();
    });

    document.addEventListener('keydown', function (event) {
        const modalActif = document.getElementById('confirmation-modal');

        if (
            event.key === 'Escape' &&
            modalActif &&
            modalActif.classList.contains('is-visible')
        ) {
            fermerModal();
        }
    });
}

function ouvrirModal(message, form) {
    creerModalConfirmation();

    formEnAttente = form;

    const modal = document.getElementById('confirmation-modal');
    const messageElement = document.getElementById('confirmation-message');
    const confirmButton = document.getElementById('confirmation-confirm');

    messageElement.textContent = message;

    modal.classList.add('is-visible');
    document.body.classList.add('modal-open');

    setTimeout(function () {
        confirmButton.focus();
    }, 50);
}

function fermerModal() {
    const modal = document.getElementById('confirmation-modal');

    if (!modal) {
        return;
    }

    modal.classList.remove('is-visible');
    document.body.classList.remove('modal-open');

    formEnAttente = null;
}

document.addEventListener('submit', function (event) {
    const form = event.target.closest('form[data-confirm]');

    if (!form) {
        return;
    }

    if (form.dataset.confirmed === 'true') {
        delete form.dataset.confirmed;
        return;
    }

    event.preventDefault();

    const message = form.getAttribute('data-confirm');

    if (!message) {
        return;
    }

    ouvrirModal(message, form);
});