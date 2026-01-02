<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_email_footer_header (datos.gob.es)".
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
namespace Drupal\dge_email_footer_header\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;

class DgeHeaderFooterEmailAdminConfigForm extends ConfigFormBase
{

    /**
     *
     * {@inheritdoc}
     * @see \Drupal\Core\Form\FormInterface::getFormId()
     */
    public function getFormId()
    {
        return 'dge_email_footer_header_config_form';
    }

    /**
     *
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'dge_email_footer_header.config'
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \Drupal\Core\Form\ConfigFormBase::buildForm()
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('dge_email_footer_header.config');

        $form = array();

        $form['header'] = array(
            '#type' => 'text_format',
            '#title' => t("Header emails"),
            '#default_value' =>  $config->get('header'),
            '#format' => $config->get('header_format'),
        );

        $form['footer'] = array(
            '#type' => 'text_format',
            '#title' => t("Footer emails"),
            '#default_value' =>  $config->get('footer'),
            '#format' => $config->get('footer_format'),
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
        $values = $form_state->getValues();
        $this->config('dge_email_footer_header.config')
            ->set('header',  $values['header']['value'])
            ->set('header_format',  $values['header']['format'])
            ->set('footer', $values['footer']['value'])
            ->set('footer_format', $values['footer']['format'])
            ->save();
    }
}
