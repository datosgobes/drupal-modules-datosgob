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


class DgeDataRequestSubscriptionForm extends FormBase {

  protected $renderer;
  protected $node;

  public function getFormId() {
      return 'dge_data_request_subscription_form';
  }

  private function loadNode() {
    $request = \Drupal::request();
    $nid = $request->attributes->get('nid');
    return $nid ? Node::load($nid) : null;
  }

  public function getTitle() {
    $node = $this->loadNode();
    $title = t('Join the request: ') . $node->get('title')->getString() ;
    return $title;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->node = $node = $this->loadNode();

    $form['container'] = [
        '#type' => 'markup',
        '#markup' => '<div class="container">',
        '#prefix' => '<div class="container">',
        '#suffix' => '</div>',
    ];

    $form['container']['field_sender_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Name and surname'),
        '#required' => TRUE,
        '#description' => $this->t('Enter first and last name.'),
        '#prefix' => '<div class="col-3">',
        '#suffix' => '</div>',
    ];

    $form['container']['field_sender_email'] = [
        '#type' => 'email',
        '#title' => $this->t('Email'),
        '#required' => TRUE,
        '#description' => $this->t('Enter your email address. Ex: abc@xyz.com'),
        '#prefix' => '<div class="col-3">',
        '#suffix' => '</div>',
    ];

    $form['container']['field_sender_company'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Organization'),
        '#required' => TRUE,
        '#prefix' => '<div class="col-3">',
        '#suffix' => '</div>',
    ];

    $form['container']['field_sender_desire'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('I want my name and organization to appear on the portal'),
        '#required' => TRUE,
    ];

    $form['container']['field_sender_consent'] = [
        '#type' => 'checkbox',
        '#title' => $this->t("I consent to Red.es, with Tax ID Q-2891006-E, as the data controller,
          processing the data provided directly by me, 
          as well as data obtained indirectly from my activity, 
          in accordance with the Privacy Policy and with the General Director's 
          Instruction on Exercising Rights to Personal Data Protection, available at the following link:
        <a href='https://www.red.es/es/proteccion-de-datos' target='_blank'>Personal data protection</a>"),
        '#required' => TRUE,
        '#attributes' => [
            'title' => $this->t("Select this checkbox if you want to continue"), 
            'aria-describedby' => 'edit-field-sender-consent-tooltip', 
        ],
        '#description' => [
            '#markup' => '<span id="edit-field-sender-consent-tooltip" class="visually-hidden">
          This field is required, and you must consent to the privacy policy to continue.</span>', 
        ],
    ];

    $form['container']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Assign'),
    ];

    return $form;
  }
  
  

  public function _email_is_subscribed($node, $email) {
    $found = false;
    $subcriptors= $node->get('field_request_subscriptor')->getValue();
    foreach ($subcriptors as $subcriptor) {
      if (isset($subcriptor['target_id'])) {
        $paragraph= Paragraph::load($subcriptor['target_id']);
        $subcriptor_email =$paragraph->get('field_correo_electronico')->value;
        if ($subcriptor_email == $email) {
          return true;
        }
      }
    }
    return $found;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    
    $names = trim($values['field_sender_name']);
    $email = trim($values['field_sender_email']);
    $company = trim($values['field_sender_company']);
    $desire = $values['field_sender_desire'];
    $consent = $values['field_sender_consent'];
    $node = $this->node;
    
    if (! $this->_email_is_subscribed($node, $email)) {

      $subcriptor = \Drupal\paragraphs\Entity\Paragraph::create([
        'type' => 'request_subscriptor',
        'field_nombre_y_apellidos' => $names,
        'field_correo_electronico' => $email,
        'field_organizacion' => $company,
      ]);

      $subcriptor->save();
      $node->get('field_request_subscriptor')->appendItem($subcriptor);
      $node->save();
      $this->node->set('field_number_subscriptors', $node->get('field_request_subscriptor')->count());
      $this->node->save();

      \Drupal::messenger()->addMessage(t('Thank you for joining the data request'), 'status', FALSE);

    } else {
      \Drupal::logger('request_subscriptor')->info(
      t('Thank you for trying to join the request but your email was already registered.'),
        array('@email' => $email, '@nid' => $this->node->get('nid')->value)
    );
      \Drupal::messenger()->addMessage(t('Thank you for trying to join the request 
      but your email was already registered.'), 'status', FALSE);
    }
    $form_state->setRedirect('entity.node.canonical', ['node' => $this->node->get('nid')->value]);
  }
}
