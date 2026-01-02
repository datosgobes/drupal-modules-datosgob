<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_comments (datos.gob.es)".
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

namespace Drupal\dge_comments\Service;

use GuzzleHttp\Exception\RequestException;
use Drupal\Component\Serialization\Json;


class DGECommentsService
{

  /**
   *
   * @var endpoint
   */


  /**
   *
   * @var token
   */
  private $token;


  public function __construct()
  {
  }


  /**
   *
   * @return array
   *   Service response.
   */


  public function getCommentsCkan()
  {

    try {

      $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
      $ckan_user_id = $user->get('field_ckan_user_id')->value;
      $roles = $user->getRoles();

      if (in_array('publicador', $user->getRoles()) && $ckan_user_id) {

        $url = \Drupal::config('dge_ckan.config')->get('ckan_host') . 'comments/userId';
        $query = ['ckan_user_id' => $ckan_user_id];
        $url_with_param = $url . '?' . http_build_query($query);
        $response = \Drupal::httpClient()->get($url_with_param, [
          'headers' => [
            'Authorization' => \Drupal::config('dge_ckan.config')->get('ckan_api_key'),
          ],
        ]);
      }

      if (in_array('aporta', $user->getRoles()) || in_array('administrator', $user->getRoles())) {

        $url = \Drupal::config('dge_ckan.config')->get('ckan_host') . 'comments';
        $response = \Drupal::httpClient()->get($url, [
          'headers' => [
            'Authorization' => \Drupal::config('dge_ckan.config')->get('ckan_api_key'),
          ],
        ]);
      }
    } catch (\Exception $e) {
      \Drupal::logger('getCommentsCkan')->error('Error api ckan conection: @error', ['@error' => $e->getMessage()]);
      return [];
    }
    return $response;
  }
}
