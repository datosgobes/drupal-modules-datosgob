<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_data_request (datos.gob.es)".
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

namespace Drupal\dge_data_request\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class DgeDataRequestConfigForm extends ConfigFormBase {

    public function getFormId() {
        return 'dge_data_request_config_form';
    }

    protected function getEditableConfigNames() {
        return [
            'dge_data_request.config'
        ];
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = \Drupal::config('dge_data_request.config');
        $enabled = $config->get('dge_data_request_enabled');
        $email_subject = $config->get('dge_data_request_email_subject');
        $email_content = $config->get('dge_data_request_email_content');
        $published_org_enabled = $config->get('dge_data_request_published_org_enabled');
        $published_org_email_subject = $config->get('dge_data_request_published_org_email_subject');
        $published_org_email_content = $config->get('dge_data_request_published_org_email_content');
        $published_user_enabled = $config->get('dge_data_request_published_user_enabled');
        $published_user_email_subject = $config->get('dge_data_request_published_user_email_subject');
        $published_user_email_content = $config->get('dge_data_request_published_user_email_content');
        $request_published_enabled = $config->get('dge_data_request_published_email_enabled');
        $leave_the_request_email_subject = $config->get('dge_data_request_published_email_subject');
        $leave_the_request_email_content = $config->get('dge_data_request_published_email_content');

        $form = array();

        
        $form['dge_data_request'] = [
            '#type'  => 'fieldset',
            '#title' => t('Notification email for data request assignment'),
        ];

        $form['dge_data_request']['dge_data_request_enabled'] = [
        '#type' => 'checkbox',
        '#title' => t('Enable this notification'),
        '#default_value' => $enabled,
        ];
        
        $form['dge_data_request']['dge_data_request_email_subject'] = [
            '#type'          => 'textfield',
            '#title'         => t('Subject'),
            '#default_value' => $email_subject,
        ];
        $form['dge_data_request']['dge_data_request_email_content'] = [
            '#type'          => 'textarea',
            '#title'         => t('Body'),
            '#required'      => true,
            '#default_value' =>  $email_content,
        ];

        $form['dge_data_request_published_org'] = [
            '#type'  => 'fieldset',
            '#title' => t('Notification email for data request publication to Organization(s)'),
        ];

        $form['dge_data_request_published_org']['dge_data_request_published_org_enabled'] = [
        '#type' => 'checkbox',
        '#title' => t('Enable this notification'),
        '#default_value' => $published_org_enabled,
        ];

        $form['dge_data_request_published_org']['dge_data_request_published_org_email_subject'] = [
            '#type'          => 'textfield',
            '#title'         => t('Subject'),
            '#default_value' => $published_org_email_subject,
        ];
        $form['dge_data_request_published_org']['dge_data_request_published_org_email_content'] = [
            '#type'          => 'textarea',
            '#title'         => t('Body'),
            '#required'      => true,
            '#default_value' =>  $published_org_email_content,
        ];

        $form['dge_data_request_published_user'] = [
            '#type'  => 'fieldset',
            '#title' => t('Notification email for data request publication to the user who submitted it'),
        ];

         $form['dge_data_request_published_user']['dge_data_request_published_user_enabled'] = [
        '#type' => 'checkbox',
        '#title' => t('Enable this notification'),
        '#default_value' => $published_user_enabled,
        ];

        $form['dge_data_request_published_user']['dge_data_request_published_user_email_subject'] = [
            '#type'          => 'textfield',
            '#title'         => t('Subject'),
            '#default_value' => $published_user_email_subject,
        ];
        $form['dge_data_request_published_user']['dge_data_request_published_user_email_content'] = [
            '#type'          => 'textarea',
            '#title'         => t('Body'),
            '#required'      => true,
            '#default_value' =>  $published_user_email_content,
        ];

        $form['dge_data_request_published_email'] = [
            '#type' => 'fieldset',
            '#title' => t('Leave the request'),
          ];

        $form['dge_data_request_published_email']['dge_data_request_published_email_enabled'] = [
        '#type' => 'checkbox',
        '#title' => t('Enable this notification'),
        '#default_value' => $request_published_enabled,
        ];

          $form['dge_data_request_published_email']['dge_data_request_published_email_subject'] = [
            '#type' => 'textfield',
            '#title' => t('Subject'),
            '#default_value' => $leave_the_request_email_subject,

          ];
          $form['dge_data_request_published_email']['dge_data_request_published_email_content'] = [
            '#type' => 'textarea',
            '#title' => t('Body'),
            '#required'      => true,
            '#default_value' => $leave_the_request_email_content,
           
          ];

        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        parent::submitForm($form, $form_state);

        $this->config('dge_data_request.config')
          ->set('dge_data_request_enabled', $form_state->getValue('dge_data_request_enabled'))
          ->set('dge_data_request_email_subject', $form_state->getValue('dge_data_request_email_subject'))
          ->set('dge_data_request_email_content', $form_state->getValue('dge_data_request_email_content'))
          ->set('dge_data_request_published_org_enabled', $form_state->getValue('dge_data_request_published_org_enabled'))
          ->set('dge_data_request_published_org_email_subject', $form_state->getValue('dge_data_request_published_org_email_subject'))
          ->set('dge_data_request_published_org_email_content', $form_state->getValue('dge_data_request_published_org_email_content'))
          ->set('dge_data_request_published_user_enabled', $form_state->getValue('dge_data_request_published_user_enabled'))
          ->set('dge_data_request_published_user_email_subject', $form_state->getValue('dge_data_request_published_user_email_subject'))
          ->set('dge_data_request_published_user_email_content', $form_state->getValue('dge_data_request_published_user_email_content'))
          ->set('dge_data_request_published_email_enabled', $form_state->getValue('dge_data_request_published_email_enabled'))
          ->set('dge_data_request_published_email_subject', $form_state->getValue('dge_data_request_published_email_subject'))
          ->set('dge_data_request_published_email_content', $form_state->getValue('dge_data_request_published_email_content'))
          ->save();
    }
}
