<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_migrate (datos.gob.es)".
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
 
/**
 * @file
 * Contains \Drupal\dge_migrate\Plugin\migrate\process\RoleMap.
 */
 
namespace Drupal\dge_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;

/**
 * Provides a 'RoleMap' migrate process plugin. This plugin runs for each rol.
 *
 * @MigrateProcessPlugin(
 *   id = "dge_migrate_role_map"
 * )
 * 
 * @code
 * field_roles:
 *   plugin: dge_migrate_role_map
 *   source: roles
 * @endcode
 * 
 */
class RoleMap extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $role_map = [
        'site manager' => 'administrator',
        'editor' => 'content_editor',
        'super editor' => 'content_editor',
        'organization' => 'publicador',
        'anonymous user' => 'anonymous',
        'authenticated user' => 'authenticated',
        'administrator' => 'administrator'
    ];
    
    return isset($role_map[$value]) ? $role_map[$value] : 'anonymous';
    
  }
}
