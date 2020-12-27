/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import { start } from '@hotwired/turbo';
// start the Stimulus application
import 'bootstrap/dist/css/bootstrap.css';
import './bootstrap';
// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

start();

const init = () => {
  document.querySelectorAll('form').forEach(elem =>
    elem.addEventListener('submit', () => {
      elem.querySelectorAll('button[type="submit"]').forEach(button => button.disabled = true);
    }),
  );
};

document.addEventListener('turbo:load', () => init());
