// assets/app.js
// (or assets/bootstrap.js - and then import it from app.js)
//import pour faire fonctionner stimulus bridges
import { startStimulusApp } from '@symfony/stimulus-bridge';

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));

import 'bootstrap';





/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

console.log('✅ app.js loaded');


import { Modal } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
  console.log('JS chargé ✅');
  const modalEl = document.getElementById('myModal');
  console.log('modalEl', modalEl);
  if (modalEl) {
    const modal = new Modal(modalEl);
    modal.show();
  }
});


