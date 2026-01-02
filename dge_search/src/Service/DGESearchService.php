<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_search (datos.gob.es)".
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

namespace Drupal\dge_search\Service;

use GuzzleHttp\Exception\RequestException;
use Drupal\Component\Serialization\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\taxonomy\Entity\Term;
use Drupal\views\Views;
use Drupal\Core\Url;
use Drupal\Core\Render\Markup;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
define('SEARCH_PATH', 'site-search');

class DGESearchService {

  /**
   *
   * @var endpoint
   */


  /**
   *
   * @var token
   */
  private $token;


  public function __construct() {

  }



  /**
   *
   * @return JsonResponse
   *
   */

    public function getSectionsByTabs() {


      $tabs= $this->getSections();

      $request = \Drupal::service('request_stack')->getCurrentRequest();
      $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $query = $request->query->all();
      $text_param = isset($query['text']) ? $query['text'] : '';

        if ($text_param) {
          $api_results =$this->queryCkanSolr( $text_param);
          $api_results_dataservice = $this->queryCkanSolrDataservice($text_param);
          if( $api_results_dataservice>0){
            $tab_dataservice = [
              'title' =>  t("Dataservices") . ' (' . $api_results_dataservice . ')',
              'url' =>   \Drupal::config('dge_ckan.config')->get('ckan_url')
              . '/' . $current_language
              .'/catalogo/servicios-datos?q='.urlencode($text_param),
            ];
            array_unshift($tabs, $tab_dataservice);
          }

          if ($api_results >0) {
            $tab_dataset = [
              'title' =>  t("Datasets") . ' (' . $api_results . ')',
              'url' =>   \Drupal::config('dge_ckan.config')->get('ckan_url')
              . '/' . $current_language
              .'/catalogo?q='.urlencode($text_param),
            ];
            array_unshift($tabs, $tab_dataset);
          }
        }
        $result = $this->generateHTMLTabs($tabs);
        return new JsonResponse($result);
    }

    public function getSections(){

      $vocabulary_id = 'buscador';
      $tabs = [];
      $first_tab_url = NULL;
      $terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree($vocabulary_id);
      $request = \Drupal::service('request_stack')->getCurrentRequest();
      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $query = $request->query->all();
      $text_param = isset($query['text']) ? $query['text'] : '';

      $date_params = [
        'field_fecha_publicacion_contenid',
        'field_fecha_publicacion_contenid_1',
        'field_fecha_evento',
        'field_fecha_evento_1',
        'created',
        'created_1'
      ];

      $date_exposed_filters = [];
      foreach ($date_params as $param) {
        if (isset($query[$param])) {
          $date_exposed_filters[$param] = $query[$param];
        }
      }
      $route_match = \Drupal::routeMatch();
      $current_view_id = $route_match->getParameter('view_id');
      foreach ($terms as $term) {
        $term_entity = Term::load($term->tid);
        if ($term_entity->hasTranslation($language)) {
          $title = $term_entity->getTranslation($language)->getName();
        } else {
          $title = $term_entity->getName();
        }
        $view_id = $term_entity->get('field_view_relacionada')->getString();
        $view = Views::getView($view_id);

        if ($view) {
          $view->setDisplay('buscador'); 
          $exposed_filters = ['text' => $text_param];
          if ($view_id === $current_view_id) {
            $exposed_filters = array_merge($exposed_filters, $date_exposed_filters);
          }
          $view->setExposedInput($exposed_filters);

          $view->execute();
          $count = $view->total_rows;

          if ($count > 0 || $view->id() === $current_view_id) {
            $url = $view->getUrl();
            if ($text_param) {
              $url = $url->setOption('query', ['text' => $text_param])->toString();
              $tabs[] = [
                'title' => $count > 0 ? $title . ' (' . $count . ')' : $title . ' (0)',
                'url' => $url,
              ];
            }
          }
        }
      }
      if (empty($tabs)) {
        return [];
      }
      return $tabs;
    }

  /**
   *
   * @return JsonResponse
   *
   */
  public function getSectionsByTabsCKAN() {

    $tabs =$this->getSections();
    $result = $this->generateHTMLTabsCKAN($tabs);

    return new JsonResponse($result);

  }

 /**
   *
   * @return array
   *
   */
  public function generateHTMLTabs($tabs) {
    $extracted_string = explode("/", trim($this->getActiveTab(), "/"))[0];
    $content = '<ul class="tabs">';
    foreach ($tabs as $tab) {
      if (str_contains($tab['url'], $extracted_string) != false) {
        $content .= '<li class="active"><a href="' . $tab['url'] . '">' . $tab['title'] . '</a></li>';
      } else {
        $content .= '<li><a href="' . $tab['url'] . '">' . $tab['title'] . '</a></li>';
      }
    }
    $content .= '</ul>';

    $result['#markup'] = $content;
    $result['#tabs'] = $tabs;

    return $result;
  }

  public function generateHTMLTabsCKAN($tabs) {
    $extracted_string = $this->getActiveTab();
    $content = '';
    foreach ($tabs as $tab) {
      if (strpos($tab['url'], $extracted_string) != false) {
        $content .= '<li class="active"><a href="' . $tab['url'] . '">' . $tab['title'] . '</a></li>';
      } else {
        $content .= '<li><a href="' . $tab['url'] . '">' . $tab['title'] . '</a></li>';
      }
    }
    $result['#markup'] = $content;
    $result['#tabs'] = $tabs;

    return $result;
  }

  public function queryCkanSolr($keys) {

    $query = ['q' => $keys];
    $api_url =  \Drupal::config('dge_ckan.config')->get('ckan_host'). 'action/package_search';
    $url_with_param = $api_url . '?' . http_build_query($query);
    $response = \Drupal::httpClient()->get($url_with_param, [
      'headers' => [
        'Authorization' => \Drupal::config('dge_ckan.config')->get('ckan_api_key'),
      ],
    ]);

    $data = json_decode($response->getBody(), TRUE);
    return $data['result']['count'];
  }

  public function queryCkanSolrDataservice($keys) {

    $query = ['q' => $keys];
    $api_url =  \Drupal::config('dge_ckan.config')->get('ckan_host'). 'action/package_search?fq=dataset_type:dataservice';
    $url_with_param = $api_url . '&' . http_build_query($query);
    $response = \Drupal::httpClient()->get($url_with_param, [
        'headers' => [
            'Authorization' => \Drupal::config('dge_ckan.config')->get('ckan_api_key'),
        ],
        ]);

    $data = json_decode($response->getBody(), TRUE);
    return $data['result']['count'];
  }

  public function getActiveTab(){
    $request_stack = \Drupal::service('request_stack');
    $current_request = $request_stack->getCurrentRequest();
    $current_url = $current_request->getUri();

    $pattern = '/\/' . preg_quote(SEARCH_PATH, '/') . '(.*?)\?/';
    preg_match($pattern, $current_url, $matches);
    $extracted_string = $matches;

    if (isset($extracted_string) && isset($extracted_string[1])){
      $extracted_string = $extracted_string[1];
    } else{
      $extracted_string= null;
    }
    return $extracted_string;
  }
}