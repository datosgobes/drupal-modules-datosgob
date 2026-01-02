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

namespace Drupal\dge_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Url;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\Renderer;
use Drupal\views\Views;
use Drupal\taxonomy\Entity\Term;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\Query\QueryInterface;




class DGESearchController extends ControllerBase {

  public function handleSearch(Request $request) {
    $session = $request->getSession();
    $keys = $request->query->get('keys');
    $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $content_type = $request->query->get('content_type');

    $api_results=0;

    if(empty($content_type) || $content_type=='custom_content_type_ckan'){
      $miServicio = \Drupal::service('dge_search.services_api_sections');
      $api_results = $miServicio->queryCkanSolr($keys);
      $api_results_dataservice = $miServicio->queryCkanSolrDataservice($keys);
    }
    if ($api_results>0 && $content_type=='custom_content_type_ckan') {
      $path =  \Drupal::config('dge_ckan.config')->get('ckan_url');
      return new RedirectResponse($path. '/' . $current_language .'/catalogo?q='.urlencode($keys).'&sections_catalog=');
    } elseif ($api_results>0 && empty($content_type)) {
      $path =  \Drupal::config('dge_ckan.config')->get('ckan_url');
      return new RedirectResponse($path. '/' . $current_language .'/catalogo?q='.urlencode($keys));
    } elseif ($api_results == 0 && empty($content_type) && $api_results_dataservice>0) {
      $path =  \Drupal::config('dge_ckan.config')->get('ckan_url');
      return new RedirectResponse($path. '/' . $current_language .'/catalogo/servicios-datos?q='.urlencode($keys));
    } else if ($api_results == 0 && $content_type=='custom_content_type_ckan' && $api_results_dataservice>0) {
      $path =  \Drupal::config('dge_ckan.config')->get('ckan_url');
      return new RedirectResponse($path. '/' . $current_language .'/catalogo/servicios-datos?q='.urlencode($keys) .'&sections_catalog=');
    }
    else {
        $first_tab_result= $this->getFirstTabResult($keys);
        if(empty($content_type)){
          if(isset($first_tab_result)){
            return new RedirectResponse($first_tab_result);
          }else{
            return new RedirectResponse('/' . $current_language .'/site-search?text='. urlencode($keys));
          }
        }else{
          return new RedirectResponse($content_type .'?text='. urlencode($keys));
        }
    }
  }

  private function  getFirstTabResult($keys){
    $vocabulary_id = 'buscador';
    $tabs = [];
    $first_tab_url = NULL;
    $current_view = NULL;

    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vocabulary_id);

    $request = \Drupal::service('request_stack')->getCurrentRequest();
    $query = $request->query->all();
    $text_param = isset($keys) ? $keys : '';

    foreach ($terms as $term) {
      $term_entity = Term::load($term->tid);
      $title = $term_entity->getName();

      $view_id = $term_entity->get('field_view_relacionada')->getString();
      $view = Views::getView($view_id);

      if ($view) {
        $view->setDisplay('buscador');
        $view->setExposedInput(['text' => $text_param]);
        $view->execute();
        $url = $view->getUrl();
        $count = $view->total_rows;

        if ($count > 0) {
          return $url->setOption('query', ['text' => $text_param])->toString();
        }
      }
    }
  }
}
