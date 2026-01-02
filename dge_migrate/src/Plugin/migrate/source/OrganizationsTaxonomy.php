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
 * Contains \Drupal\dge_migrate\Plugin\migrate\source\OrganizationsTaxonomy.
 */
 
namespace Drupal\dge_migrate\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
 
/**
 *
 * @MigrateSource(
 *   id = "dge_migrate_taxonomy_organizations",
 *   source_module = "dge_migrate",
 *   source_provider = "taxonomy"
 * )
 */
class OrganizationsTaxonomy extends SqlBase {
 
  /**
   * {@inheritdoc}
   */
  public function query() {
    $subquery = $this->select('taxonomy_vocabulary', 'tv')
                        ->condition('tv.machine_name', 'agencies')
                        ->fields('tv', ['vid']);

    $query = $this->select('taxonomy_term_data', 'tt')
      ->condition('tt.vid', $subquery, '=')
      ->fields('tt', array(
        'tid',
        'vid',
        'name',
        'description',
        'weight',
      ));
    $query->orderBy('tid');
    return $query;
  }
 
  /**
   * {@inheritdoc}
   */
  public function fields() {
    $organization_fields = $this->baseOrganizationFields();
    $organization_fields['field_ckan_organization_id'] = $this->t('CKAN Organization ID');
    $organization_fields['field_ckan_organization_name'] = $this->t('CKAN Organization Name');
    $organization_fields['field_c_id_dep_ud_principal'] = $this->t('C_ID_DEP_UD_PRINCIPAL');
    $organization_fields['field_c_id_ud_organica'] = $this->t('C_ID_UD_ORGANICA');
    $organization_fields['field_reference_id'] = $this->t('Reference ID');
    $organization_fields['field_root_designation_unit'] = $this->t('Root Designation Unit');
    return $organization_fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array(
      'tid' => array(
        'type' => 'integer',
        'alias' => 'tt',
      ),
    );
  }

  private function fetchFieldValue($field_name, $tid) {
    $query = $this->getDatabase()->query("
      SELECT fld.{$field_name}_value
      FROM {field_data_{$field_name}} fld
      WHERE fld.entity_id = :tid
    ", [':tid' => $tid]);
    
    foreach ($query as $record) {
      return $record->{$field_name . '_value'};
    }

    return NULL;
  }
 
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $tid = $row->getSourceProperty('tid');

    $row->setSourceProperty('d7_field_ckan_organization_id', $this->fetchFieldValue('field_ckan_organization_id', $tid));
    $row->setSourceProperty('d7_field_ckan_organization_name', $this->fetchFieldValue('field_ckan_organization_name', $tid));
    $row->setSourceProperty('d7_field_c_id_dep_ud_principal', $this->fetchFieldValue('field_c_id_dep_ud_principal', $tid));
    $row->setSourceProperty('d7_field_c_id_ud_organica', $this->fetchFieldValue('field_c_id_ud_organica', $tid));
    $row->setSourceProperty('d7_field_reference_id', $this->fetchFieldValue('field_reference_id', $tid));
    $row->setSourceProperty('d7_field_root_designation_unit', $this->fetchFieldValue('field_root_designation_unit', $tid));

    $log_message = '';
    $log_message .= json_encode($row->getSourceProperty('tid')) . ';';
    $log_message .= json_encode($row->getSourceProperty('d7_field_ckan_organization_id')) . ';';
    $log_message .= json_encode($row->getSourceProperty('d7_field_ckan_organization_name')) . ';';
    $log_message .= json_encode($row->getSourceProperty('d7_field_c_id_dep_ud_principal')) . ';';
    $log_message .= json_encode($row->getSourceProperty('d7_field_c_id_ud_organica')) . ';';
    $log_message .= json_encode($row->getSourceProperty('d7_field_reference_id')) . ';';
    $log_message .= json_encode($row->getSourceProperty('d7_field_root_designation_unit')) . ';';

    \Drupal::logger('dge_migrate')->warning($log_message);
    
    // description
    $result = $this->getDatabase()->query('
      SELECT
        fld.description,
        fld.format
      FROM
        {taxonomy_term_data} fld
      WHERE
        fld.tid = :tid
    ', array(':tid' => $tid));
    $d7_description = [];
    foreach ($result as $record) {
      $d7_description[] = [
        'value' => $record->description,
        'format' => $record->format,
      ];
    }
    $row->setSourceProperty('d7_description', $d7_description);

  return parent::prepareRow($row);
  }
 
  
 
  /**
   *
   * @return array
   */
  protected function baseOrganizationFields() {
    $organization_fields = [];
    $organization_fields['tid'] = $this->t('Taxonomy ID');
    $organization_fields['vid'] = $this->t('Taxonomy Organizations ID');
    $organization_fields['name'] = $this->t('Taxonomy namet');
    $organization_fields['description'] = $this->t('Taxonomy description');
    $organization_fields['weight'] = $this->t('Taxonomy weight');
    return $organization_fields;
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
    return 'taxonomy_term';
  }
 
}
