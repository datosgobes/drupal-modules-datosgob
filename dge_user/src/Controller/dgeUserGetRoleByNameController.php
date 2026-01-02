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

define('ROLES', [
      1 => 'ROLES_1',
      2 => 'ROLES_2',
      3 => 'ROLES_3',
      4 => 'ROLES_4',
      5 => 'ROLES_5',
      6 => 'ROLES_6',
      7 => 'ROLES_7',
      8 => 'ROLES_8',
  ]);

class dgeUserGetRoleByNameController extends ControllerBase {

  public function dgeUserGetRoleByNameWrapper(Request $request){

    $option = $request->attributes->get('role');
    $roles = ROLES;

    $role_name = $roles[$option];

    $role_id = dge_user_get_role_by_name($role_name);

    return [
      '#markup' => 'El nombre de rol indicado es '.$role_name.' y su id asociado es: '.$role_id,
    ];
  }

}
