import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        mobileBackgroundUrl: String,
        ipadBackgroundUrl: String,
        desktopBackgroundUrl: String
    }

    
    connect() {console.log('background');
        this.observer = new IntersectionObserver(this.handleIntersect.bind(this),{
            root: null,
            rootMargin: '200px',
            threshold: 0.1
        });

        this.observer.observe(this.element);
        //écouteur event pour gérer les changements d'ecran
        window.addEventListener('resize', this.updateBackground.bind(this));
    }

    // Add custom controller actions here
    // fooBar() { this.fooTarget.classList.toggle(this.bazClass) }

    disconnect() {
        if(this.observer)
            {
                this.observer.disconnect();
            }

        window.removeEventListener('resize', this.updateBackground.bind(this));    
    }

    handleIntersect(entries) {
        if (entries[0].isIntersecting) {
            this.updateBackground();
            this.observer.disconnect();
        }
    }

    updateBackground() {
        const width = window.innerWidth;
        if (width >= 1024) { 
            this.element.style.backgroundImage = `url("${this.desktopBackgroundUrlValue}")`;
        } else if (width >= 768) { 
            this.element.style.backgroundImage = `url("${this.ipadBackgroundUrlValue}")`;
        } else { 
            this.element.style.backgroundImage = `url("${this.mobileBackgroundUrlValue}")`;
        }
    }
}
