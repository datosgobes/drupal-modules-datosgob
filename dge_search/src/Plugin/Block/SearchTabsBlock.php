<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_search (datos.gob.es)".
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

namespace Drupal\dge_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\views\Views;
use Drupal\Core\Url;
use Drupal\Core\Render\Markup;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\dge_search\Service\DGESearchService;

/**
 *
 * @Block(
 *   id = "search_tabs_block",
 *   admin_label = @Translation("Search Tabs Block"),
 * )
 */
class SearchTabsBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
      return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $miServicio = \Drupal::service('dge_search.services_api_sections');
    $response = $miServicio->getSectionsByTabs();
    $result = json_decode($response->getContent(), TRUE);
    if (isset($result['#markup'])) {
      return [ '#markup' => Markup::create($result['#markup'])];
    }
  }
}