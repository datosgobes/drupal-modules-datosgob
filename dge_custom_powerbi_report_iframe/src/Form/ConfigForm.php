<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_custom_powerbi_report_iframe (datos.gob.es)".
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
namespace Drupal\dge_custom_powerbi_report_iframe\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dge_custom_powerbi_report_iframe.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dge_custom_powerbi_report_iframe_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dge_custom_powerbi_report_iframe.settings');

    $form['tenant_id'] = [
      '#title' => $this->t('Tenant ID'),
      '#default_value' => $config->get('tenant_id'),
      '#required' => TRUE,
      '#type' => 'textfield',
    ];

    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#required' => TRUE,
      '#default_value' => $config->get('client_id'),
      
    ];

    $form['client_secret'] = [
      '#required' => TRUE,
      '#title' => $this->t('Client Secret'),
      '#type' => 'textfield',
      '#default_value' => $config->get('client_secret'),
      
      
    ];

    $form['group_id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Workspace o Group Id'),
        '#default_value' => $config->get('group_id'),
        '#required' => TRUE,
      ];

    return parent::buildForm($form, $form_state);
  }

  

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dge_custom_powerbi_report_iframe.settings')
      ->set('tenant_id', $form_state->getValue('tenant_id'))
      ->set('client_id', $form_state->getValue('client_id'))
      ->set('client_secret', $form_state->getValue('client_secret'))
      ->set('group_id', $form_state->getValue('group_id'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
