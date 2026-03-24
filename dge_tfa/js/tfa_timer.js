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

(function (Drupal, $) {
  Drupal.behaviors.tfaTimer = {
    attach: function (context, settings) {

      const form = $('#tfa-entry-form', context);
      if (!form.length) {
        return;
      }

      const formContext = once('tfaTimerOnce', '#tfa-entry-form', context);
      formContext.forEach(function () {
        const tfaPendingTimeField = form.find('input[name="tfa_pending_time"]');
        if (!tfaPendingTimeField.length) {
          return;
        }

        let seconds = parseInt(tfaPendingTimeField.val(), 10);
        if (isNaN(seconds)) {
          return;
        } 

        if (seconds <= 0) {
          seconds = 5;
        }

        setTimeout(function() {
          location.reload();
        }, seconds * 1000);
      });
    }
  };
})(Drupal, jQuery);
