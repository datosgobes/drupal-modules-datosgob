/**
  * Copyright (C) 2026 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_tfa (datos.gob.es)".
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

(function (Drupal, $, once) {
  Drupal.behaviors.tfaTimerAndSteps = {
    attach: function (context, settings) {

      const form = $('#tfa-entry-form', context);
      if (!form.length) return;

      once('tfaFullLogic', '#tfa-entry-form', context).forEach(function () {
        

        const step1Elements = form.find('.dge-tfa-step-1, #edit-validation-send');
        const step2Elements = form.find('.step-two');
        const tfaPendingTimeField = form.find('input[name="tfa_pending_time"]');
        const backToStep1Link = form.find('.js-tfa-back-to-step-1');

        backToStep1Link.addClass('is-disabled');
        backToStep1Link.attr('aria-disabled', 'true');
        backToStep1Link.attr('tabindex', '-1');

        setTimeout(function () {
          backToStep1Link.removeClass('is-disabled');
          backToStep1Link.removeAttr('aria-disabled');
          backToStep1Link.removeAttr('tabindex');
        }, 60000);


        const loadingMessage = $('<div id="tfa-loading-message" style="display:none; font-weight:bold; margin-top:10px;">' + 
                                  '<span class="tfa-spinner"></span>'+
                                  Drupal.t('Authenticating your user, please wait a moment...') + 
                                 '</div>');

        form.find('#edit-actions').after(loadingMessage);

        let currentStep = sessionStorage.getItem('dge_tfa_step') || '1';

        function updateVisibility() {
          if (currentStep === '1') {
            step1Elements.show();
            step2Elements.hide();
          } else {
            step1Elements.hide();
            step2Elements.show();
          }
        }

        
        updateVisibility();


        form.find('#edit-login').on('click', function() {

          const codeInput = form.find('#edit-code');
          const codeValue = codeInput.val().trim();

          if (!codeValue) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
          }

          if (sessionStorage.getItem('dge_tfa_step') === '2') {
            form.find('#edit-actions').hide();
            form.find('.dge-tfa-back-link.dge-tfa-step-2.step-two').hide();
            loadingMessage.fadeIn(200);
          }
        });


        form.find('#edit-validation-send').on('click', function() {
          sessionStorage.setItem('dge_tfa_step', '2');
        });


        $('a').not(form.find('a')).on('click', function() {
          sessionStorage.removeItem('dge_tfa_step');
        });


       
        if (tfaPendingTimeField.length) {
          let seconds = parseInt(tfaPendingTimeField.val(), 10);
          
          if (!isNaN(seconds)) {
            
            if (seconds <= 0) seconds = 5;

            setTimeout(function() {
              
              sessionStorage.removeItem('dge_tfa_step'); 
              location.reload();
            }, seconds * 1000);
          }
        }
      });


      
      once('tfaBackLinkHandler', '.js-tfa-back-to-step-1', context).forEach(function (element) {
        $(element).on('click', function (e) {
          
          if ($(this).hasClass('is-disabled')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
          }
          e.preventDefault();
          
          location.reload();
        });
      });
    }
  };
})(Drupal, jQuery, once);
