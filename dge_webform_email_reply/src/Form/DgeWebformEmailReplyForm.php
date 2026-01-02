<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_webform_email_reply (datos.gob.es)".
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
 * Contains \Drupal\dge_webform_email_reply\Form\DgeWebformEmailReplyForm.
 */

namespace Drupal\dge_webform_email_reply\Form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\webform\WebformRequestInterface;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

class DgeWebformEmailReplyForm extends FormBase {

  /**
   * A webform submission.
   *
   * @var \Drupal\webform\WebformSubmissionInterface
   */
  protected $webformSubmission;

  /**
   * The source entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dge_webform_email_reply_form';
  }

  /**
   * Webform request handler.
   *
   * @var \Drupal\webform\WebformRequestInterface
   */
  protected $requestHandler;

  /**
   * Current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $current_user;

  /**
   * Email Validator.
   *
   * @var Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * Constructs a WebformResultsResendForm object.
   *
   * @param \Drupal\webform\WebformRequestInterface $request_handler
   *   The webform request handler.
   */
  public function __construct(WebformRequestInterface $request_handler, AccountInterface $current_user, EmailValidatorInterface $email_validator) {
    $this->requestHandler = $request_handler;
    $this->currentUser = $current_user;
    $this->emailValidator = $email_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('webform.request'),
      $container->get('current_user'),
      $container->get('email.validator')
    );
  }


  public static function checkReplyAccess(WebformSubmissionInterface $webform_submission, AccountInterface $account) {
    if ($webform_submission->getWebform()->hasMessageHandler()) {
      if ($account->hasPermission('send email replies to contact webforms')) {
        return AccessResult::allowed();
      }
    }
    return AccessResult::forbidden();
  }

  public function buildForm(array $form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission = NULL) {
    $this->webformSubmission = $webform_submission;

    $user = $this->currentUser;
    $default_from_email = \Drupal::config('system.site')->get('mail');
    $title = $webform_submission->getWebform()->label();
    $webform_id = $webform_submission->getWebform()->id();
    $sid = $webform_submission->id();
    $submission_email_to = $webform_submission->getElementData('email');

    $workflow_elements = $webform_submission->getElementData('workflow');
    $workflow_state = '';
    if(isset($workflow_elements['workflow_state'])){
      $workflow_state = $workflow_elements['workflow_state'];
    }

    $form['#tree'] = TRUE;
    $form['details'] = [
      '#type' => 'fieldset',
      '#title' => t('Reply'),
    ];
    $form['details']['webform_id'] = [
      '#type' => 'value',
      '#value' => $webform_id,
    ];
    $form['details']['sid'] = [
      '#type' => 'value',
      '#value' => $sid,
    ];
     $form['details']['from_address'] = [
      '#type' => 'hidden',
      '#value' => $default_from_email,
    ];
    $form['details']['email'] = [
      '#type' => 'hidden',
      '#value' => $submission_email_to,
    ];
    $form['details']['workflow_state'] = [
      '#type' => 'hidden',
      '#value' => $workflow_state,
    ];
    $form['details']['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $this->t('RE: @title', [
        '@title' => strip_tags($title)
        ]),
      '#required' => TRUE,
    ];
    $form['details']['message'] = [
      '#type' => 'webform_html_editor',
      '#title' => $this->t('Message'),
      '#required' => TRUE,
    ];

    $form['details']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    $source_entity = $this->requestHandler->getCurrentSourceEntity('webform_submission');
    $form['navigation'] = [
      '#type' => 'webform_submission_navigation',
      '#webform_submission' => $webform_submission,
      '#weight' => -20,
    ];
    $form['information'] = [
      '#type' => 'webform_submission_information',
      '#webform_submission' => $webform_submission,
      '#source_entity' => $source_entity,
      '#weight' => -19,
    ];
    $form['#attached']['library'][] = 'webform/webform.admin';
    $form['#attached']['library'][] = 'webform/webform.element.html_editor';
    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $from_email = $form_state->getValue(['details', 'from_address']);
    if (!$this->emailValidator->isValid($from_email)) {
      $form_state->setErrorByName('details][from_address', $this->t('The from email address, @email, is not valid. Please enter a valid email address.', [
        '@email' => $from_email
        ]));
    }
    $valid_email = explode(',', $form_state->getValue(['details', 'email']));
    foreach ($valid_email as $email) {
      if (!$this->emailValidator->isValid($email)) {
        $form_state->setErrorByName('details][email', $this->t('The email address, @email, is not valid. Please enter a valid email address.', [
          '@email' => $email
          ]));
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $webform_id = $form_state->getValue(['details', 'webform_id']);
    $sid = $form_state->getValue(['details', 'sid']);

    $emails = explode(',', $form_state->getValue(['details', 'email']));
    $body = $form_state->getValue(['details', 'message']);
    $subject = $form_state->getValue(['details', 'subject']);

    $params = [
      'body' => $body,
      'subject' => $subject,
    ];

    $from_address = $form_state->getValue(['details', 'from_address']);
    $params['from'] = $from_address;

    $data = $form_state->getValue(['details']);

    $workflow_state = $form_state->getValue(['details', 'workflow_state']);
    $is_replied = $workflow_state == 'respondido' ? true : false;

    foreach ($emails as $email) {
      $mail_sent = \Drupal::service('plugin.manager.mail')->mail('dge_webform_email_reply', 'email', $email, $this->currentUser->getPreferredLangcode(), $params, NULL, TRUE);
      if ($mail_sent) {
        if($is_replied){
          \Drupal::messenger()->addMessage($this->t('Reply email sent to @email from @from_address.', [
            '@email' => $email,
            '@from_address' => $from_address,
          ]));
        }else{
          \Drupal::messenger()->addMessage($this->t('Reply email sent to @email from @from_address.<br><br> The submission state is not "replied". You can change the state by editing the submission.', [
            '@email' => $email,
            '@from_address' => $from_address,
          ]));
        }

        dge_webform_email_reply_insert($data);
      }
      else {
        \Drupal::messenger()->addError($this->t('There was an error sending the email to @email, please contact the site admin.', [
          '@email' => $email
        ]));
      }
    }
  }

}
?>
