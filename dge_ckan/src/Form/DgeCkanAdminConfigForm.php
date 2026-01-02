<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_ckan (datos.gob.es)".
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
namespace Drupal\dge_ckan\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;

class DgeCkanAdminConfigForm extends ConfigFormBase
{

    /**
     *
     * {@inheritdoc}
     * @see \Drupal\Core\Form\FormInterface::getFormId()
     */
    public function getFormId()
    {
        return 'dge_ckan_config_form';
    }

    /**
     *
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'dge_ckan.config'
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \Drupal\Core\Form\ConfigFormBase::buildForm()
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('dge_ckan.config');

        $form = array();

        $form['ckan_host'] = array(
            '#type' => 'textfield',
            '#title' => t("CKAN host"),
            '#description' => t('Example:http://xxx.xxx.xxx.x/api/3/'),
            '#default_value' =>  $config->get('ckan_host')
        );

        $form['ckan_api_key'] = array(
            '#type' => 'textfield',
            '#title' => t("CKAN API Key"),
            '#default_value' =>  $config->get('ckan_api_key')
        );

        $form['ckan_url'] = array(
            '#type' => 'textfield',
            '#title' => t("CKAN Public URL"),
            '#description' => t('Example: http://xxx.xxx.xxx.x'),
            '#default_value' =>  $config->get('ckan_url')
        );

        $form['ckan_url_internal'] = array(
            '#type' => 'textfield',
            '#title' => t("CKAN Internal URL"),
            '#description' => t('Example: http://xxx.xxx.xxx.x'),
            '#default_value' =>  $config->get('ckan_url_internal')
        );

        $form['ckan_pagina_dataset'] = array(
            '#type' => 'textfield',
            '#title' => t("CKAN dataset page"),
            '#description' => t('Example: dataset'),
            '#default_value' => $config->get('ckan_pagina_dataset')
        );

        
        $vocabulary = Vocabulary::loadMultiple();
        $checklist_vocab_array = array();
        foreach ($vocabulary as $item) {
          $key = $item->id();
          $value = $item->label();
          $checklist_vocab_array[$key] = $value;
        }

        $form['dge_organization_vocabulary'] = array(
            '#type'             => 'select',
            '#target_type'      => 'taxonomy_term',
            '#title'            => t('Select organization list'),
            '#position'         => 'left',
            '#tags'             => true,
            '#options'          => $checklist_vocab_array,
            '#default_value'    => $config->get('dge_organization_vocabulary'),
        );
        
        return parent::buildForm($form, $form_state);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Drupal\Core\Form\ConfigFormBase::submitForm()
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitForm($form, $form_state);

        $this->config('dge_ckan.config')
            ->set('ckan_host', $form_state->getValue('ckan_host'))
            ->set('ckan_api_key', $form_state->getValue('ckan_api_key'))
            ->set('ckan_url', $form_state->getValue('ckan_url'))
            ->set('ckan_url_internal', $form_state->getValue('ckan_url_internal'))
            ->set('ckan_pagina_dataset', $form_state->getValue('ckan_pagina_dataset'))
            ->set('dge_organization_vocabulary', $form_state->getValue('dge_organization_vocabulary'))
            ->save();
    }
}
