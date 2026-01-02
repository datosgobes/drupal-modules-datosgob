<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_user (datos.gob.es)".
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

namespace Drupal\dge_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;


class dgeUserHasRoleNameController extends ControllerBase {

  public function dgeUserHasRoleNameWrapper(Request $request) {

    $option = $request->attributes->get('role');
    $is_account = $request->attributes->get('account');
    $account = null;
    $roles = ROLES;

    if($is_account){
      $account = \Drupal::currentUser();
    }

    $role_name = $roles[$option];

    $has_role = dge_user_has_role_name($role_name, $account);

    if($has_role){
      $resultado = [
        '#markup' => 'Su rol es '.$role_name,
      ];
    }else{
      $resultado = [
        '#markup' => 'Su rol no es '.$role_name,
      ];
    }

    return $resultado;
  }

}
