<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_widget (datos.gob.es)".
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
namespace Drupal\dge_widget\Controller;

use Drupal\dge_ckan\Service\CkanJsonLoader;
use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\dge_ckan\dge_ckan_json_label;

class DgeWidgetController extends ControllerBase {


  public function fechaCallback($parameter) {

    $nti_dge_dataset_label = $this->read('nti_dge_dataset.json');
    $frequency_label = $this->read('frequency_label.json');

    $ckan_organization_id = $this->convertIdToCkan($parameter);
    $response = $this->dgeWidgetCkanRequest($ckan_organization_id, 'metadata_modified desc', 0, 10000);
    
    $build = [
      '#theme' => 'widget_rss_fecha',
      '#response' => $response,
      '#title' => 'widget-rss-fecha',
      '#nti_dge_dataset_label' => $nti_dge_dataset_label,
      '#frequency_label' => $frequency_label,
    ];
    $output = \Drupal::service('renderer')->renderRoot($build);

    return new Response($output);
  }
  
  public function alphaCallback($parameter) {

    $nti_dge_dataset_label = $this->read('nti_dge_dataset.json');
    $frequency_label = $this->read('frequency_label.json');

    $ckan_organization_id = $this->convertIdToCkan($parameter);
    $response = $this->dgeWidgetCkanRequest($ckan_organization_id, 'title_string asc', 0, 10000);
    
    $build = [
      '#theme' => 'widget_rss_alpha',
      '#response' => $response,
      '#title' => 'widget-rss-alpha',
      '#nti_dge_dataset_label' => $nti_dge_dataset_label,
      '#frequency_label' => $frequency_label,
    ];
    $output = \Drupal::service('renderer')->renderRoot($build);

    return new Response($output);
  }
  
  public function callback($parameter) {

    $nti_dge_dataset_label = $this->read('nti_dge_dataset.json');
    $frequency_label = $this->read('frequency_label.json');

    $ckan_organization_id = $this->convertIdToCkan($parameter);
    $response = $this->dgeWidgetCkanRequest($ckan_organization_id, 'title_string asc', 0, 10000);
    
    $build = [
      '#theme' => 'widget_rss',
      '#response' => $response,
      '#title' => 'widget-rss',
      '#nti_dge_dataset_label' => $nti_dge_dataset_label,
      '#frequency_label' => $frequency_label,
    ];
    $output = \Drupal::service('renderer')->renderRoot($build);
    
    return new Response($output);
  }
  
  private function dgeWidgetCkanRequest($organization, $sort, $start, $rows) {
    $ckan = dge_ckan_init_class();
    $organization_dictionary = $ckan->request(
      'organization_show',
      $organization,
      false,
      '',
      '',
      '',
      '',
      ''
    );

    if ($organization_dictionary['success']) {
      $organization_name = $organization_dictionary['result']['name'];
    }

    $response = $ckan->request(
      'package_search',
      '',
      "organization:" . $organization_name,
      $rows,
      $start,
      $sort
    );

    return $response;
  }
    
  public function convertIdToCkan($id) {
    $vocabulary = Vocabulary::load('organizaciones');
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary->id());

    foreach ($terms as $term) {
      $term_entity = Term::load($term->tid);
      if ($term_entity->hasField('field_reference_id') && $term_entity->hasField('field_ckan_organization_id')) {

        $field_reference_id = $term_entity->get('field_reference_id')->value;

        if ($field_reference_id == $id) {
          return $term_entity->get('field_ckan_organization_id')->value;
        }
      }
    }
    return NULL; 
  }

  private function read(string $file_name): ?array {
    $path = \Drupal::service('extension.list.module')->getPath('dge_ckan') . '/json/' . $file_name;

    if (!file_exists($path)) {
      return NULL;
    }

    $data = file_get_contents($path);
    return json_decode($data, TRUE);
  }
}
