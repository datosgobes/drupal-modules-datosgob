/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_social_media_share (datos.gob.es)".
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

(function (Drupal) {
     Drupal.behaviors.copyToClipboard = {
      attach: function (context, settings) {
        var copyToClipboard = function (url) {
          navigator.clipboard.writeText(url).then(function() {
          }).catch(function(err) {
            console.error('Error al copiar al portapapeles: ', err);
          });
        };
    
        var linkElements = document.querySelectorAll('.copy-to-clipboard');
        linkElements.forEach(function(linkElement) {
          linkElement.addEventListener('click', function(event) {
            event.preventDefault();
    
            var link = event.currentTarget.getAttribute('href'); 
            copyToClipboard(link);
            showCopyNotification(linkElement);
          });
        });
      }
    };
    
    
    function showCopyNotification(element) {
      const notification = document.createElement('div');
      notification.className = 'copy-notification';
    
      const checkmark = document.createElement('img');
      checkmark.src = '/themes/custom/dge_theme/images/icons/checkmark.svg';
      checkmark.alt = 'Copiado';
      notification.appendChild(checkmark);
    
      const text = document.createElement('span');
      text.textContent = '¡Enlace copiado al portapapeles!';
      notification.appendChild(text);
    
      element.closest('.dialog-container').appendChild(notification);
    
      setTimeout(() => notification.remove(), 4000);
    }
    

    document.addEventListener('DOMContentLoaded', function () {
      const shareIcons = document.querySelectorAll('.share-icon'); 
      const body = document.body;
      const focusableElementsString = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex="0"], [contenteditable]';
  
      shareIcons.forEach((shareIcon, index) => {
          shareIcon.addEventListener('click', function () {
              const modal = shareIcon.closest('.block-social-media').querySelector('.dialog');
              const modalOpacity = shareIcon.closest('.block-social-media').querySelector('.dialog-opacity');
              const closeModalButton = modal.querySelector('.dialog-close button');
  
              if (!modal || !modalOpacity) return;
  
              modal.classList.add('active');
              modalOpacity.classList.add('active');
              body.classList.add('modal-open');
  
              let focusableElements = modal.querySelectorAll(focusableElementsString);
              if (focusableElements.length > 0) {
                  focusableElements[0].focus();
              }
  
              closeModalButton.addEventListener('click', () => closeModal(modal, modalOpacity));
              modalOpacity.addEventListener('click', () => closeModal(modal, modalOpacity));
  
              modal.addEventListener('keydown', function (e) {
                  handleKeyDown(e, modal, focusableElements);
              });
  
              modal.addEventListener('click', function (e) {
                  if (e.target.matches('.copy-to-clipboard')) {
                      e.preventDefault();
                      const link = e.target.getAttribute('href');
                      navigator.clipboard.writeText(link)
                          .then(() => showCopyNotification(modal))
                          .catch(err => console.error('Error al copiar: ', err));
                  }
              });
          });
      });
  
      function closeModal(modal, modalOpacity) {
          modal.classList.remove('active');
          modalOpacity.classList.remove('active');
          body.classList.remove('modal-open');
      }
  
      function handleKeyDown(e, modal, focusableElements) {
          const isTabPressed = (e.key === 'Tab' || e.keyCode === 9);
          if (!isTabPressed) return;
  
          const firstFocusableElement = focusableElements[0];
          const lastFocusableElement = focusableElements[focusableElements.length - 1];
  
          if (e.shiftKey) {
              if (document.activeElement === firstFocusableElement) {
                  lastFocusableElement.focus();
                  e.preventDefault();
              }
          } else {
              if (document.activeElement === lastFocusableElement) {
                  firstFocusableElement.focus();
                  e.preventDefault();
              }
          }
      }
  
      function showCopyNotification(modal) {
          const notification = document.createElement('div');
          notification.className = 'copy-notification';
  
          const checkmark = document.createElement('img');
          checkmark.src = '/themes/custom/dge_theme/images/icons/checkmark.svg';
          checkmark.alt = 'Copiado';
          notification.appendChild(checkmark);
  
          const text = document.createElement('span');
          text.textContent = '¡Enlace copiado al portapapeles!';
          notification.appendChild(text);
  
          modal.querySelector('.dialog-container').appendChild(notification);
  
          setTimeout(() => notification.remove(), 4000);
      }
  });
  
  })(Drupal);
  