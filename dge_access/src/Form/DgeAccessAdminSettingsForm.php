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

namespace Drupal\dge_access\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * This class provides an admin settings form for configuring access control in your module. 
 * Specifically, it allows site administrators to choose which content types will be restricted based on organization.
 */
class DgeAccessAdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dge_access_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dge_access.settings'];
  }

  /**
   * Builds the form with a list of node types (content types) that an admin can enable or disable for organization-based access control
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $node_types = [];
    foreach ($types as $type) {
      $node_types[$type->id()] = $type->label();
    }

    // Create a form field to select node types.
    $form['dge_access_content_types'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => $this->t('Control organization access by node type'),
      '#options' => $node_types,
      '#description' => $this->t('This content types are enabled for organization access control'),
      '#default_value' => $this->config('dge_access.settings')->get('dge_access_content_types'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Saves the selected node types to configuration once the form is submitted.
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dge_access.settings')
      ->set('dge_access_content_types', $form_state->getValue('dge_access_content_types'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
