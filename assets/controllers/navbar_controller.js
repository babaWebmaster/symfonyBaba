// assets/controllers/navbar_controller.js

import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["logo"];

  connect() {
    window.addEventListener("scroll", this.onScroll.bind(this));
  }

  disconnect() {
    window.removeEventListener("scroll", this.onScroll.bind(this));
  }

  onScroll() {
    const scrollPosition = window.scrollY;
    
    if (scrollPosition > 1) {
      // Cible l'élément 'a' qui est le logo et ajoute la classe
      this.logoTarget.classList.add("logo-small"); 
      
    } else {
      this.logoTarget.classList.remove("logo-small");
      
    }
  }
}