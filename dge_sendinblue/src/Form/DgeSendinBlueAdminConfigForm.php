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

/**
 * Importación librería DGE Sendinblue Client
 */
include_once (DRUPAL_ROOT . "/modules/custom/dge_sendinblue/libraries/dge_sendinblue_php_client/DgeSendInBlueClient.php");

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use DgeSendInBlueClient;

class DgeSendinBlueAdminConfigForm extends ConfigFormBase
{

    /**
     *
     * {@inheritdoc}
     * @see \Drupal\Core\Form\FormInterface::getFormId()
     */
    public function getFormId()
    {
        return 'dge_sendinblue_config_form';
    }

    /**
     *
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'dge_sendinblue.config'
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \Drupal\Core\Form\ConfigFormBase::buildForm()
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = \Drupal::config('dge_sendinblue.config');
        $dgeSendinblueApiKey = $config->get('dge_sendinblue_api_key', '');
        $dgeSendinblueListId = $config->get('dge_sendinblue_list_id', '');
        $dgeSendinblueFrom = $config->get('dge_sendinblue_from');
        $dgeSendinblueTestListId = $config->get('dge_sendinblue_test_list_id', '');
        $dgeSendinblueUnsusbscribeSendEmail = $config->get('dge_sendinblue_unsuscribe_send_email');
        $dgeSendinblueUnsusbscribeEmainSubject = $config->get('dge_sendinblue_unsuscribe_email_subject');
        $dgeSendinblueUnsusbscribeEmailContent = $config->get('dge_sendinblue_unsuscribe_email_content');


        $form = array();

        $form['dge_sendinblue'] = [
            '#type'  => 'fieldset',
            '#title' => t('Datos de authenticación de SendInBlue'),
        ];
        $form['dge_sendinblue']['info'] = [
            '#type'          => 'markup',
            '#title'         => t('SendInBlue List ID'),
            '#required'      => false,
            '#markup' => '<p></p>'
        ];
        $form['dge_sendinblue']['dge_sendinblue_api_key'] = [
            '#type'          => 'textfield',
            '#title'         => t('SendInBlue API Key'),
            '#required'      => false,
            '#default_value' => $dgeSendinblueApiKey,
            '#attributes' => array('style' => 'width:100%')
        ];
        $form['dge_sendinblue']['dge_sendinblue_list_id'] = [
            '#type'          => 'textfield',
            '#title'         => t('SendInBlue List ID'),
            '#required'      => false,
            '#default_value' => $dgeSendinblueListId,

        ];
        $form['dge_sendinblue']['dge_sendinblue_from'] = [
            '#type'          => 'email',
            '#title'         => t('From email'),
            '#default_value' => $dgeSendinblueFrom,
        ];
        $form['dge_sendinblue']['dge_sendinblue_test_list_id'] = [
            '#type'          => 'textfield',
            '#title'         => t('SendInBlue TEST List ID'),
            '#required'      => false,
            '#default_value' => $dgeSendinblueTestListId,
        ];

        $form['dge_sendinblue_unsuscribe_mail'] = [
            '#type'  => 'fieldset',
            '#title' => t('Newsletter unsuscribe mail configuration'),
        ];
        $form['dge_sendinblue_unsuscribe_mail']['dge_sendinblue_unsuscribe_send_email'] = [
            '#type'          => 'email',
            '#title'         => t('Send newsletter unsuscribe email (CCO)'),
            '#required'      => true,
            '#default_value' => $dgeSendinblueUnsusbscribeSendEmail,
        ];
        $form['dge_sendinblue_unsuscribe_mail']['dge_sendinblue_unsuscribe_email_subject'] = [
            '#type'          => 'textfield',
            '#title'         => t('Newsletter unsuscribe email subject'),
            '#default_value' => $dgeSendinblueUnsusbscribeEmainSubject,
        ];
        $form['dge_sendinblue_unsuscribe_mail']['dge_sendinblue_unsuscribe_email_content'] = [
            '#type'          => 'textarea',
            '#title'         => t('Newsletter unsuscribe email content'),
            '#required'      => true,
            '#default_value' =>  $dgeSendinblueUnsusbscribeEmailContent,
        ];

        $library = class_exists('DgeSendInBlueClient');

        if (! $library) {
            \Drupal::messenger()->addMessage(t('DGE SENDINBLUE PHP Client is not installed'), 'error');
        }

        if ($dgeSendinblueApiKey && $dgeSendinblueListId &&  $dgeSendinblueTestListId) {
            try {
                try {
                    $client = new  DgeSendInBlueClient($dgeSendinblueApiKey,  $dgeSendinblueListId);
                } catch (\Exception $e) {
                    $form['dge_sendinblue']['info']['#markup'] = '<div class="messages error">
                    <div>' .t('Error de Conexion:'). '<strong>' . $e->getMessage() . '</strong></div></div>';
                }
                try {
                    $client_test = new  DgeSendInBlueClient($dgeSendinblueApiKey,  $dgeSendinblueTestListId);
                } catch (\Exception $e) {
                    $form['dge_sendinblue']['info']['#markup'] = '<div class="messages error">
                    <div>' .t('Error de Conexion:'). '<strong>' . $e->getMessage() . '</strong></div></div>';
                }

                if(isset($client) && isset($client_test)){
                    $form['dge_sendinblue']['info']['#markup'] = '<div class="messages status">
                    <div><strong>'.t('Conexión exitosa').'</strong></div>
                    <div><strong>'.t('Account:').'</strong> ' . $client->account->getEmail() . '</div>
                    <div><strong>'.t('Lista de suscriptores:').'</strong> ' . $client->list->getName() . '</div>
                    <div><strong>'.t('Lista de PRUEBA:').'</strong> ' . $client_test->list->getName() . '</div>
             </div>';
                }


            } catch (Exception $e) {
                $form['dge_sendinblue']['info']['#markup'] = '<div class="messages error">
          <div>'.t('Error de Conexion:').' <strong>' . $e->getMessage() . '</strong></div></div>';
            }
        }

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

        $this->config('dge_sendinblue.config')
            ->set('dge_sendinblue_api_key', $form_state->getValue('dge_sendinblue_api_key'))
            ->set('dge_sendinblue_list_id', $form_state->getValue('dge_sendinblue_list_id'))
            ->set('dge_sendinblue_from', $form_state->getValue('dge_sendinblue_from'))
            ->set('dge_sendinblue_test_list_id', $form_state->getValue('dge_sendinblue_test_list_id'))
            ->set('dge_sendinblue_unsuscribe_send_email', $form_state->getValue('dge_sendinblue_unsuscribe_send_email'))
            ->set('dge_sendinblue_unsuscribe_email_subject', $form_state->getValue('dge_sendinblue_unsuscribe_email_subject'))
            ->set('dge_sendinblue_unsuscribe_email_content', $form_state->getValue('dge_sendinblue_unsuscribe_email_content'))
            ->save();
    }
}
