// assets/controllers/margin_top_main_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["navbar", "mainContent"];
  debounceTimer = null;

  connect() {
    // La logique s'exécute ici au chargement initial
    // et après chaque navigation Turbo.
    this.updateMainContentMargin();
    window.addEventListener("resize", this.handleResize.bind(this));
  }

  disconnect() {
    window.removeEventListener("resize", this.handleResize.bind(this));
  }

  handleResize() {
    clearTimeout(this.debounceTimer);
    this.debounceTimer = setTimeout(() => {
      this.updateMainContentMargin();
    }, 100);
  }

  updateMainContentMargin() {
    if (!this.hasNavbarTarget || !this.hasMainContentTarget) {
      console.warn("Stimulus: Les cibles 'navbar' ou 'mainContent' sont manquantes.");
      return;
    }

    // Le code à l'intérieur de requestAnimationFrame s'exécutera
    // après que le navigateur a calculé toutes les hauteurs.
    window.requestAnimationFrame(() => {
      const navbarHeight = this.navbarTarget.offsetHeight;
      this.mainContentTarget.style.marginTop = `${navbarHeight}px`;
    });
  }
}