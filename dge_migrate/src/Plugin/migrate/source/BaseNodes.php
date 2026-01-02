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
 * Contains \Drupal\dge_migrate\Plugin\migrate\source\Nodes.
 */
 
namespace Drupal\dge_migrate\Plugin\migrate\source;
 
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
 

abstract class BaseNodes extends SqlBase {
 
  /**
   * {@inheritdoc}
   */
  abstract public function query();
 
  /**
   * {@inheritdoc}
   */
  public function fields() {

    $node_fields = $this->baseNodesFields();
    $node_fields['body_format'] = $this->t('Format of body');
    $node_fields['body_value'] = $this->t('Full text of body');
    $node_fields['body_summary'] = $this->t('Summary of body');

    $node_fields['image_fid'] = $this->t('Fid of image');
    $node_fields['image_alt'] = $this->t('Alt of image');
    $node_fields['image_title'] = $this->t('Title of image');
    $node_fields['image_width'] = $this->t('Width of image');
    $node_fields['image_height'] = $this->t('Height of image');
    $node_fields['field_date'] = $this->t('Date of content');

    $node_fields['field_ficheros_adjuntos'] = $this->t('Files attached of content');
    $node_fields['field_ficheros_externos_adjuntos'] = $this->t('Extern files attached of content');

    $node_fields['field_nombre_y_apellidos'] = $this->t('Sender name');
    $node_fields['field_correo_electronico'] = $this->t('Sender email');
    $node_fields['field_telefono'] = $this->t('Sender phone');
    $node_fields['field_acepto_las_condiciones_del'] = $this->t('Sender terms of use');

 
    $node_fields['field_deseo_que_mi_nombre_y_orga'] = $this->t('Sender show data');
    $node_fields['field_categoria'] = $this->t('Category taxonomy');

    $node_fields['field_blog_source'] = $this->t('Source of content');

    $node_fields['field_url_catalogo'] = $this->t('Dataset url');
    $node_fields['field_categoria'] = $this->t('Category taxonomy');
    $node_fields['field_url_catalogo'] = $this->t('Dataset url');
    $node_fields['field_nombre_y_apellidos'] = $this->t('Sender name');
    $node_fields['field_correo_electronico'] = $this->t('Sender email');
    $node_fields['field_telefono'] = $this->t('Sender phone');
    $node_fields['field_deseo_que_mi_nombre_y_orga'] = $this->t('Sender show data');
    $node_fields['field_acepto_las_condiciones_del'] = $this->t('Sender terms of use');

    $node_fields['field_detailed_question'] = $this->t('Detailed question');
    $node_fields['field_faq_category_name'] = $this->t('ID of FAQ Categories');

    $node_fields['field_tipo'] = $this->t('ID of Tipo de documentación categorie');
    $node_fields['field_audiencia'] = $this->t('ID of Audiencia categorie');
    $node_fields['field_autoria'] = $this->t('ID of Autoría categorie');
    $node_fields['field_url_video_youtube'] = $this->t('URL youtube video');

    $node_fields['field_bio_nombre'] = $this->t('Interview bio name');
    $node_fields['field_bio_organismo_institucion'] = $this->t('Interview bio company');
    $node_fields['field_bio_pais'] = $this->t('Interview bio country');
    $node_fields['field_bio_sector'] = $this->t('Interview bio sector');
    $node_fields['field_bio_especializacion'] = $this->t('Interview bio specialization');
    $node_fields['field_bio_trabajos_a_destacar'] = $this->t('Interview bio works');
    $node_fields['field_bio_contacto'] = $this->t('Interview bio contact');
    $node_fields['field_bio_pagina_web'] = $this->t('Interview bio web page');

    $node_fields['field_fecha_de_publicacion'] = $this->t('Application publish date');
    $node_fields['field_fecha_de_actualizacion'] = $this->t('Application update date');
    $node_fields['field_autor_desarrollador'] = $this->t('Application developer');
    $node_fields['field_plataforma'] = $this->t('Application platform');
    $node_fields['field_licencia'] = $this->t('Application license');
    $node_fields['field_url_aplicacion'] = $this->t('Application url');
    $node_fields['field_capturas_de_pantalla'] = $this->t('Application images');
    $node_fields['field_empresa_organismo'] = $this->t('Application sender company');
    
    $node_fields['field_year_create_reuse_co'] = $this->t('Reuse company creation year');
    $node_fields['field_pagina_web_de_la_empresa'] = $this->t('Reuse company web page');
    $node_fields['field_tamano_de_la_empresa'] = $this->t('Reuse company size');
    $node_fields['field_oficinas_en'] = $this->t('Reuse company offices');
    $node_fields['field_sede_central'] = $this->t('Reuse company headquarters');
    $node_fields['field_grado_de_internacionalizac'] = $this->t('Reuse company degree of internalization');
    $node_fields['field_principales_productos_y_se'] = $this->t('Reuse company main products and services');
    $node_fields['field_clientes'] = $this->t('Reuse company clients');
    $node_fields['field_datos_abiertos_utilizados'] = $this->t('Reuse company open data used');
    $node_fields['field_modelo_de_comercializacion'] = $this->t('Reuse company marketing model');
    $node_fields['field_clave_del_exito'] = $this->t('Reuse company key to success');
    $node_fields['field_crecimiento'] = $this->t('Reuse company increase');
    $node_fields['field_planes_de_futuro'] = $this->t('Reuse company future plans');
    $node_fields['field_mas_informacion'] = $this->t('Reuse company more information');

    $node_fields['field_initiative_channel'] = $this->t('Initiative request channel');
    $node_fields['field_initiative_catalog'] = $this->t('Initiative app catalog');
    $node_fields['field_initiative_link'] = $this->t('Link to the initiative');
    $node_fields['field_enlace_al_cat_logo_de_dato'] = $this->t('Link to the open data catalog');
    $node_fields['field_initiative_strategy'] = $this->t('Initiative open data strategy');
    $node_fields['field_initiative_resources'] = $this->t('Initiative resources');
    $node_fields['field_initiative_license'] = $this->t('Initiative license');
    $node_fields['field_initiative_organization'] = $this->t('Organization promoting the initiative');
    $node_fields['field_initiative_collaborate'] = $this->t('Collaborate with datos.gob.es');
    $node_fields['field_initiative_category'] = $this->t('Initiative data themes');
    $node_fields['field_initiative_admin_level'] = $this->t('Initiative more information');
    $node_fields['field_geoposition'] = $this->t('Initiative geoposition');

    $node_fields['field_organizacion'] = $this->t('Data request sender company');
    $node_fields['field_motivo_de_la_peticion'] = $this->t('Data request reason');
    $node_fields['field_beneficios_esperados'] = $this->t('Data request beneficts');
    $node_fields['field_respuesta'] = $this->t('Data request answer');
    $node_fields['field_publicador'] = $this->t('Data request publisher');
    $node_fields['field_url_del_conjunto_de_datos'] = $this->t('Data request dataset url');
    $node_fields['field_request_subscriptor'] = $this->t('Data request request subscriptor');
    $node_fields['field_organismo_responsable'] = $this->t('Data request organization');
    $node_fields['field_number_subscriptors'] = $this->t('Data request number of subscriptors');
    $node_fields['field_estado'] = $this->t('Data request content status');

    $node_fields['field_photo'] = $this->t('Aporta Speakers photo');
    $node_fields['field_position'] = $this->t('Aporta Speakers position');
    $node_fields['field_departament'] = $this->t('Aporta Speakers departament');
    $node_fields['field_twitter'] = $this->t('Aporta Speakers twitter link');
    $node_fields['field_linkedin'] = $this->t('Aporta Speakers linkedin link');

    return $node_fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['nid']['type'] = 'integer';
    $ids['nid']['alias'] = 'n';
    return $ids;
  }

  private function fetchFieldValue($field_name, $nid) {
    $query = $this->getDatabase()->query("
    SELECT
      fld.{$field_name}_value
    FROM
      {field_data_{$field_name}} fld
    WHERE
      fld.entity_id = :nid
      ", [
        ':nid' => $nid,
    ]);

    foreach ($query as $record) {
      return $record->{$field_name . '_value'};
    }
    return NULL;
  }

  private function fetchFieldValueFormat($field_name, $nid) {
    $query = $this->getDatabase()->query("
    SELECT
      fld.{$field_name}_value,
      fld.{$field_name}_format
    FROM
      {field_data_{$field_name}} fld
    WHERE
      fld.entity_id = :nid
      ", [
        ':nid' => $nid,
    ]);

    $values_format = [];
    foreach ($query as $record) {
      $values_format[] = [
        'value' => $record->{$field_name . '_value'},
        'format' => $record->{$field_name . '_format'},
      ];
    }
    return $values_format;
  }

  private function fetchFieldTaxonomy($field_name, $nid) {
    $query = $this->getDatabase()->query("
      SELECT 
        tt.name
      FROM 
        {field_data_{$field_name}} fld
      LEFT JOIN
        {taxonomy_term_data} tt ON fld.{$field_name}_tid  = tt.tid
      WHERE
        fld.entity_id = :nid
      ", [
        ':nid' => $nid,
    ]);

    $taxonomies = [];
    foreach ($query as $record) {
      $taxonomies[] = [
        'name' => $record->name,
      ];
    }
    return $taxonomies;
  }

  private function fetchFieldImages($field_name, $nid) {
    $query = $this->getDatabase()->query("
    SELECT
      fld.field_{$field_name}_fid,
      fld.field_{$field_name}_alt,
      fld.field_{$field_name}_title,
      fld.field_{$field_name}_width,
      fld.field_{$field_name}_height
    FROM
      {field_data_field_{$field_name}} fld
    WHERE
      fld.entity_id = :nid
    ", [
      ':nid' => $nid,
    ]);

    $images = [];
    foreach ($query as $record) {
      $images[] = [
        'image_fid' => $record->{'field_' . $field_name . '_fid'},
        'alt' => $record->{'field_' . $field_name . '_alt'},
        'title' => $record->{'field_' . $field_name . '_title'},
        'width' => $record->{'field_' . $field_name . '_width'},
        'height' => $record->{'field_' . $field_name . '_height'},
      ];
    }
    return $images;
  }

  private function fetchFieldFiles($field_name, $nid) {
    $query = $this->getDatabase()->query("
    SELECT
        fld.field_{$field_name}_fid,
        fld.field_{$field_name}_display,
        fld.field_{$field_name}_description
      FROM
        {field_data_field_{$field_name}} fld
      WHERE
        fld.entity_id = :nid
    ", [
      ':nid' => $nid,
    ]);

    $ficheros_adjuntos = [];
    foreach ($query as $record) {
      $ficheros_adjuntos[] = [
        'files_fid' => $record->{'field_' . $field_name . '_fid'},
        'display' => $record->{'field_' . $field_name . '_display'},
        'description' => $record->{'field_' . $field_name . '_description'},
      ];
    }
    return $ficheros_adjuntos;
  }

  private function fetchFieldURL($field_name, $nid) {
    $query = $this->getDatabase()->query("
    SELECT
        fld.field_{$field_name}_url,
        fld.field_{$field_name}_title,
        fld.field_{$field_name}_attributes
      FROM
        {field_data_field_{$field_name}} fld
      WHERE
        fld.entity_id = :nid
    ", [
      ':nid' => $nid,
    ]);

    $url = [];
    foreach ($query as $record) {
      $url[] = [
        'uri' => $record->{'field_' . $field_name . '_url'},
        'title' => $record->{'field_' . $field_name . '_title'},
      ];
    }
    return $url;
  }
 
  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    $nid = $row->getSourceProperty('nid');
    $type = $row->getSourceProperty('type');

    $log_message = '';
    $log_message .= json_encode($nid) . ';';
    $log_message .= json_encode($type) . ';';

    $row->setSourceProperty('moderation_state', ($row->getSourceProperty('status') == 1) ? 'published' : 'draft');

    $result = $this->getDatabase()->query('
      SELECT
        fld.body_value,
        fld.body_summary,
        fld.body_format
      FROM
        {field_data_body} fld
      WHERE
        fld.entity_id = :nid
    ', array(':nid' => $nid));
    $description = [];
    foreach ($result as $record) {
      $description[] = [
        'value' => $record->body_value,
        'summary' => $record->body_summary,
        'format' => ($record->body_format == 'filtered_html' || 'code') ? 'full_html' : $record->body_format,
      ];
    }
    $row->setSourceProperty('field_body', $description);
    $log_message .= json_encode($description) . ';';

    $result = $this->getDatabase()->query('
      SELECT
        MIN(fld.timestamp) AS published_date
      FROM
        {node_revision} fld
      WHERE
        fld.nid = :nid AND fld.status = 1
    ', array(':nid' => $nid));
    foreach ($result as $record) {
      $row->setSourceProperty('publish_d7', $record->published_date);
      $log_message .= json_encode($row->getSourceProperty('publish_d7')) . ';';
    }

    if ( $type == 'blog' || $type == 'blog_blog' || $type == 'doc' || $type == 'talk' || $type == 'app' || $type == 'success' || $type == 'event' ) {
      $row->setSourceProperty('field_titulo_contenido_relaciona_d7', $this->fetchFieldValue('field_content_related', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_titulo_contenido_relaciona_d7')) . ';';

      $result = $this->getDatabase()->query('
        SELECT
          fld.field_related_content_target_id
        FROM
          {field_data_field_related_content} fld
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));
      $related_content = [];
      foreach ($result as $record) {
        $related_content[] = [
          'target_id' => $record->field_related_content_target_id,
        ];
      }
      $row->setSourceProperty('field_related_content_d7', $related_content);
      $log_message .= json_encode($row->getSourceProperty('field_related_content_d7')) . ';';

      $result = $this->getDatabase()->query("
        SELECT
          fld.field_{$type}_tags_tid,
          tt.name
        FROM
          {field_data_field_{$type}_tags} fld
        LEFT JOIN
          {taxonomy_term_data} tt ON fld.field_{$type}_tags_tid  = tt.tid
        WHERE
          fld.entity_id = :nid
      ", array(':nid' => $nid));
      $all_tags = [];
      foreach ($result as $record) {
        $all_tags[] = [
          'name' => $record->name,
          'target_id' => $record->{'field_' . $type . '_tags_tid'},
        ];
      }
      $row->setSourceProperty('d7_tidTags', $all_tags);
      $log_message .= json_encode($all_tags) . ';';
    }

    if ( $type == 'blog' || $type == 'blog_blog' || $type == 'doc' ){
      $ficheros_ext_adjuntos = [];
      $result = $this->getDatabase()->query('
        SELECT
          fld.field_ficheros_externos_adjuntos_value,
          fld.field_ficheros_externos_adjuntos_revision_id
        FROM
          {field_data_field_ficheros_externos_adjuntos} fld
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));

      foreach ($result as $record) {
        if (!empty($record->field_ficheros_externos_adjuntos_value) && isset($record->field_ficheros_externos_adjuntos_revision_id)) {
          $ficheros_ext_adjuntos[] = [
            'item_id' => (int) $record->field_ficheros_externos_adjuntos_value,
          ];
        }
      }

      if (!empty($ficheros_ext_adjuntos)) {
        $row->setSourceProperty('field_ext_files_d7', $ficheros_ext_adjuntos);
        $log_message .= json_encode($row->getSourceProperty('field_ext_files_d7')) . ';';
      }

      if ( $type == 'blog' || $type == 'blog_blog' ) {
        $row->setSourceProperty('field_image_d7', $this->fetchFieldImages('blog_image', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_image_d7')) . ';';

        $row->setSourceProperty('field_ficheros_adjuntos_d7', $this->fetchFieldFiles('blog_files', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_ficheros_adjuntos_d7')) . ';';

        $values_noticia_blog = ['field_blog_date', 'field_blog_source'];
        foreach ($values_noticia_blog as $value_noticia_blog) {
          $row->setSourceProperty($value_noticia_blog . '_d7', $this->fetchFieldValue($value_noticia_blog, $nid));
          $log_message .= json_encode($row->getSourceProperty($value_noticia_blog . '_d7')) . ';';
        }

      } elseif ($type == 'doc') {
        $row->setSourceProperty('field_image_d7', $this->fetchFieldImages('doc_image', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_image_d7')) . ';'; 

        $row->setSourceProperty('field_ficheros_adjuntos_d7', $this->fetchFieldFiles('doc_file', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_ficheros_adjuntos_d7')) . ';';

        $taxonomies_doc = ['field_doc_tx_type', 'field_doc_tx_audience', 'field_doc_tx_autor'];
        foreach ($taxonomies_doc as $taxonomy_doc) {
          $row->setSourceProperty($taxonomy_doc . '_d7', $this->fetchFieldTaxonomy($taxonomy_doc, $nid));
          $log_message .= json_encode($row->getSourceProperty($taxonomy_doc . '_d7')) . ';';
        }

        $values_doc = ['field_doc_date', 'field_doc_youtube'];
        foreach ($values_doc as $value_doc) {
          $row->setSourceProperty($value_doc . '_d7', $this->fetchFieldValue($value_doc, $nid));
          $log_message .= json_encode($row->getSourceProperty($value_doc . '_d7')) . ';';
        }
      }
    
    } elseif ( $type == 'app' || $type == 'success' || $type == 'initiative' || $type == 'request' ) {
      $result = $this->getDatabase()->query('
        SELECT
          fld.field_sender_email_email
        FROM
          {field_data_field_sender_email} fld
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));
      foreach ($result as $record) {
        $row->setSourceProperty('field_sender_email_d7', $record->field_sender_email_email);
        $log_message .= json_encode($row->getSourceProperty('field_sender_email_d7')) . ';';
      }

      $values_contact = ['field_sender_name', 'field_sender_phone', 'field_sender_terms_of_use'];
      foreach ($values_contact as $value_contact) {
        $row->setSourceProperty($value_contact . '_d7', $this->fetchFieldValue($value_contact, $nid));
        $log_message .= json_encode($row->getSourceProperty($value_contact . '_d7')) . ';';
      }

      if ( $type == 'app' ) {
        $row->setSourceProperty('field_image_d7', $this->fetchFieldImages('app_image', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_image_d7')) . ';';

        $row->setSourceProperty('field_capturas_de_pantalla_d7', $this->fetchFieldImages('app_images', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_capturas_de_pantalla_d7')) . ';';

        $row->setSourceProperty('field_url_catalogo_d7', $this->fetchFieldURL('app_dataset_url', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_url_catalogo_d7')) . ';';

        $row->setSourceProperty('field_url_aplicacion_d7', $this->fetchFieldURL('app_url', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_url_aplicacion_d7')) . ';';

        $row->setSourceProperty('field_categoria_d7', $this->fetchFieldTaxonomy('field_app_category', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_categoria_d7')) . ';';

        $row->setSourceProperty('field_app_platform_d7', $this->fetchFieldTaxonomy('field_app_platform', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_app_platform_d7')) . ';';

        $values_app = ['field_app_publish_date', 'field_app_update_date', 'field_app_developer', 'field_app_license',
                     'field_sender_company', 'field_sender_show_data'];
        foreach ($values_app as $value_app) {
          $row->setSourceProperty($value_app . '_d7', $this->fetchFieldValue($value_app, $nid));
          $log_message .= json_encode($row->getSourceProperty($value_app . '_d7')) . ';';
        }

      } elseif ( $type == 'success' ) {
        $row->setSourceProperty('field_image_d7', $this->fetchFieldImages('success_image', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_image_d7')) . ';'; 

        $row->setSourceProperty('field_categoria_d7', $this->fetchFieldTaxonomy('field_success_category', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_categoria_d7')) . ';';

        $row->setSourceProperty('field_url_catalogo_d7', $this->fetchFieldURL('success_catalog', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_url_catalogo_d7')) . ';';

        $row->setSourceProperty('field_pagina_web_de_la_empresa_d7', $this->fetchFieldURL('success_url', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_pagina_web_de_la_empresa_d7')) . ';';

        $size[] = ['company_size' => $this->fetchFieldValue('field_company_size', $nid)];
        $row->setSourceProperty('field_tamano_de_la_empresa_d7', $size);
        $log_message .= json_encode($row->getSourceProperty('field_tamano_de_la_empresa_d7')) . ';';

        $values_format_empresa = ['field_products_services', 'field_success_key', 'field_success_increase', 'field_future_plans', 'field_used_data', 'field_clients', 'field_marketing_model'];
        foreach ($values_format_empresa as $value_format_empresa) {
          $row->setSourceProperty($value_format_empresa . '_d7', $this->fetchFieldValueFormat($value_format_empresa, $nid));
          $log_message .= json_encode($row->getSourceProperty($value_format_empresa . '_d7')) . ';';
        }

        $values_empresa = ['field_success_launch_date', 'field_offices', 'field_headquarters', 'field_internationality_grade', 'field_more_information', 'field_sender_show_data'];
        foreach ($values_empresa as $value_empresa) {
          $row->setSourceProperty($value_empresa . '_d7', $this->fetchFieldValue($value_empresa, $nid));
          $log_message .= json_encode($row->getSourceProperty($value_empresa . '_d7')) . ';';
        }
      
      } elseif ( $type == 'initiative' ) {
        $result = $this->getDatabase()->query('
        SELECT
          fld.field_geoposition_geom,
          fld.field_geoposition_geo_type,
          fld.field_geoposition_lat,
          fld.field_geoposition_lon,
          fld.field_geoposition_left,
          fld.field_geoposition_top,
          fld.field_geoposition_right,
          fld.field_geoposition_bottom,
          fld.field_geoposition_geohash
        FROM
          {field_data_field_geoposition} fld
        WHERE
          fld.entity_id = :nid
        ', array(':nid' => $nid));
        $geoposition = [];
        foreach ($result as $record) {
          $geoposition[] = [
            'value' => $record->field_geoposition_geom,
            'geo_type' => $record->field_geoposition_geo_type,
            'lat' => $record->field_geoposition_lat,
            'lon' => $record->field_geoposition_lon,
            'left' => $record->field_geoposition_left,
            'top' => $record->field_geoposition_top,
            'right' => $record->field_geoposition_right,
            'bottom' => $record->field_geoposition_bottom,
            'geohash' => $record->field_geoposition_geohash,
          ];
        }
        $row->setSourceProperty('field_geoposition_d7', $geoposition);
        $log_message .= json_encode($geoposition) . ';';

        $urls_iniciativa = ['initiative_channel', 'initiative_catalog', 'initiative_link', 'enlace_al_cat_logo_de_dato', 
                            'initiative_strategy', 'initiative_resources'];
        foreach ($urls_iniciativa as $url_iniciativa) {
          $row->setSourceProperty($url_iniciativa . '_d7', $this->fetchFieldURL($url_iniciativa, $nid));
          $log_message .= json_encode($row->getSourceProperty($url_iniciativa . '_d7')) . ';';
        }

        $values_iniciativa = ['field_initiative_license', 'field_initiative_organization', 'field_initiative_collaborate'];
        foreach ($values_iniciativa as $value_iniciativa) {
          $row->setSourceProperty($value_iniciativa . '_d7', $this->fetchFieldValue($value_iniciativa, $nid));
          $log_message .= json_encode($row->getSourceProperty($value_iniciativa . '_d7')) . ';';
        }

        $taxonomies_iniciativa = ['field_initiative_category', 'field_initiative_admin_level'];
        foreach ($taxonomies_iniciativa as $taxonomy_iniciativa) {
          $row->setSourceProperty($taxonomy_iniciativa . '_d7', $this->fetchFieldTaxonomy($taxonomy_iniciativa, $nid));
          $log_message .= json_encode($row->getSourceProperty($taxonomy_iniciativa . '_d7')) . ';';
        }
      }

    elseif ( $type == 'request' ) {

      $row->setSourceProperty('field_categoria_d7', $this->fetchFieldTaxonomy('field_request_category', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_categoria_d7')) . ';';

      if ( $this->fetchFieldTaxonomy('field_request_tx_status', $nid) == 'Recibido' && $row->getSourceProperty('moderation_state') == 'draft' ) {
        $row->setSourceProperty('field_estado_d7', 'Borrador');
      } elseif ( $this->fetchFieldTaxonomy('field_request_tx_status', $nid) == 'Recibido' && $row->getSourceProperty('moderation_state') == 'published' ) {
        $row->setSourceProperty('field_estado_d7', 'Recibido');
      } else {
        $row->setSourceProperty('field_estado_d7', $this->fetchFieldTaxonomy('field_request_tx_status', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_estado_d7')) . ';';
      }
      
      $row->setSourceProperty('field_request_answer_d7', $this->fetchFieldValueFormat('field_request_answer', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_request_answer_d7')) . ';';

      $row->setSourceProperty('request_dataset_url_d7', $this->fetchFieldURL('request_dataset_url', $nid));
      $log_message .= json_encode($row->getSourceProperty('request_dataset_url_d7')) . ';';

      if ( $this->fetchFieldValue('field_number_subscriptors', $nid) == null ){
        $row->setSourceProperty('field_number_subscriptors_d7', '0');
        $log_message .= json_encode($row->getSourceProperty('field_number_subscriptors_d7')) . ';';
      } else {
        $row->setSourceProperty('field_number_subscriptors_d7', $this->fetchFieldValue('field_number_subscriptors', $nid));
        $log_message .= json_encode($row->getSourceProperty('field_number_subscriptors_d7')) . ';';
      }

      $values_request = ['field_sender_company', 'field_sender_show_data', 'field_request_long_reason', 'field_request_long_benefit',
                        'field_request_publisher'];
      foreach ($values_request as $value_request) {
        $row->setSourceProperty($value_request . '_d7', $this->fetchFieldValue($value_request, $nid));
        $log_message .= json_encode($row->getSourceProperty($value_request . '_d7')) . ';';
      }

      $result = $this->getDatabase()->query('
        SELECT
          fld.field_request_subscriptor_value,
          fld.field_request_subscriptor_revision_id
        FROM
          {field_data_field_request_subscriptor} fld
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));
      $solicitudes_suscripcion = [];
      foreach ($result as $record) {
        $solicitudes_suscripcion[] = [
          'item_id' => (int) $record->field_request_subscriptor_value,
        ];
      }
      $row->setSourceProperty('field_request_subscriptor_d7', $solicitudes_suscripcion);
      $log_message .= json_encode($row->getSourceProperty('field_request_subscriptor_d7')) . ';';

      $result = $this->getDatabase()->query('
        SELECT
          fld.field_organismo_pub_tid,
          tt.name
        FROM
          {field_data_field_organismo_pub} fld
        LEFT JOIN
          {taxonomy_term_data} tt ON fld.field_organismo_pub_tid  = tt.tid
        WHERE
          fld.entity_id  = :nid
      ', array(':nid' => $nid));
      foreach ($result as $record) {
        $log_message .= json_encode($record->name) . ';';
        $row->setSourceProperty('d7_tidOrganizacion', $record->field_organismo_pub_tid);
        $log_message .= json_encode($row->getSourceProperty('d7_tidOrganizacion')) . ';';
      }
    }
    } elseif ( $type == 'talk' ) {
      $row->setSourceProperty('field_image_d7', $this->fetchFieldImages('talk_image', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_image_d7')) . ';';

      $result = $this->getDatabase()->query('
        SELECT
          fld.field_talk_bio_contact_value
        FROM
          {field_data_field_talk_bio_contact} fld
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));
      $bio_contacto = [];
      foreach ($result as $record) {
        $bio_contacto[] = [
          'value' => $record->field_talk_bio_contact_value,
        ];
      }
      $row->setSourceProperty('field_bio_contacto_d7', $bio_contacto);
      $log_message .= json_encode($bio_contacto) . ';';

      $row->setSourceProperty('field_bio_pagina_web_d7', $this->fetchFieldURL('talk_bio_web', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_bio_pagina_web_d7')) . ';';

      $row->setSourceProperty('field_date_d7', $this->fetchFieldValue('field_talk_date', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_date_d7')) . ';';

      $taxonomies_entrevista = ['field_talk_bio_country', 'field_talk_bio_sector'];
      foreach ($taxonomies_entrevista as $taxonomy_entrevista) {
        $row->setSourceProperty($taxonomy_entrevista . '_d7', $this->fetchFieldTaxonomy($taxonomy_entrevista, $nid));
        $log_message .= json_encode($row->getSourceProperty($taxonomy_entrevista . '_d7')) . ';';
      }

      $values_entrevista = ['field_talk_bio_name', 'field_talk_bio_company', 'field_talk_bio_spec', 'field_talk_bio_works'];
      foreach ($values_entrevista as $value_entrevista) {
        $row->setSourceProperty($value_entrevista . '_d7', $this->fetchFieldValue($value_entrevista, $nid));
        $log_message .= json_encode($row->getSourceProperty($value_entrevista . '_d7')) . ';';
      }

    } elseif ( $type == 'faq' ) {
      $row->setSourceProperty('field_detailed_question_d7', $this->fetchFieldValue('field_detailed_question', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_detailed_question_d7')) . ';';

      $row->setSourceProperty('field_faq_category_name_d7', $this->fetchFieldTaxonomy('field_faq_category', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_faq_category_name_d7')) . ';';

    } elseif ( $type == 'event' ) {
      $row->setSourceProperty('field_date_d7', $this->fetchFieldValue('field_event_date', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_date_d7')) . ';';

      $row->setSourceProperty('field_image_d7', $this->fetchFieldImages('event_image', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_image_d7')) . ';';

    } elseif ( $type == 'speakers_aporta' ) {
      $row->setSourceProperty('field_photo_d7', $this->fetchFieldImages('photo', $nid));
      $log_message .= json_encode($row->getSourceProperty('field_photo_d7')) . ';';

      $values_ponentes = ['field_position', 'field_departament'];
      foreach ($values_ponentes as $value_ponentes) {
        $row->setSourceProperty($value_ponentes . '_d7', $this->fetchFieldValue($value_ponentes, $nid));
        $log_message .= json_encode($row->getSourceProperty($value_ponentes . '_d7')) . ';';
      }

      $urls_ponentes = ['twitter', 'linkedin'];
      foreach ($urls_ponentes as $url_ponentes) {
        $row->setSourceProperty($url_ponentes . '_d7', $this->fetchFieldURL($url_ponentes, $nid));
        $log_message .= json_encode($row->getSourceProperty($url_ponentes . '_d7')) . ';';
      }
    }
 
    return parent::prepareRow($row);
  }
 
 
  /**
   * {@inheritdoc}
   */
  public function bundleMigrationRequired() {
    return TRUE;
  }
 
  /**
   * {@inheritdoc}
   */
  public function entityTypeId() {
    return 'node';
  }
 
  /**
   *
   * @return array
   */
  protected function baseNodesFields() {
    $node_fields = array(
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Version ID'),
      'type' => $this->t('Type'),
      'title' => $this->t('Title'),
      'format' => $this->t('Format'),
      'teaser' => $this->t('Teaser'),
      'uid' => $this->t('Authored by (uid)'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
      'status' => $this->t('Published'),
      'promote' => $this->t('Promoted to front page'),
      'sticky' => $this->t('Sticky at top of lists'),
      'language' => $this->t('Language (fr, en, ...)'),
    );
    return $node_fields;
  }
 
}
