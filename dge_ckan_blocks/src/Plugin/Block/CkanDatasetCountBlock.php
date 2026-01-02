<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_ckan_blocks (datos.gob.es)".
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

namespace Drupal\dge_ckan_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;

/**
 * @Block(
 *   id = "ckan_dataset_count_block",
 *   admin_label = @Translation("CKAN Dataset Count")
 * )
 */
class CkanDatasetCountBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $count = 0;

    if (!function_exists('dge_ckan_init_class')) {
      throw new \Exception('La función initializeCkanClass no está disponible. Asegúrate de que el módulo dge_ckan está habilitado.');
    }

    $ckan = dge_ckan_init_class();

    $response = $ckan->request('package_search','','-state:draft',0,0,'');

    if (isset($response['success']) && $response['success']) {
      $count = $response['result']['count'];
  
      if ($count >= 1000) {
          $count = number_format($count, 0, '', '.');
      }
     
  }
  
    
    return [
      '#title' => t('CKAN Dataset Count'),
      '#markup' => $count,
      '#cache' => ['max-age' => 3600],
    ];
  }
}
