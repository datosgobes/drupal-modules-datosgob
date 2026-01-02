<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_ckan_blocks (datos.gob.es)".
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

namespace Drupal\dge_ckan_blocks\Service;
include_once DRUPAL_ROOT . '/modules/custom/dge_ckan/dge_ckan_json_label.inc';

use DateTime;
use DateTimeZone;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Entity\EntityTypeManagerInterface;


class CkanService {

    public function packageSearch($q , $fq , $rows, $start, $sort) {
        if (!function_exists('dge_ckan_init_class')) {
            throw new \Exception('La función initializeCkanClass no está disponible. Asegúrate de que el módulo dge_ckan está habilitado.');
        }
        $ckan = dge_ckan_init_class();

        $response = $ckan->request('package_search', $q, $fq, $rows, $start, $sort);

        $packages = [];
        if (isset($response['success']) && $response['success']) {
            foreach ($response['result']['results'] as $result) {
                $date = DateTime::createFromFormat('Y-m-d\TH:i:s.u', $result['metadata_created'], new DateTimeZone('UTC'));
                $date->setTimezone(new DateTimeZone('Europe/Madrid'));

                $publisher_id = $result['publisher'] ?? NULL;

                $taxonomy_term = $this->getTermByCkanOrganizationId($publisher_id);

                $packages[$result['id']] = [
                'date' => $date,
                'title' => $this->getTranslatedField($result['title_translated'])['value'],
                'publisher' => $taxonomy_term->label(),
                'organization' => $result['organization']['title'],
                'description' => $this->getTranslatedField($result['description'])['value'],
                'url' => $this->getCkanUrl($result['name']),
                'formats' => $this->getResourceFormats($result),
                ];
            }
        }

        return $packages;
    }


    protected function getTermByCkanOrganizationId($ckan_id) {
        if (!$ckan_id) {
            return NULL;
        }

        $terms = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties([
                'vid' => 'organizaciones', 
                'field_ckan_organization_id' => $ckan_id,
            ]);

        return !empty($terms) ? reset($terms) : NULL;
    }

    protected function getTranslatedField($value) {
        if (is_array($value)) {
            $language_manager = \Drupal::service('language_manager');
            $currentLanguage = $language_manager->getCurrentLanguage()->getId();

            if (!empty($value[$currentLanguage])) {
                return [
                'value' => $value[$currentLanguage],
                'language' => $currentLanguage,
                ];
            }

            foreach (array_keys($language_manager->getLanguages()) as $languageKey) {
                if (!empty($value[$languageKey])) {
                    return [
                        'value' => $value[$languageKey],
                        'language' => $languageKey,
                    ];
                }
            }
        }
        return null;
    }

    protected function getCkanUrl($name) {
        return \Drupal::config('dge_ckan.config')->get('ckan_url') . '/dataset/' . $name;
    }


    protected function getResourceFormats(array $dataset) {
        $resources = [];
        if (isset($dataset['resources']) && is_array($dataset['resources'])) {
            foreach ($dataset['resources'] as $resource) {
                $resources[] = $this->getFormatLabel($resource['format'], "");
            }
        }
        return array_unique($resources);
    }

    protected function getFormatLabel($format, $definition) {
        $label = $format;
    
        if (isset($definition->json_data['resource_fields'])) {
            foreach ($definition->json_data['resource_fields'] as $field) {
                if ($field['field_name'] == 'format') {
                    foreach ($field['choices'] as $file_format) {
                        if ($file_format['value'] == $format) {
                            $label = $file_format['label'];
                            break;
                        }
                    }
                }
            }
        }
        return $label;
    }
}
