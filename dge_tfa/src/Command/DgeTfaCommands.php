<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
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

namespace Drupal\dge_tfa\Command;

use Drush\Commands\DrushCommands;
use Drupal\user\Entity\User;


class DgeTfaCommands extends DrushCommands {

  /**
   *
   * @command dge_tfa:enable_all
   * @aliases dge-tfa-enable-all
   * @aliases dge-tfa-enable-all
   * @usage drush dge_tfa:enable_all
   *   Enables TFA for all users except those with the administrator role.
   * @usage drush dge_tfa:enable_all editor
   *   Enables TFA for all users with the "editor" role.
   */
  public function enableAll($role = null) {
    $user_storage = \Drupal::entityTypeManager()->getStorage('user');
    $users = $user_storage->loadMultiple();

    foreach ($users as $user) {
      if ($user->hasRole('administrator')) {
        continue;
      }

      if ($role && !$user->hasRole($role)) {
        continue;
      }

      $uid = $user->id();
      $user_data = \Drupal::service('user.data');

      $user_tfa_data = $user_data->get('tfa', $uid, 'tfa_email_otp') ?? [];
      $user_tfa_data['enable'] = 1;
      $user_data->set('tfa', $uid, 'tfa_email_otp', $user_tfa_data);

      $user_tfa_data = $user_data->get('tfa', $uid, 'tfa_user_settings') ?? [];
      $this->output()->writeln("UID: $uid with data " . print_r($user_tfa_data, true));
      if (isset($user_tfa_data['status']) && $user_tfa_data['status'] == true) {
        if (!isset($user_tfa_data['data']['plugins']['tfa_email_otp'])) {
          if (!isset($user_tfa_data['data']['plugins'])) {
            $user_tfa_data['data']['plugins'] = [];
          }
          $user_tfa_data['data']['plugins']['tfa_email_otp'] = 'tfa_email_otp';
          $user_tfa_data['saved'] = \Drupal::time()->getRequestTime();
        }
      } else {
        $user_tfa_data = [
          'saved' => \Drupal::time()->getRequestTime(),
          'status' => true,
          'data' => [
            'plugins' => [
              'tfa_email_otp' => 'tfa_email_otp'
            ],
          ],
          'validation_skipped' => 1,
        ];
      }
      $user_data->set('tfa', $uid, 'tfa_user_settings', $user_tfa_data);

      $this->output()->writeln("TFA enabled for user: {$user->getDisplayName()} (UID: $uid)");
    }

    $this->output()->writeln("Email TFA has been enabled for all applicable users.");
  }
}
