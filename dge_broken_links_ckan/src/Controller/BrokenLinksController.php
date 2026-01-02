<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_broken_links_ckan (datos.gob.es)".
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

namespace Drupal\dge_broken_links_ckan\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use \Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Routing\TrustedRedirectResponse;

define('PUBLISHER', 'publicador');
define('REUSER', 'reutilizador');

class BrokenLinksController extends ControllerBase
{

    public function dge_broken_links_ckan_views()
    {
        $account = User::load(\Drupal::currentUser()->id());

        $is_publisher = dge_user_has_role_name(PUBLISHER, $account);

        $is_reuser = dge_user_has_role_name(REUSER, $account);

        $language = \Drupal::languageManager()->getCurrentLanguage();

        $path =  \Drupal::config('dge_ckan.config')->get('ckan_url');

        $url_object = Url::fromUri($path);
        $url_string = $url_object->toString();

        if($url_string) {
            $path .= '/'.$language->getId().'/report/broken-links';
        } else {
            $path = url($path.'/report/broken-links', array('absolute'=>true));
        }

        if($is_publisher || $is_reuser){
          $user_organization = dge_ckan_get_organization_name_from_user($account);
          $path .= '?organization='.$user_organization;
        }

        $check_path_url_obj = Url::fromUri($path);
        $check_path_url_string = $check_path_url_obj->toString();
        if(!$check_path_url_string) {
            $path = url($path, array('absolute'=>true));
        }

        return new TrustedRedirectResponse($path);
    }
}
