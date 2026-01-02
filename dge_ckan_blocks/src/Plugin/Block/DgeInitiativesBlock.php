<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_ckan_blocks (datos.gob.es)".
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

namespace Drupal\dge_ckan_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

/**
 *
 * @Block(
 *   id = "dge_initiatives_block",
 *   admin_label = @Translation("DGE CKAN Initiatives"),
 *   category = @Translation("Custom")
 * )
 */
class DgeInitiativesBlock extends BlockBase {
  public function getCacheMaxAge() {
      return 0;
  }
  /**
   * {@inheritdoc}
   */
  public function build() {
    $total = 0;
    $map_link = $this->configuration['initiative_map_url'] ?? '#';

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'iniciativa')
      ->condition('status', 1)
      ->condition('langcode', 'es')
      ->accessCheck(FALSE);
    $total = $query->count()->execute();

    $initiatives = [];
    $taxonomy = Vocabulary::load('administrative_level');
    if ($taxonomy) {
      $levels = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree($taxonomy->id());
      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

      foreach ($levels as $level) {
        $query = \Drupal::entityQuery('node')
          ->condition('type', 'iniciativa')
          ->condition('status', 1)
          ->condition('langcode', 'es')
          ->condition('field_initiative_admin_level.target_id', $level->tid)
          ->accessCheck(FALSE);
        $num = $query->count()->execute();

        if ($num > 0) {

          $term = Term::load($level->tid);
          $title = $term ? $term->getTranslation($language)->getName() : $level->name;
          
          $initiatives[$level->tid] = [
            'title' => $title,
            'total' => $num,
          ];
        }
      }
    }

    return [
      '#theme' => 'dge_ckan_blocks_initiatives_review',
      '#total' => $total,
      '#initiatives' => $initiatives,
      '#map_link' => ($map_link != '#') ? Url::fromUri('internal:' . $map_link)->toString() : '',
      
      
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'initiative_map_url' => '#',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['initiative_map_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Initiative Map URL'),
      '#description' => $this->t('URL of the map to link to.'),
      '#default_value' => $this->configuration['initiative_map_url'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['initiative_map_url'] = $form_state->getValue('initiative_map_url');
  }

}
