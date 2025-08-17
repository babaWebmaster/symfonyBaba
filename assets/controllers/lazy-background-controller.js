// assets/controllers/footer_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        mobileBackgroundUrl: String,
        desktopBackgroundUrl: String
    }

    connect() {
        this.observer = new IntersectionObserver(this.handleIntersection.bind(this), {
            root: null, // On observe par rapport au viewport
            rootMargin: '0px',
            threshold: 0.1 // Déclenche quand 10% du footer est visible
        });
        this.observer.observe(this.element);
    }

    disconnect() {
        this.observer.disconnect();
        window.removeEventListener('resize', this.setBg.bind(this));
    }

    handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Le footer est visible, on charge l'image
                this.setBg();
                // On arrête d'observer pour ne pas recharger l'image
                this.observer.disconnect();
                // On ajoute l'écouteur de redimensionnement une fois que l'image est chargée
                window.addEventListener('resize', this.setBg.bind(this));
            }
        });
    }

    setBg() {
        if (window.innerWidth >= 768) {
            this.element.style.backgroundImage = `url("${this.desktopBackgroundUrlValue}")`;
        } else {
            this.element.style.backgroundImage = `url("${this.mobileBackgroundUrlValue}")`;
        }
    }
}