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
 * Contains \Drupal\dge_migrate\Plugin\migrate\source\TagsTaxonomy.
 */
 
namespace Drupal\dge_migrate\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
 
/**
 *
 * @MigrateSource(
 *   id = "dge_migrate_taxonomy_tags",
 *   source_module = "dge_migrate",
 *   source_provider = "taxonomy"
 * )
 */
class TagsTaxonomy extends SqlBase {
 
  /**
   * {@inheritdoc}
   */
  public function query() {
    $tags_type = $this->configuration['tags_type'];

    $subquery = $this->select('taxonomy_vocabulary', 'tv')
                        ->condition('tv.machine_name', $tags_type, 'IN')
                        ->fields('tv', ['vid']);

    $query = $this->select('taxonomy_term_data', 'tt')
      ->condition('tt.vid', $subquery, 'IN')
      ->fields('tt', array(
        'tid',
        'vid',
        'name',
        'description',
        'format',
        'weight',
      ));
    $query->orderBy('tid');
    return $query;
  }
 
  /**
   * {@inheritdoc}
   */
  public function fields() {
    return $tags_fields;
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
 
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $tid = $row->getSourceProperty('tid');
    $name = $row->getSourceProperty('name');

    $log_message = '';
    $log_message .= json_encode($row->getSourceProperty('tid')) . ';';
    $log_message .= json_encode('d7: ' . $row->getSourceProperty('name')) . ';';

    try {
        $file = __DIR__ . "/mapping_tags.csv";
        if (!file_exists($file)) {
            throw new \Exception("El archivo CSV no existe: $file");
        }
    
        $handle = fopen($file, "r");
        if (!$handle) {
            throw new \Exception("No se pudo abrir el archivo CSV: $file");
        }
    
        $csv_map = [];
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if (isset($data[0], $data[1])) {
                $csv_map[trim($data[0])] = trim($data[1]);
            }
        }
        fclose($handle);
        $mapped_tag = $csv_map[$name] ?? null;
        if (!empty($mapped_tag)) {
          $query = \Drupal::database()->select('taxonomy_term_field_data', 't');
          $query->fields('t', ['tid']);
          $query->condition('t.name', $mapped_tag);
          $query->condition('t.vid', 'tags');
          $existing_tid = $query->execute()->fetchField();

          if ($existing_tid) {
            $row->setDestinationProperty('tid', $existing_tid);
          }

          $row->setSourceProperty('filtered_tag', $mapped_tag);
          $log_message .= json_encode('d10: ' . $row->getSourceProperty('filtered_tag')) . ';';

        } else {
          $row->setSourceProperty('filtered_tag', '');
          $log_message .= json_encode('d10: ' . $row->getSourceProperty('filtered_tag')) . ';';
        }
    
    } catch (\Exception $e) {
        echo "Error con el filtro de etiquetas.";
    }


    \Drupal::logger('dge_migrate')->warning($log_message);

    return parent::prepareRow($row);
  }
 
  
 
  /**
   *
   * @return array
   */
  protected function baseTagsFields() {
    $tags_fields = [];
    $tags_fields['tid'] = $this->t('Taxonomy ID');
    $tags_fields['vid'] = $this->t('Taxonomy Tags ID');
    $tags_fields['name'] = $this->t('Taxonomy name');
    $tags_fields['description'] = $this->t('Taxonomy description');
    $tags_fields['weight'] = $this->t('Taxonomy weight');
    return $tags_fields;
 
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
