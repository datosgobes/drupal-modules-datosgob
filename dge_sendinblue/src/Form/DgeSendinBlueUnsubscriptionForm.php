<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_sendinblue (datos.gob.es)".
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
namespace Drupal\dge_sendinblue\Form;

include_once (DRUPAL_ROOT . "/modules/custom/dge_sendinblue/libraries/dge_sendinblue_php_client/DgeSendInBlueClient.php");

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use DgeSendInBlueClient;
use Exception;

class DgeSendinBlueUnsubscriptionForm extends FormBase
{

    /**
     *
     * {@inheritdoc}
     * @see \Drupal\Core\Form\FormInterface::getFormId()
     */
    public function getFormId()
    {
        return 'dge_sendinblue_unsubscription_form';
    }


    /**
     *
     * {@inheritDoc}
     * @see \Drupal\Core\Form\FormBase::buildForm()
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#attached']['library'][] = 'dge_sendinblue/dge_sendinblue.styling';
        $form['#attributes'] = array('class' => 'dge-sendinblue-form');
        $form['#prefix'] = '<div id="sib-container">'
                            . '<p class="page-description">'
                            . $this->t('From this page you can unsubscribe from the list of recipients of our newsletter.')
                            . '</p>';

        $form['#suffix'] = '</div>';

        $form['title'] = array(
            '#type' => 'markup',
            '#markup' => '<h2>' . $this->t('Subscription cancellation') . '</h2>',
            '#prefix' => '<div class="sib-form-block">',
            '#suffix' => '</div>',
        );

        $form['email'] =  array(
            '#title' => $this->t('Enter your email address to unsubscribe'),
            '#type' => 'email',
            '#required' => TRUE,
            '#description' => $this->t('Enter your email address. Ex: abc@xyz.com'),
            '#attributes' => array('class' => array('test-class')),
            '#prefix' => '<div class="sib-form-block">',
            '#suffix' => '</div>',
        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Cancel the subscription'),
            '#prefix' => '<div class="sib-form-block">',
            '#suffix' => '</div>',
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $library = class_exists('DgeSendInBlueClient');

        if (! $library) {
            \Drupal::messenger()->addMessage(t('DGE SENDINBLUE PHP Client is not installed'), 'error');
        }

        $email = $form_state->getValue('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            \Drupal::messenger()->addError(t('Enter a valid email. Ex: abc@xyz.com'));
        } else {
            try {
                $api_key = \Drupal::config('dge_sendinblue.config')->get('dge_sendinblue_api_key');
                $list_id = \Drupal::config('dge_sendinblue.config')->get('dge_sendinblue_list_id');

                $sendinblue = new DgeSendInBlueClient($api_key, $list_id);
                $contacts = $sendinblue->getContactsFromList();

                if (!in_array($email, $contacts)) {
                    \Drupal::messenger()->addError(t('The email entered is not subscribed to the datos.gob.es newsletter'));
                }
            } catch (\Exception $e) {
                \Drupal::messenger()->addError(t('An error occurred while checking the subscription status.'));
            }
        }
    }


    /**
     *
     * {@inheritDoc}
     * @see \Drupal\Core\Form\FormBase::submitForm()
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $subscriber = $form_state->getValue('email');

        $library = class_exists('DgeSendInBlueClient');

        if (! $library) {
            \Drupal::messenger()->addMessage(t('DGE SENDINBLUE PHP Client is not installed'), 'error');
        }
        try {
            $api_key = \Drupal::config('dge_sendinblue.config')->get('dge_sendinblue_api_key');
            $list_id = \Drupal::config('dge_sendinblue.config')->get('dge_sendinblue_list_id');

            $sendinblue = new DgeSendInBlueClient($api_key, $list_id);

            $sendinblue->removeContactFromList($subscriber);

            \Drupal::messenger()->addStatus(t('You have successfully unsubscribed from the datos.gob.es newsletter'));

            if (\Drupal::config('dge_sendinblue.config')->get('dge_sendinblue_from')) {

                $mailManager = \Drupal::service('plugin.manager.mail');
                $module = 'dge_sendinblue';
                $key = 'unsubscribe';
                $to = $subscriber;
                $langcode = \Drupal::languageManager()->getCurrentLanguage();
                $token_service = \Drupal::token();
                $token_options = ['callback' => 'user_mail_tokens', 'clear' => TRUE];
                $textBody = \Drupal::config('dge_sendinblue.config')->get('dge_sendinblue_unsuscribe_email_content');
                $textBodySubscriptors = $token_service->replace($textBody, ['mail' => $subscriber], $token_options);
                $params['subject'] = \Drupal::config('dge_sendinblue.config')->get('dge_sendinblue_unsuscribe_email_subject');
                $params['body'] = $textBodySubscriptors;
                $params['headers']['Bcc'] = \Drupal::config('dge_sendinblue.config')->get('dge_sendinblue_unsuscribe_send_email');
                $send = TRUE;
                $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

            } else {
                \Drupal::logger('maillog')->info(
                    'SendInBlue: Not configured. The unsubscribe email for @user has not been sent',
                    array('@user' => $subscriber)
                );
            }
        } catch (Exception $e) {
        }
    }
}
