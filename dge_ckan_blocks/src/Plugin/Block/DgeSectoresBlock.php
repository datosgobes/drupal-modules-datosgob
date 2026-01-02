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
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;
use Drupal\media\Entity\Media;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;


/**
 *
 * @Block(
 *   id = "dge_sectores_block",
 *   admin_label = @Translation("DGE Sectores"),
 *   category = @Translation("Custom")
 * )
 */
class DgeSectoresBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $total = 0;

    if (!function_exists('dge_ckan_init_class')) {
      throw new \Exception('La función initializeCkanClass no está disponible. Asegúrate de que el módulo dge_ckan está habilitado.');
    }

    $ckan = dge_ckan_init_class();
    
    $sectores_template = [];
    $taxonomy = Vocabulary::load('categorias');
    if ($taxonomy) {
      $sectores = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree($taxonomy->id());

      foreach ($sectores as $sector) {
        $num_packages = 0;
        $num_initiatives = 0;
        $num_empresas = 0;
        $num_aplicaciones = 0;

        $query = \Drupal::entityQuery('node')
          ->condition('type', 'iniciativa')
          ->condition('status', 1)
          ->condition('langcode', 'es')
          ->condition('field_initiative_category.target_id', $sector->tid)
          ->accessCheck(TRUE);
        $num_initiatives = $query->count()->execute();

        $query = \Drupal::entityQuery('node')
          ->condition('type', 'empresa_reutilizadora')
          ->condition('status', 1)
          ->condition('langcode', 'es')
          ->condition('field_categoria.target_id', $sector->tid)
          ->accessCheck(TRUE);
        $num_empresas = $query->count()->execute();

        $query = \Drupal::entityQuery('node')
          ->condition('type', 'aplicacion')
          ->condition('status', 1)
          ->condition('langcode', 'es')
          ->condition('field_categoria.target_id', $sector->tid)
          ->accessCheck(TRUE);
        $num_aplicaciones = $query->count()->execute();

        $file_url_generator = \Drupal::service('file_url_generator');

        $sector_obj = Term::load($sector->tid);
        if ($sector_obj) {
          if ($sector_obj->hasTranslation(\Drupal::languageManager()->getCurrentLanguage()->getId())){
            $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();
            $sector_obj = $sector_obj->getTranslation($current_language);
          }
        
          $field_imagen_url = '';
          if ($sector_obj->hasField('field_category_image') && !$sector_obj->get('field_category_image')->isEmpty()) {
              $media = $sector_obj->get('field_category_image')->entity;
              if ($media instanceof Media && $media->hasField('field_media_image')) {
                  $image_file = $media->get('field_media_image')->entity;
                  if ($image_file) {
                    $field_imagen_url = $file_url_generator->generate($image_file->getFileUri());
                  }
              }
          }

          $field_link_url = '';
          if ($sector_obj->hasField('field_link') && !$sector_obj->get('field_link')->isEmpty()) {
              $field_link = $sector_obj->get('field_link')->first();
              if ($field_link && $field_link->getUrl()) {
                $field_link_url = $field_link_url = $field_link->getUrl()->toString();
              }
          }

          $field_color_value = '';
          if ($sector_obj->hasField('field_color') && !$sector_obj->get('field_color')->isEmpty()) {
              $field_color_item = $sector_obj->get('field_color')->first();
              if ($field_color_item && isset($field_color_item->getValue()['color'])) {
                  $field_color_value = $field_color_item->getValue()['color'];
              }
          }

          $field_category_id_ckan = '';
          if ($sector_obj->hasField('field_category_id_ckan') && !$sector_obj->get('field_category_id_ckan')->isEmpty()) {
            $field_category_id_ckan = $sector_obj->get('field_category_id_ckan')->value;

            $current_user = \Drupal::currentUser();
            $roles = $current_user->getRoles();
            $account = User::load($current_user->id());

            $admin_roles = ['administrator', 'aporta'];
            $organization_roles = ['publicador', 'reutilizador'];

            $capacity_filter = 'capacity:public';

            if (array_intersect($admin_roles, $roles)) {
              $capacity_filter = '(capacity:private OR capacity:public)';
            } elseif (array_intersect($organization_roles, $roles)) {
              $organization = $this->getUserOrganizationCKANName($account);
              if ($organization) {
                $capacity_filter = '(capacity:public OR (capacity:private AND organization:' . $organization . '))';
              }
            }

            $fq = [$capacity_filter];
            if (!empty($organization_filter)) {
              $fq[] = $organization_filter;
            }
            $fq[] = 'theme_id:' . $field_category_id_ckan;

            $fq_string = implode(' AND ', $fq);
            \Drupal::logger('dge_ckan_blocks')->debug($fq_string);
            
            $response = $ckan->request(
              'package_search',
              '',
              $fq_string,
              0,
              0
            );
            if (isset($response['success']) && $response['success']) {
              $num_packages_withoutFormat= $response['result']['count'];
              $num_packages = number_format($num_packages_withoutFormat, 0, ',', '.'); 
              \Drupal::logger('dge_ckan_blocks')->debug('Consulta exitosa a CKAN. Número de paquetes: @num_packages', ['@num_packages' => $num_packages]);
              \Drupal::logger('dge_ckan_blocks')->debug('Consulta exitosa a CKAN. Respuesta: @response', ['@response' => print_r($response, TRUE)]);
            } else {
              \Drupal::logger('dge_ckan_blocks')->error('Error en la consulta a CKAN: @response', ['@response' => print_r($response, TRUE)]);
            }
          }
        }

        $sectores_template[$sector->tid] = [
          'title' => $sector_obj->get('name')->value,
          'color' => $field_color_value,
          'imagen' => $field_imagen_url,
          'num_initiatives' => $num_initiatives,
          'num_packages' => $num_packages,
          'num_empresas' => $num_empresas,
          'num_aplicaciones' => $num_aplicaciones,
          'link' => $field_link_url,
        ];
      }
    }

    return [
      '#title' => t('Sectors'),
      '#theme' => 'dge_ckan_blocks_sectores',
      '#total' => $total,
      '#sectores' => $sectores_template,
    ];
  }

  protected function getUserOrganizationCKANName(AccountInterface $account) {
    if ($account->hasField('field_organizacion') && !$account->get('field_organizacion')->isEmpty()) {
      $organization_id = $account->get('field_organizacion')->target_id;

      $organization_term = Term::load($organization_id);

      if ($organization_term && $organization_term->hasField('field_ckan_organization_name')) {
        $ckan_organization_name = $organization_term->get('field_ckan_organization_name')->value;
        return $ckan_organization_name;
      }
    }
    return NULL;
  }
}
