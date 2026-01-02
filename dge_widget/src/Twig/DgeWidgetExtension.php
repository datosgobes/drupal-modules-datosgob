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

namespace Drupal\dge_widget\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class DgeWidgetExtension extends AbstractExtension {

  public function getFunctions() {
    return [
      new TwigFunction('get_json_value', [$this, 'getJsonValue']),
      new TwigFunction('get_json_key_value', [$this, 'getJsonKeyValue']),
      new TwigFunction('get_json_date_value', [$this, 'getJsonDateValue']),
      new TwigFunction('get_json_multiple_value', [$this, 'getJsonMultipleValue']),
      new TwigFunction('get_json_array_value', [$this, 'getJsonArrayValue']),
      new TwigFunction('get_json_array_key_value', [$this, 'getJsonArrayKeyValue']),
      new TwigFunction('get_json_label', [$this, 'getJsonLabel']),
      new TwigFunction('get_frequency_label', [$this, 'getFrequencyLabel']),
    ];
  }



  public function getJsonValue($key, $array) {

    return function_exists('get_json_value') ? get_json_value($key, $array) : NULL;
  }

  public function getJsonKeyValue($field_name, $field_key, $json_data) {
   
    return function_exists('get_json_key_value') ? get_json_key_value($field_name, $field_key, $json_data) : NULL;
  }

  public function getJsonDateValue($field_name, $json_data, $format) {
    
    return function_exists('get_json_date_value') ? get_json_date_value($field_name, $json_data, $format) : NULL;
  }

  public function getJsonMultipleValue($field_name, $json_data) {
  
    return function_exists('get_json_multiple_value') ? get_json_multiple_value($field_name, $json_data) : NULL;
  }

  public function getJsonArrayValue($field_name, $field_key, $json_data, $label_data) {
    
    return function_exists('get_json_array_value') ? get_json_array_value($field_name, $field_key, $json_data, $label_data) : NULL;
  }

  public function getJsonArrayKeyValue($field_name, $field_key, $json_data) {
    
    return function_exists('get_json_array_key_value') ? get_json_array_key_value($field_name, $field_key, $json_data) : NULL;
  }

  public function getJsonLabel($field_name, $field_key, $field_value, $label_data) {
    
    return function_exists('get_json_label') ? get_json_label($field_name, $field_key, $field_value, $label_data) : NULL;
  }

  public function getFrequencyLabel($json_data, $label_data) {
    
    return function_exists('get_frequency_label') ? get_frequency_label($json_data, $label_data) : NULL;
  }
}
