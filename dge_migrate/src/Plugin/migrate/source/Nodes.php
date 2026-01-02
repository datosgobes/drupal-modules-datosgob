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
 * Contains \Drupal\dge_migrate\Plugin\migrate\source\Nodes.
 */
 
namespace Drupal\dge_migrate\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
 
/**
 * Drupal 7 New node source plugin
 *
 * @MigrateSource(
 *   id = "dge_migrate_nodes"
 * )
 */
class Nodes extends BaseNodes {
 
  /**
   * {@inheritdoc}
   */
  public function query() {
    $node_type = $this->configuration['node_type'];
    
    $query = $this->select('node', 'n')
      ->condition('n.type', $node_type, 'IN')
      ->condition('n.status', 1)
      ->condition('n.language', 'es')
      ->fields('n', array(
      'nid',
      'vid',
      'type',
      'language',
      'title',
      'uid',
      'status',
      'created',
      'changed',
      'promote',
      'sticky',
      'tnid',
    ));
    
    $query->orderBy('nid');
    return $query;
  }
}