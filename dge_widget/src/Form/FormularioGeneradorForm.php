<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_widget (datos.gob.es)".
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

namespace Drupal\dge_widget\Form;

use Drupal\Core\Form\FormBase;
use Drupal\profile\Entity\Profile;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

class FormularioGeneradorForm extends FormBase {

  public function getFormId() {
    return 'formulario_generador_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $user = User::load(\Drupal::currentUser()->id());
    $this->addJs($form);

    $vid = \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->load('organizaciones')->id();
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);

    $array_organismos = [];
    foreach ($terms as $term) {
      $term_data = \Drupal\taxonomy\Entity\Term::load($term->tid);
      if ($term_data->hasField('field_reference_id')) {
        $field_reference_id = $term_data->get('field_reference_id')->value;
        $array_organismos[$field_reference_id] = $term_data->getName();
      }
    }

    $form['description'] = [
      '#markup' => $this->t('For generating your widget, select an organization, the desired widget dimensions and push the Send button'),
    ];

    $form['organismo'] = [
      '#type' => 'select',
      '#title' => $this->t('Select the organization:'),
      '#options' => $array_organismos,
      '#required' => TRUE,
    ];

    if ($user) {
      if($user->get('field_organizacion')->getValue() != null){
        if($user->get('field_organizacion')->getValue()[0]['target_id']){
          $term_id = $user->get('field_organizacion')->getValue()[0]['target_id'];
          if ($term_id) {
              $term = \Drupal\taxonomy\Entity\Term::load($term_id);
              if ($term && $term->hasField('field_reference_id')) {
                  $form['organismo']['#default_value'] = $term->get('field_reference_id')->value;
              }
          }
        }
      }  
    }

    $form['ancho'] = [
      '#type' => 'textfield',
      '#size' => 12,
      '#title' => $this->t('Enter width:'),
    ];

    $form['alto'] = [
      '#type' => 'textfield',
      '#size' => 12,
      '#title' => $this->t('Enter height:'),
    ];

    $form['ordenacion'] = [
      '#type' => 'select',
      '#title' => $this->t('Select the order:'),
      '#options' => [
        'fecha_desc' => $this->t('Fecha (descendente)'),
        'titulo_asc' => $this->t('Título (ascendente)'),
      ],
      '#default_value' => 'fecha_desc', 
      '#required' => TRUE,
    ];
    
    $form['sede'] = [
      '#type' => 'select',
      '#title' => $this->t('Include warning of redirection to datos.gob.es:'),
      '#options' => [
        'si' => $this->t('Sí'),
        'no' => $this->t('No'),
      ],
      '#default_value' => 'si', 
      '#required' => TRUE,
    ];
    
    $form['boton'] = [
      '#type' => 'button',
      '#value' => $this->t('Generate'),
      '#attributes' => ['onclick' => 'return (false);'],
    ];

    $form['codigo'] = [
      '#title' => $this->t('HTML code of the generated widget'),
      '#type' => 'textarea',
      '#cols' => 25,
      '#rows' => 12,
      '#required' => FALSE,
      '#attributes' => [
        'readonly' => 'readonly',
        'style' => 'overflow: hidden; resize: none;'
      ],
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
   
  }

  private function addJs(array &$form) {
    $form['#attached']['library'][] = 'dge_widget/dge_widget';
  }

}
