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
      $field_config = FieldConfig::loadByName(
        $entity->getEntityTypeId(),
        $entity->bundle(),
        $field_name
      );
      $settings = $field_config?->getSettings();
      $per_page = !empty($settings['per_page']) ? $settings['per_page'] : 50;
    }

    $nid = $entity->id();

    
    $total_query = \Drupal::entityQuery('comment')
      ->accessCheck(TRUE)
      ->condition('entity_id', $nid)
      ->condition('entity_type', $entity->getEntityTypeId())
      ->condition('field_name', $field_name)
      ->condition('pid', NULL, 'IS NULL');

    $total = $total_query->count()->execute();

   
    $pager = \Drupal::service('pager.manager')
      ->createPager($total, $per_page);

    
    $ids_query = \Drupal::entityQuery('comment')
      ->accessCheck(TRUE)
      ->condition('entity_id', $nid)
      ->condition('entity_type', $entity->getEntityTypeId())
      ->condition('field_name', $field_name)
      ->condition('pid', NULL, 'IS NULL')
      ->sort('created', 'DESC')
      ->range($pager->getCurrentPage() * $per_page, $per_page);

    $parent_ids = $ids_query->execute();

    $parents = array_filter(
      Comment::loadMultiple($parent_ids),
      fn($comment) => $comment->access('view')
    );

    $all_comments = $parents;
    foreach ($parents as $parent) {
      $children = self::loadChildrenRecursively($parent);
      foreach ($children as $child) {
          $all_comments[$child->id()] = $child;
      }
    }

    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('comment');
    $comments_to_render = array_values($all_comments);
    $rendered_comments = $view_builder->viewMultiple($comments_to_render);

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

    $children_ids = \Drupal::entityQuery('comment')
      ->accessCheck(TRUE)
      ->condition('pid', $comment->id())
      ->sort('created', 'ASC')
      ->execute();

    if (empty($children_ids)) {
      return [];
    }

    $children = Comment::loadMultiple($children_ids);

    foreach ($children as $child) {
      if (!$child->access('view')) {
        continue;
      }

      $children_comments[$child->id()] = $child;
      $grandchildren = self::loadChildrenRecursively($child);
      foreach ($grandchildren as $grandchild) {
        $children_comments[$grandchild->id()] = $grandchild;
      }
    }

    return $children_comments;
  }





}