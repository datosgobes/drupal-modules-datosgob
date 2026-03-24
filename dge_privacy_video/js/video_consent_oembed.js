
/**
  * Copyright (C) 2026 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_privacy_video (datos.gob.es)".
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
 * Handles video oEmbed consent for all videos.
 */
(function (Drupal, once, drupalSettings) {
  'use strict';

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

  function hasConsent(service) {
    try {
      const cookieValue = getCookie('cookiesjsr');
      if (!cookieValue) { return false; }
      const consentData = JSON.parse(decodeURIComponent(cookieValue));
      return consentData[service] === true;
    } catch (e) { return false; }
  }

  function blockIframe(iframe, service) {
    if (!iframe.dataset.src) {
      iframe.dataset.src = iframe.getAttribute('src') || '';
    }
    if (iframe.dataset.src) {
      iframe.removeAttribute('src');
    }
    iframe.style.display = 'none';
    if (!iframe.nextElementSibling?.classList.contains('cookie-video-placeholder')) {
      const placeholder = document.createElement('div');
      placeholder.className = 'cookie-video-placeholder';
      placeholder.setAttribute('role', 'region');
      const texts = drupalSettings.dge_privacy_video;
      placeholder.setAttribute('aria-label', texts.aria_label);
      placeholder.innerHTML =
        `<p>${texts.message.replace('Vídeos', service === 'youtube_video' ? 'YouTube' : 'Vimeo')}</p>` +
        '<button type="button" class="js-open-cookie-settings button">' + texts.button_text + '</button>';
      iframe.parentNode.insertBefore(placeholder, iframe.nextSibling);
    }
  }

  function unblockIframe(iframe) {
    const placeholder = iframe.nextElementSibling;
    if (placeholder?.classList.contains('cookie-video-placeholder')) {
      placeholder.remove();
    }
    iframe.style.display = '';
    if (!iframe.getAttribute('src') && iframe.dataset.src) {
      iframe.setAttribute('src', iframe.dataset.src);
    }
  }

  function extractVideoUrlFromOembed(src) {
    try {
      const url = new URL(src);
      if (url.pathname.includes('/media/oembed')) {
        const videoUrl = url.searchParams.get('url');
        if (videoUrl) {
          return decodeURIComponent(videoUrl);
        }
      }
      return src;
    } catch (e) {
      return src;
    }
  }

  function reevaluate() {
    const videoIframes = document.querySelectorAll(
      'iframe.media-oembed-content, iframe.dge-privacy-video-processed'
    );
    videoIframes.forEach(function (iframe) {
      const src = iframe.getAttribute('src') || iframe.dataset.src;
      if (src) {
        const videoUrl = extractVideoUrlFromOembed(src);
        if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be') || videoUrl.includes('youtube-nocookie.com')) {
          if (hasConsent('youtube_video')) {
            unblockIframe(iframe);
          } else {
            blockIframe(iframe, 'youtube_video');
          }
        } else if (videoUrl.includes('vimeo.com')) {
          if (hasConsent('vimeo_video')) {
            unblockIframe(iframe);
          } else {
            blockIframe(iframe, 'vimeo_video');
          }
        }
      }
    });
  }

  Drupal.behaviors.dgeVideoConsentOembed = {
    attach: function (context) {
      // 1. Re-evaluate any iframes just added to the DOM (e.g., AJAX)
      const videoIframes = once('dgeVideoConsentOembed', 'iframe.media-oembed-content, iframe.dge-privacy-video-processed', context);
      if (videoIframes.length) {
        reevaluate();
      }
      
      // 2. Listen for the global consent change event
      once('dge-video-consent-listener', 'body', context).forEach(() => {
        document.addEventListener('dgePrivacyConsentChanged', reevaluate);
      });
    }
  };
})(Drupal, once, drupalSettings);