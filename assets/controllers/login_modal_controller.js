import { Controller } from '@hotwired/stimulus';
export default class extends Controller {
    connect() {
        const urlParams = new URLSearchParams(window.location.search);
        const shouldOpenByUrl = urlParams.has('login');

        const hasAuthError = this.element && this.element.querySelector('.alert-danger') !== null;

        if (shouldOpenByUrl || hasAuthError) {
            this.openModal();
        }
    }

    openModal() {
        if (window.bootstrap) {
            const modal = new window.bootstrap.default.Modal(this.element);
            modal.show();
        } else {
            console.error("Bootstrap n'est pas chargé.");
        }
    }
}
