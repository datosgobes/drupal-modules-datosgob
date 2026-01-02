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

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Render\RendererInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;


class DgeDataRequestUnsubscriptionForm extends FormBase {

  protected $renderer;
  protected $node;
  protected  $paragraph;


  private function loadNode() {
    $request = \Drupal::request();
    $nid = $request->attributes->get('nid');
    return $nid ? Node::load($nid) : null;
  }

  public function getFormId() {
      return 'dge_data_request_unsubscription_form';
  }

  public function getTitle() {
    $node = $this->loadNode();
    $title = t('Leave the data request: ') . $node->get('title')->getString() ;
    return $title;
  }


  public function buildForm(array $form, FormStateInterface $form_state) {
  
    $this->node = $node = $this->loadNode();
    $form['field_sender_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
      '#description' => $this->t('Enter your email address. Ex: abc@xyz.com'),
    ];

    $form['field_sender_consent'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("I consent to Red.es, with Tax ID Q-2891006-E, as the data controller,
        processing the data provided directly by me, 
        as well as data obtained indirectly from my activity, 
        in accordance with the Privacy Policy and with the General Director's 
        Instruction on Exercising Rights to Personal Data Protection, available at the following link:.
      <a href='https://www.red.es/es/proteccion-de-datos' target='_blank'>Personal data protection</a>"),
      '#required' => TRUE,
      '#attributes' => [
        'title' => $this->t("Select this checkbox if you want to continue."), 
        'aria-describedby' => 'edit-field-sender-consent-tooltip', 
      ],
      '#description' => [
        '#markup' => '<span id="edit-field-sender-consent-tooltip" class="visually-hidden">This field is required, and you must consent to the privacy policy to continue.</span>',
      ],
    ];
    
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Assign'),
    );
      return $form;
  }

  public function _email_is_subscribed_and_delete($node, $email) {
    $found = null;
    $subcriptors= $node->get('field_request_subscriptor')->getValue();
    foreach ($subcriptors as $key => $subcriptor) {
      if (isset($subcriptor['target_id'])) {
        $paragraph= Paragraph::load($subcriptor['target_id']);
        $subcriptor_email =$paragraph->get('field_correo_electronico')->value;
        if ($subcriptor_email == $email) {
          $node->get('field_request_subscriptor')->removeItem($key);
          $node->save();
          $node->set('field_number_subscriptors', $node->get('field_request_subscriptor')->count());
          $node->save();
          return true;
        }
      }
    }
    
    return $found;
}

  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $values = $form_state->getValues();
    $email = trim($values['field_sender_email']);
    $consent = $values['field_sender_consent'];
    $node = $this->node;
    
    if ($this->_email_is_subscribed_and_delete($node, $email)) {

      $config = \Drupal::config('dge_data_request.config');

      if ($config->get('dge_data_request_published_email_enabled')) {
        
        $mail_manager = \Drupal::service('plugin.manager.mail');
        $module = 'dge_data_request';
        $key = 'unsubscribed_request_data';
        $token_service = \Drupal::token();
        $token_options = ['callback' => 'user_mail_tokens', 'clear' => TRUE];
        $data_request_title = $node->getTitle();
        $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()]);
        $url->setAbsolute();
        $data_request_url = $url->toString();
        $to = $email;
        $langcode = \Drupal::languageManager()->getCurrentLanguage();
    
        $textBody = \Drupal::config('dge_data_request.config')->get('dge_data_request_published_email_content');
        $textBodyTokens = $token_service->replace($textBody, ['peticion_datos_title' => $data_request_title, 'peticion_datos_url' => $data_request_url], $token_options);
        $texSubject= \Drupal::config('dge_data_request.config')->get('dge_data_request_published_email_subject');
        $textSubjectTokens = $token_service->replace($texSubject, ['peticion_datos_title' => $data_request_title], $token_options);
        $params['subject'] = $textSubjectTokens;
        $params['body'] = $textBodyTokens;
    
        $send = TRUE;
        $mail_manager->mail($module, $key, $to, $langcode, $params, NULL, $send);
        \Drupal::messenger()->addMessage(t('You have successfully left the data request'), 'status', FALSE);
      }
    } else {
      \Drupal::messenger()->addMessage(t('You have correctly detached yourself from the data request.'), 'status', FALSE);
    }
    $form_state->setRedirect('entity.node.canonical', ['node' => $this->node->get('nid')->value]);
  }
}
