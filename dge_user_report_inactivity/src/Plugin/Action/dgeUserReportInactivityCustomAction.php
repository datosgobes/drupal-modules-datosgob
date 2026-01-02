<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_user_report_inactivity (datos.gob.es)".
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

namespace Drupal\dge_user_report_inactivity\Plugin\Action;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Mail\MailManager;
use Drupal\Component\Render\PlainTextOutput;


/**
 *
 * @Action(
 *   id = "dge_user_report_inactivity.action.report_inactivity_action",
 *   label = @Translation("Notificar inactividad"),
 *   type = "user"
 * )
 */
class DgeUserReportInactivityCustomAction extends ViewsBulkOperationsActionBase implements PluginFormInterface  {

    /**
    *
    * {@inheritdoc}
    */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

      return $form;
  }

 /**
  * 
  * {@inheritdoc}
  */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
      $this->configuration['new_tags'] = $form_state->getValue('new_tags');
  }

  /**
   * 
   * {@inheritdoc}
   */
  public function execute($account = NULL) {

    $mail_config = \Drupal::getContainer()->get('config.factory')->getEditable('dge_user_report_inactivity.email');
    $token_service = \Drupal::token();
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'dge_user_report_inactivity';
    $key = 'dge_user_report_inactivity_email';
    $to = $account->getEmail();
    $variables = ['user' => $account];
    $langcode = $account->getPreferredLangcode();
    $params['subject'] = $mail_config->get('dge_user_report_inactivity_email.subject');
    $params['message'] = $mail_config->get('dge_user_report_inactivity_email.body');
    $token_options = ['langcode' => $langcode,  'callback' => 'user_mail_tokens', 'clear' => TRUE];
    $params['subject'] = PlainTextOutput::renderFromHtml($token_service->replace($mail_config->get('dge_user_report_inactivity_email.subject'), $variables, $token_options));
    $params['message'] = $token_service->replace($mail_config->get('dge_user_report_inactivity_email.body'), $variables, $token_options);
    $send = TRUE;

    $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
  }

  /**
   * 
   *    * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    return AccessResult::allowedIf(TRUE);
  }
}
