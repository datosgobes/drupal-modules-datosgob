<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_tfa (datos.gob.es)".
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

namespace Drupal\dge_tfa\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class DgeTfaSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dge_tfa.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dge_tfa_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dge_tfa.settings');

    $form['dge-tfa-title'] = [
      '#type' => 'textfield',
      '#title' => t("TFA page title"),
      '#description' => t('Title of the TFA page'),
      '#default_value' =>  $config->get('dge-tfa-title') ?? '',
    ];

    $dge_tfa_description = $config->get('dge-tfa-description');
    $form['dge-tfa-description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('General description'),
      '#description' => $this->t('Enter a descriptive text before the form.'),
      '#default_value' => $dge_tfa_description['value'] ?? '',
      '#format' => $dge_tfa_description['format'] ?? 'basic_html',
    ];

    $dge_tfa_step1 = $config->get('dge-tfa-step1');
    $form['dge-tfa-step1'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Step 1 description'),
      '#description' => $this->t('Enter a descriptive text before the "Send" button for the Email TFA Login form.'),
      '#default_value' => $dge_tfa_step1['value'] ?? '',
      '#format' => $dge_tfa_step1['format'] ?? 'basic_html',
    ];

    $dge_tfa_step2 = $config->get('dge-tfa-step2');
    $form['dge-tfa-step2'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Step 2 description'),
      '#description' => $this->t('Enter a descriptive text before the "Authentication code" text input for the Email TFA Login form.'),
      '#default_value' => $dge_tfa_step2['value'] ?? '',
      '#format' => $dge_tfa_step2['format'] ?? 'basic_html',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dge_tfa.settings')
      ->set('dge-tfa-title', $form_state->getValue('dge-tfa-title'))
      ->save();

    $this->config('dge_tfa.settings')
      ->set('dge-tfa-description', $form_state->getValue('dge-tfa-description'))
      ->save();

    $this->config('dge_tfa.settings')
      ->set('dge-tfa-step1', $form_state->getValue('dge-tfa-step1'))
      ->save();

    $this->config('dge_tfa.settings')
      ->set('dge-tfa-step2', $form_state->getValue('dge-tfa-step2'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
