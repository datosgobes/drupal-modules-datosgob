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
use Drupal\dge_ckan_blocks\Service\CkanService;

/**
 *
 * @Block(
 *   id = "ckan_dataservice_block",
 *   admin_label = @Translation("CKAN Dataservices")
 * )
 */
class CkanDataserviceBlock extends BlockBase {
  public function getCacheMaxAge() {
      return 0;
  }


  /**
   * {@inheritdoc}
   */
  public function build() {
    $settings = [
      'rows'  => 5,
      'start' => 0,
      'sort'  => 'metadata_created desc',
    ];

    $ckan_service = \Drupal::service('dge_ckan_blocks.ckan_service');
    
    $packages = $ckan_service->packageSearch('', 'served_by_dataservice:* AND -served_by_dataservice:"[]" AND -state:draft',
    $settings['rows'], $settings['start'], $settings['sort']);

    $ckan_url = \Drupal::config('dge_ckan.config')->get('ckan_url') .
    '/' . \Drupal::languageManager()->getCurrentLanguage()->getId() . '/'
    . \Drupal::config('dge_ckan.config')->get('ckan_pagina_dataset', 'dataset').'/dataservice';

    return [
      '#theme' => 'dge_ckan_blocks_datasets', 
      '#packages' => $packages,
      '#ckan_url' => $ckan_url,
      '#cache' => ['max-age' => 0]
    ];
  }
}
