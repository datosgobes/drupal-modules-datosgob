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
 * Contains \Drupal\dge_migrate\Plugin\migrate\source\Comments.
 */
 
namespace Drupal\dge_migrate\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
 
/**
 * Drupal 7 Comments source plugin
 *
 * @MigrateSource(
 *   id = "dge_migrate_comments",
 *   source_module = "dge_migrate"
 * )
 */
class Comments extends SqlBase {
 
  /**
   * {@inheritdoc}
   */
  public function query() {
    $node_type = $this->configuration['node_type'];
    $subquery = $this->select('node', 'n')
                        ->condition('n.type', $node_type, 'IN')
                        ->fields('n', ['nid']);

    $query = $this->select('comment', 'c')
      ->condition('c.nid', $subquery, 'IN')
      ->condition('c.status', 1)
      ->fields('c', array_keys($this->baseCommentFields()));
    $query->orderBy('cid');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $comment_fields = $this->baseCommentFields();
    $comment_fields['body_format'] = $this->t('Format of body');
    $comment_fields['body_value'] = $this->t('Full text of body');
    return $comment_fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['cid']['type'] = 'integer';
    $ids['cid']['alias'] = 'c';
    return $ids;
  }
 
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $cid = $row->getSourceProperty('cid');

    // Body
    $result = $this->getDatabase()->query('
      SELECT
        fld.comment_body_value,
        fld.comment_body_format
      FROM
        {field_data_comment_body} fld
      WHERE
        fld.entity_id = :cid
    ', array(':cid' => $cid));
    $description = [];
    foreach ($result as $record) {
      $description[] = [
        'value' => $record->comment_body_value,
        'format' => ($record->comment_body_format == 'filtered_html' || 'code') ? 'full_html' : $record->comment_body_format,
      ];
    }
    $row->setSourceProperty('comment_body', $description);

    $log_message = '';
    $log_message .= json_encode($row->getSourceProperty('cid')) . ';';
    $log_message .= json_encode($row->getSourceProperty('pid')) . ';';
    $log_message .= json_encode($row->getSourceProperty('nid')) . ';';
    $log_message .= json_encode($row->getSourceProperty('uid')) . ';';
    $log_message .= json_encode($row->getSourceProperty('subject')) . ';';
    $log_message .= json_encode($row->getSourceProperty('created')) . ';';
    $log_message .= json_encode($row->getSourceProperty('changed')) . ';';
    $log_message .= json_encode($row->getSourceProperty('status')) . ';';
    $log_message .= json_encode($row->getSourceProperty('thread')) . ';';
    $log_message .= json_encode($row->getSourceProperty('name')) . ';';
    $log_message .= json_encode($row->getSourceProperty('mail')) . ';';
    $log_message .= json_encode($row->getSourceProperty('homepage')) . ';';
    $log_message .= json_encode($row->getSourceProperty('language')) . ';';
    $log_message .= json_encode($row->getSourceProperty('comment_body')) . ';';

    \Drupal::logger('dge_migrate')->warning($log_message);
 
    return parent::prepareRow($row);
  }
 
 
  /**
   * {@inheritdoc}
   */
  public function bundleMigrationRequired() {
    return TRUE;
  }
 
  /**
   * {@inheritdoc}
   */
  public function entityTypeId() {
    return 'comment';
  }

  /**
   * 
   *
   * @return array
   *   
   */
  protected function baseCommentFields() {
    $comment_fields = array(
      'cid' => $this->t('Commentn ID'),
      'pid' => $this->t('The comment cid to which the comment is a reply'),
      'nid' => $this->t('Node ID to which the comment is a reply'),
      'uid' => $this->t('User who authored the comment'),
      'subject' => $this->t('The comment title'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
      'status' => $this->t('Published'),
      'thread' => $this->t('The vancode representation'),
      'name' => $this->t('Comment author name'),
      'mail' => $this->t('Comment author mail'),
      'homepage' => $this->t('Comment author home page address'),
      'language' => $this->t('Language (fr, en, ...)'),
    );
    return $comment_fields;
  }
 
}
