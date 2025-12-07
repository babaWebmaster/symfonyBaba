import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        console.log('Hello Stimulus connecté !');
        this.element.textContent = 'Hello Stimulus ! (contrôleur actif2)';
    }
}



