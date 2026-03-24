<?php

/**
  * Copyright (C) 2026 Entidad Pública Empresarial Red.es
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

namespace Drupal\dge_comments;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Render\Renderer;
use Drupal\field\Entity\FieldConfig;

use Drupal\Core\Session\AccountInterface;


class CommentHelper {

  public static function loadPaginatedParentComments(EntityInterface $entity, $field_name, $per_page = NULL) {
    
    if ($per_page === NULL) {
      $field_config = FieldConfig::loadByName($entity->getEntityTypeId(), $entity->bundle(), $field_name);
      if ($field_config) {
        $settings = $field_config->getSettings();
        if (!empty($settings['per_page'])) {
          $per_page = $settings['per_page'];
        }
        else {
          $per_page = 50; 
        }
      }
    }
    
    $nid = $entity->id();
    $user = \Drupal::currentUser();
    $total_query = \Drupal::entityQuery('comment')
    ->accessCheck(TRUE)
    ->condition('entity_id', $nid)
    ->condition('entity_type', $entity->getEntityTypeId())
    ->condition('field_name', $field_name)
    ->condition('pid', NULL, 'IS NULL'); 


    if (!$user->hasPermission('view any unpublished comment')) {
        $total_query->condition('status', 1);
    }

    $total = $total_query->count()->execute();

    $pager = \Drupal::service('pager.manager')->createPager($total, $per_page);

    $ids_query = \Drupal::entityQuery('comment')
    ->accessCheck(TRUE)
    ->condition('entity_id', $nid)
    ->condition('entity_type', $entity->getEntityTypeId())
    ->condition('field_name', $field_name)
    ->condition('pid', NULL, 'IS NULL')
    ->sort('created', 'DESC')
    ->range($pager->getCurrentPage() * $per_page, $per_page);


    if (!$user->hasPermission('view any unpublished comment')) {
        $ids_query->condition('status', 1);
    }

    $parent_ids = $ids_query->execute();

    $parents = Comment::loadMultiple($parent_ids);

    $all_comments = $parents;

    foreach ($parents as $parent) {
      $children = self::loadChildrenRecursively($parent);
      foreach ($children as $child) {
        $all_comments[$child->id()] = $child;
      }
    }

    $rendered_comments = [];
    foreach ($all_comments as $comment) {
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('comment');
      $rendered = $view_builder->view($comment);
      $rendered['#comment'] = $comment;
      $rendered_comments[] = $rendered;
    }

    return [
      'rendered' => $rendered_comments,
      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }





  /**
   * 
   * 
   * @param \Drupal\comment\Entity\Comment $comment
   * @return \Drupal\comment\Entity\Comment[]
   */
  protected static function loadChildrenRecursively(Comment $comment) {
    $children_comments = [];
    $user = \Drupal::currentUser();

    $children_query = \Drupal::entityQuery('comment')
      ->accessCheck(TRUE)
      ->condition('pid', $comment->id())
      ->sort('created', 'DESC');

    if (!$user->hasPermission('view any unpublished comment')) {
     $children_query->condition('status', 1);
    }  

    $children_ids = $children_query->execute();
    $children = Comment::loadMultiple($children_ids);

    foreach ($children as $child) {
      $children_comments[$child->id()] = $child;
      $grandchildren = self::loadChildrenRecursively($child);
      foreach ($grandchildren as $grandchild) {
        $children_comments[$grandchild->id()] = $grandchild;
      }
    }

    return $children_comments;
  }





}