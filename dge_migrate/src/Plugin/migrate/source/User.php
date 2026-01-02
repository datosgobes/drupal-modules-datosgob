<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_migrate (datos.gob.es)".
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
 * Contains \Drupal\dge_migrate\Plugin\migrate\source\User.
 */
 
namespace Drupal\dge_migrate\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
 
/**
 *
 * @MigrateSource(
 *   id = "dge_migrate_users_roles",
 *   source_module = "dge_migrate"
 * )
 */
class User extends SqlBase {
 
  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('users', 'u')
      ->fields('u', array_keys($this->baseUserFields()))
      ->condition('uid', 0, '>');
  }
 
  /**
   * {@inheritdoc}
   */
  public function fields() {
    $user_fields = $this->baseUserFields();
    $user_fields['roles'] = $this->t('User roles');
    $user_fields['field_nombre'] = $this->t('User name');
    $user_fields['field_apellidos'] = $this->t('User last name');
    $user_fields['field_telefono'] = $this->t('User phone');
    $user_fields['field_organizacion'] = $this->t('User organization');
    $user_fields['field_posicion'] = $this->t('User position');
    $user_fields['field_comentarios_adicionales'] = $this->t('User additional comment');
    $user_fields['field_terminos_de_servicio'] = $this->t('User terms of service');
    $user_fields['field_ckan_user_id'] = $this->t('User id CKAN');
    $user_fields['field_ckan_user_name'] = $this->t('User name CKAN');
    return $user_fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'uid' => array(
        'type' => 'integer',
        'alias' => 'u',
      ),
    );
  }

  private function fetchFieldValue($field_name, $uid, $profile_name) {
    $query = $this->getDatabase()->query("
      SELECT fld.{$field_name}_value
      FROM 
        {profile} p
      LEFT JOIN
        {field_data_{$field_name}} fld ON fld.entity_id  = p.pid
      WHERE
        p.uid = :uid and p.type  = :profile_name
    ", [
      ':uid' => $uid,
      ':profile_name' => $profile_name,
    ]);
    
    foreach ($query as $record) {
      return $record->{$field_name . '_value'};
    }

    return NULL;
  }
 
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $uid = $row->getSourceProperty('uid');
    $log_message = '';
    $log_message .= json_encode($row->getSourceProperty('uid')) . ';';

    // roles
    $result = $this->getDatabase()->query('
      SELECT
        r.name
      FROM
        {users_roles} ur
      LEFT JOIN
        {role} r ON ur.rid = r.rid
      WHERE
        ur.uid = :uid
    ', array(':uid' => $uid));

    $roles = [];
    foreach ($result as $record) {
      $roles[] = $record->name;
    }
    $row->setSourceProperty('roles', $roles);
    $log_message .= json_encode($row->getSourceProperty('roles')) . ';';

    // field_nombre
    $row->setSourceProperty('d7_nombre', $this->fetchFieldValue('field_name_contact', $uid, 'main'));
    $log_message .= json_encode($row->getSourceProperty('d7_nombre')) . ';';

    // field_apellidos
    $row->setSourceProperty('d7_apellidos', $this->fetchFieldValue('field_surname_contact', $uid, 'main'));
    $log_message .= json_encode($row->getSourceProperty('d7_apellidos')) . ';';

    // field_telefono
    $row->setSourceProperty('d7_telefono', $this->fetchFieldValue('field_phone_contact', $uid, 'main'));
    $log_message .= json_encode($row->getSourceProperty('d7_telefono')) . ';';

    // field_organizacion
    $result = $this->getDatabase()->query('
      SELECT
        fld.field_root_agency_tid,
        tt.name
      FROM
        {profile} p
      LEFT JOIN
        {field_data_field_root_agency} fld ON fld.entity_id  = p.pid
      LEFT JOIN
        {taxonomy_term_data} tt ON fld.field_root_agency_tid  = tt.tid
      WHERE
        p.uid = :uid and p.type  = \'agency_data\'
    ', array(':uid' => $uid));
    foreach ($result as $record) {
      if (empty($record->field_root_agency_tid)) {
        $row->setSourceProperty('d7_tidOrganizacion', NULL);
      } else {
        $row->setSourceProperty('d7_tidOrganizacion', $record->field_root_agency_tid);
        $log_message .= json_encode($record->name) . ';';
        $log_message .= json_encode($row->getSourceProperty('d7_tidOrganizacion')) . ';';
      }
    }

    $row->setSourceProperty('d7_posicion', $this->fetchFieldValue('field_position_agency', $uid, 'agency_data'));
    $log_message .= json_encode($row->getSourceProperty('d7_posicion')) . ';';

    $row->setSourceProperty('d7_comentarios_adicionales', $this->fetchFieldValue('field_comments_agency', $uid, 'agency_data'));
    $log_message .= json_encode($row->getSourceProperty('d7_comentarios_adicionales')) . ';';

    $row->setSourceProperty('d7_terminosServicio', $this->fetchFieldValue('field_terms_service', $uid, 'agency_data'));
    $log_message .= json_encode($row->getSourceProperty('d7_terminosServicio')) . ';';

    $row->setSourceProperty('d7_ckan_user_id', $this->fetchFieldValue('field_ckan_user_id', $uid, 'agency_data'));
    $log_message .= json_encode($row->getSourceProperty('d7_ckan_user_id')) . ';';

    $row->setSourceProperty('d7_ckan_user_name', $this->fetchFieldValue('field_ckan_user_name', $uid, 'agency_data'));
    $log_message .= json_encode($row->getSourceProperty('d7_ckan_user_name')) . ';';

    \Drupal::logger('dge_migrate')->warning($log_message);
  
  return parent::prepareRow($row);
  }
 
  
 
  /**
   *
   * @return array
   */
  protected function baseUserFields() {
    $user_fields = array(
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'pass' => $this->t('Password'),
      'mail' => $this->t('Email address'),
      'signature' => $this->t('Signature'),
      'signature_format' => $this->t('Signature format'),
      'created' => $this->t('Registered timestamp'),
      'access' => $this->t('Last access timestamp'),
      'login' => $this->t('Last login timestamp'),
      'status' => $this->t('Status'),
      'timezone' => $this->t('Timezone'),
      'language' => $this->t('Language'),
      'picture' => $this->t('Picture'),
      'init' => $this->t('Init'),
    );
    return $user_fields;
 
}
 
  /**
   * {@inheritdoc}
   */
  public function bundleMigrationRequired() {
    return FALSE;
  }
 
  /**
   * {@inheritdoc}
   */
  public function entityTypeId() {
    return 'user';
  }
 
}
