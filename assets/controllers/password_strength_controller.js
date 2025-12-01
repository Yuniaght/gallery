import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["input", "progressBar", "message"];

    connect() {
        this.updateStrength(); // Vérification initiale (si pré-rempli)
    }

    updateStrength() {
        const value = this.inputTarget.value;
        let score = 0;
        let message = "";
        let colorClass = "bg-danger";

        if (value.length === 0) {
            this.resetUI();
            return;
        }

        // --- RÈGLES DE CALCUL (Simulation de PasswordStrength) ---

        // 1. Longueur minimale (Base)
        if (value.length >= 8) score += 20;
        if (value.length >= 12) score += 20;

        // 2. Complexité
        if (/[A-Z]/.test(value)) score += 15; // Majuscule
        if (/[a-z]/.test(value)) score += 15; // Minuscule
        if (/[0-9]/.test(value)) score += 15; // Chiffre
        if (/[^A-Za-z0-9]/.test(value)) score += 15; // Caractère spécial

        // --- DÉFINITION DU NIVEAU ---
        if (score < 40) {
            message = "Très faible";
            colorClass = "bg-danger"; // Rouge
        } else if (score < 70) {
            message = "Moyen";
            colorClass = "bg-warning"; // Orange
        } else if (score < 100) {
            message = "Bon";
            colorClass = "bg-info"; // Bleu
        } else {
            message = "Excellent";
            colorClass = "bg-success"; // Vert
            score = 100; // Cap à 100
        }

        // --- MISE À JOUR UI ---
        this.progressBarTarget.style.width = `${score}%`;
        this.progressBarTarget.className = `progress-bar ${colorClass}`; // On reset les classes et on met la couleur
        this.messageTarget.textContent = message;
        this.messageTarget.className = `small mt-1 text-end fw-bold text-${colorClass.replace('bg-', '')}`;
    }

    resetUI() {
        this.progressBarTarget.style.width = "0%";
        this.messageTarget.textContent = "";
    }
}
