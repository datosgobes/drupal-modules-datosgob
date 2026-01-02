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

namespace Drupal\dge_access\Access;

use Drupal\node\NodeAccessControlHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\NodeInterface;
use Drupal\Core\Database\Database;

/**
 * This class controls the node access based on user roles and organizations. 
 * It's designed to manage who can view specific content (nodes) depending on whether they belong to the same organization as the author of the content.
 *
 */
class DgeNodeAccessControlHandler implements NodeAccessControlHandlerInterface {

  /**
   * This is the key method where the node access logic is applied. 
   * It checks if the operation is view, then compares the organization of the current user with that of the node's author
   */
  public function access(NodeInterface $node, $operation, AccountInterface $account) {
    if ($operation !== 'view') {
      return AccessResult::neutral();
    }
  
    if ($account->hasRole('aporta') || $account->hasRole('administrator')) {
      return AccessResult::allowed();
    }
  
    if ($account->hasRole('publicador') || $account->hasRole('reutilizador')) {
      $user_organization = $this->getUserOrganization($account);
  
      if ($node->getType() === 'peticion_de_datos') {
        $author_organization = $this->getNodeResponsibleOrganization($node);
      } else {
        $author_organization = $this->getNodeAuthorOrganization($node);
      }
  
      if ($user_organization === $author_organization) {
        return AccessResult::allowed();
      } else {
        return AccessResult::forbidden();
      }
    }
  
    return AccessResult::neutral();
  }
  
  /**
   * Get the organization of the current user.
   */
  protected function getUserOrganization(AccountInterface $account) {
    
    if ($account->hasField('field_organizacion')) {
      $user_organization_field = $account->get('field_organizacion')->target_id;
      return $user_organization_field;
    }
    return NULL;
  }

  /**
   * Get the organization of the node's author (user who created the node).
   */
  protected function getNodeAuthorOrganization(NodeInterface $node) {
    // Load the author (node's uid) and get their organization.
    $author = \Drupal::entityTypeManager()->getStorage('user')->load($node->getOwnerId());
    if ($author && $author->hasField('field_organizacion')) {
      $author_organization_field = $author->get('field_organizacion')->target_id;
      return $author_organization_field;
    }
    return NULL;
  }

  /**
   * Get the responsible organization for the node.
   */
  protected function getNodeResponsibleOrganization(NodeInterface $node) {
    $connection = Database::getConnection();
    $query = $connection->select('node__field_organismo_responsable', 'nfr')
      ->fields('nfr', ['field_organismo_responsable_target_id'])
      ->condition('nfr.entity_id', $node->id())
      ->execute();
    $result = $query->fetchAssoc();

    if ($result) {
      return $result['field_organismo_responsable_target_id'];
    }
    return NULL;
  }

  public function grants(AccountInterface $account, $op) {
    // Custom grant logic for users.
  }

  public function acquire(EntityInterface $node) {
    // Custom logic to determine access by organization, etc.
  }
}
