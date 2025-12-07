import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  connect() {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.element.classList.add("visible");
          observer.unobserve(this.element); // optionnel : observer une seule fois
        }
      });
    });

    observer.observe(this.element);
  }
}
