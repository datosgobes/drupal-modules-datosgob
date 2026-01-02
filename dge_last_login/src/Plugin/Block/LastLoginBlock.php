<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_last_login (datos.gob.es)".
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

namespace Drupal\dge_last_login\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a map with amrs.
 *
 * @Block(
 *   id = "last_login_block",
 *   admin_label = @Translation("LastLogin block"),
 *   category = @Translation("dge_last_login"),
 * )
 */
class LastLoginBlock extends BlockBase
{

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $is_logg_in = \Drupal::currentUser()->isAuthenticated();

        if (isset ($is_logg_in) && isset($_SESSION['user_last_login'])) {
            $data = ['last_login' => $_SESSION['user_last_login']];
            $loggin = ['is_logg_in' => $is_logg_in];

            return [
                '#theme' => 'block__last_login',
                '#data' => $data,
                '#loggin' => $loggin,
            ];
        }
    }

    public function getCacheMaxAge() {
        return 0;
    }
}
