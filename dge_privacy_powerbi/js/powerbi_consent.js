/**
  * Copyright (C) 2026 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_privacy_powerbi (datos.gob.es)".
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
 * Handles Power BI iframe consent blocking based on COOKiES module.
 */
(function (Drupal, once, drupalSettings) {
  'use strict';

  // --- Cookie Helper Functions ---
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

  function hasConsent(category) {
    try {
      const cookieValue = getCookie('cookiesjsr');
      if (!cookieValue) { return false; }
      const consentData = JSON.parse(decodeURIComponent(cookieValue));
      // Ensure consentData exists and the specific category is true
      return consentData && consentData[category] === true;
    } catch (e) {
      console.error('Error reading COOKiES consent data.', e);
      return false;
    }
  }

  // --- Iframe Blocking/Unblocking ---
  function blockIframe(iframe) {
    // Check if already blocked by JS to avoid reprocessing
    if (iframe.dataset.consentBlocked === 'true') {
        return;
    }

    // Ensure iframe.dataset.src gets the correct value from the data-src attribute set by PHP.
    const dataSrcValue = iframe.getAttribute('data-src');
    if (dataSrcValue && !iframe.dataset.src) {
        iframe.dataset.src = dataSrcValue;
    } else if (!dataSrcValue && iframe.getAttribute('src') && !iframe.dataset.src) {
        // Fallback if PHP missed it somehow, copy from src
        iframe.dataset.src = iframe.getAttribute('src');
    }

    // Always ensure src is removed when blocking
    if (iframe.hasAttribute('src')) {
        iframe.removeAttribute('src');
    }

    iframe.style.display = 'none';
    iframe.dataset.consentBlocked = 'true'; // Mark as blocked by JS

    // Only add placeholder if one doesn't exist
    if (!iframe.nextElementSibling?.classList.contains('cookie-powerbi-placeholder')) {
      const placeholder = document.createElement('div');
      placeholder.className = 'cookie-powerbi-placeholder';
      placeholder.setAttribute('role', 'region');

      // Check if settings are available before using them
      const texts = drupalSettings?.dge_privacy_powerbi;
      if (!texts) {
          console.error('Cannot create placeholder - dge_privacy_powerbi settings missing.');
          return;
      }
      placeholder.setAttribute('aria-label', texts.aria_label);
      placeholder.innerHTML =
        '<p>' + texts.message + '</p>' +
        '<button type="button" class="js-open-cookie-settings button">' + texts.button_text + '</button>';

      if (iframe.parentNode) {
        iframe.parentNode.insertBefore(placeholder, iframe.nextSibling);
      } else {
         console.warn('Cannot insert placeholder - iframe parent node not found.', iframe);
      }
    }
  }

  function unblockIframe(iframe) {
    const placeholder = iframe.nextElementSibling;
    if (placeholder?.classList.contains('cookie-powerbi-placeholder')) {
      placeholder.remove();
    }

    iframe.style.display = '';
    iframe.removeAttribute('data-consent-blocked'); // Unmark

    // Restore the src attribute using the value we stored in iframe.dataset.src.
    if (!iframe.getAttribute('src') && iframe.dataset.src) {
      iframe.setAttribute('src', iframe.dataset.src);
    } else if (!iframe.getAttribute('src') && iframe.getAttribute('data-src')) {
      // Fallback: If dataset.src is missing, try the attribute directly.
      iframe.setAttribute('src', iframe.getAttribute('data-src'));
    } else if (!iframe.getAttribute('src') && !iframe.dataset.src && !iframe.getAttribute('data-src')) {
        console.warn('Cannot unblock iframe - src, dataset.src and data-src attribute are all missing.', iframe);
    }
  }

  // --- Main Evaluation Logic ---
  function reevaluate() {
    // Check if settings are available
    if (!drupalSettings || !drupalSettings.dge_privacy_powerbi) {
         // Log less intrusively or handle silently if settings might not always be present
         // console.error('dge_privacy_powerbi settings not found.');
         return;
    }
    const { cookie_category } = drupalSettings.dge_privacy_powerbi;
    if (!cookie_category) {
        console.error('Cookie category not defined in drupalSettings.dge_privacy_powerbi.');
        return;
    }

    const consentGiven = hasConsent(cookie_category);
    // Target only iframes that have been processed by our PHP code
    const powerBiIframes = document.querySelectorAll('iframe.dge-privacy-powerbi-iframe');

    powerBiIframes.forEach(function (iframe) {
      const isBlockedByJS = iframe.dataset.consentBlocked === 'true';

      if (consentGiven) {
        // If consent is given and it was blocked by JS, unblock it.
        // Also unblock if src is missing but data-src exists (initial load case).
        if (isBlockedByJS || (!iframe.hasAttribute('src') && iframe.hasAttribute('data-src'))) {
          unblockIframe(iframe);
        }
      } else {
        // If consent is NOT given and it's not already marked as blocked by JS, block it.
        if (!isBlockedByJS) {
          blockIframe(iframe);
        }
      }
    });
  }

  // --- Drupal Behavior ---
  Drupal.behaviors.dgePrivacyPowerBi = {
    attach: function (context) {
      // 1. Evaluate any new iframes that just got loaded (e.g., via AJAX).
      const iframesInContext = once('dgePrivacyPowerBi', 'iframe.dge-privacy-powerbi-iframe', context);
      if (iframesInContext.length) {
        reevaluate();
      }

      // 2. Listen for the global consent change event from dge_privacy_core.
      once('dge-powerbi-consent-listener', 'body', context).forEach(() => {
        document.addEventListener('dgePrivacyConsentChanged', reevaluate);
      });
    }
  };

})(Drupal, once, drupalSettings);