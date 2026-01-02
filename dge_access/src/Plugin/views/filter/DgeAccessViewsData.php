<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_access (datos.gob.es)".
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

namespace Drupal\dge_access\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filter content by organization for publicador role.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("dge_access_node_access")
 */
class DgeAccessViewsData extends FilterPluginBase {

   /**
   * {@inheritdoc}
   */
  public function adminSummary() {
    return $this->t('Filter by node access grants.');
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['value'] = ['default' => 'view'];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form['value'] = [
      '#type' => 'select',
      '#title' => $this->t('Node access'),
      '#description' => $this->t('Level of access to a node'),
      '#options' => [
        'view' => $this->t('View'),
        'update' => $this->t('Edit'),
        'delete' => $this->t('Delete'),
      ],
      '#default_value' => $this->options['value'] ?? 'view',
    ];
    $form['caution'] = [
      '#markup' => $this->t('Note: for Edit and Delete access, <strong>only node access grants</strong> are checked. Modules can override node access grants to allow or deny actions, so this check does not necessarily reflect whether the user can actually perform the Edit or Delete action.'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    if (!\Drupal::currentUser()->hasPermission('administer nodes') && \Drupal::moduleHandler()->moduleExists('node')) {
      $access = !empty($this->value) ? $this->value : 'view';
      $table = $this->ensureMyTable();
  
      $grants = node_access_grants($access, \Drupal::currentUser());
  
      if (!empty($grants)) {
        $conditions = [];
        $parameters = [];
        $index = 0;
  
        foreach ($grants as $realm => $gids) {
          foreach ($gids as $gid) {
            $conditions[] = "($table.gid = :gid_$index AND $table.realm = :realm_$index)";
            $parameters[":gid_$index"] = $gid;
            $parameters[":realm_$index"] = $realm;
            $index++;
          }
        }
  
        if (!empty($conditions)) {
          $this->query->addWhereExpression('AND', implode(' OR ', $conditions), $parameters);
        }
        
        $this->query->addWhereExpression('AND', "$table.grant_$access >= :grant", [':grant' => 1]);
      }
    }
  }

  /**
   * 
   * @return string
   * 
   */
  public function ensureMyTable() {
    return $this->query->ensureTable('node_access');
  }
}
