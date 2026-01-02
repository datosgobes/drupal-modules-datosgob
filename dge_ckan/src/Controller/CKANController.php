<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_ckan (datos.gob.es)".
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

namespace Drupal\dge_ckan\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Response;
use DgeCkanClient;
use Drupal\Core\Routing\TrustedRedirectResponse;


include_once (DRUPAL_ROOT . "/modules/custom/dge_ckan/libraries/dge_ckan_php_client/DgeCkanClient.php");

class CKANController extends ControllerBase
{

    public function dge_ckan_page_test()
    {
        global $conf;

        $ckanConfig = \Drupal::config('dge_ckan.config');

        $library = class_exists('DgeCkanClient');

        if (! $library) {
            \Drupal::messenger()->addMessage(t('DGE CKAN PHP Client is not installed'), 'error');
        }
        $apiUrl = $ckanConfig->get('ckan_host');
        $apiKey = $ckanConfig->get('ckan_api_key');
        $ckan = new DgeCkanClient($apiUrl, $apiKey, $conf);

        $ckanResults = $ckan->package_search('organization:irs-gov');

        $ckanResults = json_decode($ckanResults, true);
        $output = '';
        if ($ckanResults && $ckanResults['success'] == 1) {
            \Drupal::messenger()->addMessage(t('CKAN Connexion is OK'));
            $output = t('RAW result') . ': ' . print_r($ckanResults, true);
        } else {
            \Drupal::messenger()->addMessage(t('CKAN Connexion error'), 'error');
        }
        if (isset($conf['proxy_server']) && $conf['proxy_server'] != '') {
            $output .= '<br />' . t('Proxy server is configurated. IP !ip', array(
                '!ip' => $conf['proxy_server']
            ));
        }

        $exampleConnexionURL = t('CKAN example connexion URL');
        $curlRequest = print_r($ckan->getCurlRequest(), true);
        $response = $output . '<br />' . $exampleConnexionURL . ': ' . $curlRequest;

        return [
            '#markup' => $response
        ];
    }

    public function dge_ckan_harvest_page()
    {
        $language = \Drupal::languageManager()->getCurrentLanguage();

        $path =  \Drupal::config('dge_ckan.config')->get('ckan_url');

        $url_object = Url::fromUri($path);
        $url_string = $url_object->toString();


        if($url_string) {
            $path .= '/'.$language->getId().'/harvest';
        } else {
            $path = url($path.'/harvest', array('absolute'=>true));
        }

        $check_path_url_obj = Url::fromUri($path);
        $check_path_url_string = $check_path_url_obj->toString();
        if(!$check_path_url_string) {
            $path = url($path, array('absolute'=>true));
        }

        return new TrustedRedirectResponse($path);
    }

    public function dge_ckan_catalogo_page(){

        $language = \Drupal::languageManager()->getCurrentLanguage();

        $path =  \Drupal::config('dge_ckan.config')->get('ckan_url');
        $url_object = Url::fromUri($path);
        $url_string = $url_object->toString();


        if($url_string) {
            $path .= '/'.$language->getId().'/dataset';
        } else {
            $path = url($path.'/dataset', array('absolute'=>true));
        }

        return new TrustedRedirectResponse($path);
    }

    public function dge_ckan_datasets_page()
    {

        $user= \Drupal::currentUser();
        $language = \Drupal::languageManager()->getCurrentLanguage();

        $path =  \Drupal::config('dge_ckan.config')->get('ckan_url');

        $url_object = Url::fromUri($path);
        $url_string = $url_object->toString();

        if($url_string) {
            $path .= '/'.$language->getId().'/organization';
        } else {
            $path = url($path.'/organization', array('absolute'=>true));
        }

        if (in_array('organizacion', $user->getRoles())) {
            $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
            $organization = dge_ckan_get_organization_name_from_user($account);
            if(!empty($organization)) {
                $path .= '/'.$organization;
            }
        }
        return new TrustedRedirectResponse($path);
    }
}