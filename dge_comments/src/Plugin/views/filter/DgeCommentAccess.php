<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_comments (datos.gob.es)".
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

namespace Drupal\dge_comments\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\user\Entity\User;
use Drupal\Core\Database\Database;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\views\Plugin\ViewsHandlerManager;

/**
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("dge_access_comment_access")
 */
class DgeCommentAccess extends FilterPluginBase {

  /**
   *
   * @var \Drupal\views\Plugin\ViewsHandlerManager
   */
  protected ViewsHandlerManager $joinHandler;


  public function __construct(array $configuration, $plugin_id, $plugin_definition, ViewsHandlerManager $join_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->joinHandler = $join_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): DgeCommentAccess {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('plugin.manager.views.join')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
  
    if (\Drupal::currentUser()->hasPermission('administer comments')) {
      $account = \Drupal::currentUser();
      $user = User::load($account->id());
  
      if (!array_intersect(['aporta', 'administrator'], $user->getRoles())) {
        if ($user && $user->hasField('field_organizacion') && !$user->get('field_organizacion')->isEmpty()) {
          $organization_id = $user->get('field_organizacion')->target_id;
  
          $node_field_data_table = $this->getTableAlias($this->query, 'node_field_data');
          if (!$node_field_data_table) {
            $node_field_data_table = $this->createRelationToNodeFieldDataTable();
          }
  
          if ($this->isTableNeeded('node__field_organismo_responsable')) {
            $peticion_de_datos_table = $this->createRelationToOrganismoResponsableTable($node_field_data_table);
          } else {
            $peticion_de_datos_table = null;
          }
  
          if ($peticion_de_datos_table) {
            $this->query->addWhereExpression(0, "(
              ($node_field_data_table.type = 'peticion_de_datos' AND $peticion_de_datos_table.field_organismo_responsable_target_id = :org_id)
              OR
              ($node_field_data_table.type != 'peticion_de_datos' AND $node_field_data_table.uid IN (:uids[]))
            )", [
              ':org_id' => $organization_id,
              ':uids[]' => $this->getUsersByOrganization($organization_id),
            ]);
          } else {
            $this->query->addWhereExpression(0, "$node_field_data_table.uid IN (:uids[])", [
              ':uids[]' => $this->getUsersByOrganization($organization_id),
            ]);
          }
  
        } else {
          $node_field_data_table = $this->getTableAlias($this->query, 'node_field_data');
          if (!$node_field_data_table) {
            $node_field_data_table = $this->createRelationToNodeFieldDataTable();
          }
          $this->query->addWhereExpression(0, "$node_field_data_table.uid = :uid", [':uid' => $account->id()]);
        }
      }
    }
  }
  

  private function createRelationToOrganismoResponsableTable($node_field_data_table) {
    $join = $this->joinHandler->createInstance('standard', [
      'type' => 'LEFT',
      'table' => 'node__field_organismo_responsable',
      'field' => 'entity_id',
      'left_table' => $node_field_data_table,
      'left_field' => 'nid',
    ]);
  
    $this->query->addRelationship("node__field_organismo_responsable", $join, $node_field_data_table);
    
    return "node__field_organismo_responsable";
  }
  

  private function isTableNeeded($table_name) {
    return !$this->getTableAlias($this->query, $table_name);
  }
  
  
  public function getUsersByOrganization($organization_id) {
    $connection = Database::getConnection();

    $result = $connection->query("SELECT ufo.entity_id FROM user__field_organizacion ufo WHERE ufo.bundle = :type and ufo.field_organizacion_target_id = :organization_id", [
      ':type' => 'user',
      ':organization_id' => $organization_id,
    ]);

    return $result->fetchCol();
  }


  private function getTableAlias($query, $table_name) {
    $table_alias = $this->query->ensureTable($table_name);

    if (empty($table_alias)) {
      $table_alias = $this->getTableAliasFromTableQueue($query, $table_name);
    }
    if (empty($table_alias)) {
      $table_alias = $this->getTableAliasFromTables($query, $table_name);
    }
    if (empty($table_alias)) {
      $table_alias = $this->getTableAliasFromRelations($query, $table_name);
    }

    return $table_alias;
  }

  private function getTableAliasFromTableQueue($query, $table) {
    $table_alias = NULL;
    
    foreach ($query->getTableQueue() as $name => $table_data) {
      if (!empty($table_data['table']) && $table_data['table'] === $table) {
          $table_alias = $table_data['alias'] ?? $name;
      }
    }

    return $table_alias;
  }

  private function getTableAliasFromTables($query, $table, $table_link = NULL) {
    $table_alias = NULL;

    $selected_tables = $query->tables;
    if (!empty($table_link)) {
        $selected_tables = $query->tables[$table_link];
    }
    foreach ($selected_tables as $name => $table_data) {
      if ($table === $name && !(empty($table_data['alias']) && empty($table_data['table']))) {
        $table_alias = $table_data['alias'] ?? $table_data['table'];
      }
    }

    return $table_alias;
  }


  private function getTableAliasFromRelations($query, $table) {
    $table_alias = NULL;

    foreach ($query->relationships as $relationship => $rel_data) {
      if ($rel_data['table'] === $table && !empty($rel_data['alias'])) {
        $table_alias = $rel_data['alias'];
      } elseif ($rel_data['table'] === $table) {
        $table_alias = $this->getTableAliasFromTables($query, $relationship, $rel_data['link'] ?? NULL);
      }
    }

    return $table_alias;
  }


  private function createRelationToNodeFieldDataTable() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $comment_table = $this->getTableAlias($this->query, 'comment_field_data');
    $join = $this->joinHandler->createInstance('standard', [
      'type' => 'LEFT',
      'table' => 'node_field_data',
      'field' => 'nid',
      'left_table' => "$comment_table",
      'left_field' => 'entity_id',
      'extra' => [
              [
                'table' => "$comment_table",
                'field' => 'entity_type',
                'value' =>  'node',
              ],
              [
                'table' => "node_field_data",
                'field' => 'langcode',
                'value' =>  $language,
              ],
       ],
    ]);
    $this->query->addRelationship("node_field_data", $join, "$comment_table");

    return "node_field_data";
  }

}
