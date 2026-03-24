/**
  * Copyright (C) 2026 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_privacy_core (datos.gob.es)".
  *
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 2 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * @file
 * dge_privacy_core.js
 *
 * Handles global cookie consent polling and settings button clicks.
 * Dispatches a custom 'dgePrivacyConsentChanged' event when the cookie changes.
 */
(function (Drupal, once) {
  'use strict';

  /**
   * Gets a cookie by name.
   */
  function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === ' ') { c = c.substring(1, c.length); }
      if (c.indexOf(nameEQ) === 0) { return c.substring(nameEQ.length, c.length); }
    }
    return null;
  }

  Drupal.behaviors.dgePrivacyCore = {
    attach: function (context) {
      // Use once() on the body to ensure these listeners are only ever attached one time.
      once('dge-privacy-global-listeners', 'body', context).forEach(
        (body) => {

          let lastCookieValue = getCookie('cookiesjsr');
          setInterval(() => {
            const currentCookieValue = getCookie('cookiesjsr');
            if (currentCookieValue !== lastCookieValue) {
              lastCookieValue = currentCookieValue;
              
              const event = new Event('dgePrivacyConsentChanged');
              document.dispatchEvent(event);
            }
          }, 1000);

          body.addEventListener('click', function (e) {
            if (e.target?.classList.contains('js-open-cookie-settings')) {
              // We rely on the fallback logic.
              const settingsLink = document.querySelector('button.cookiesjsr-settings');
              if (settingsLink) {
                settingsLink.click();
              }
              else {
                // This hash comes from the module config (#editCookieSettings) to open the COOKiES window pop up when the COOKiES bottom panel isn't visible (user has already accepted/denied some cookies)
                window.location.hash = 'editCookieSettings';
                window.location.reload();
              }
            }
          });
        }
      );
    }
  };
})(Drupal, once);