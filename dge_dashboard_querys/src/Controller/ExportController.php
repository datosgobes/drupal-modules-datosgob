<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_dashboard_querys (datos.gob.es)".
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

namespace Drupal\dge_dashboard_querys\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\views\Views;
use Drupal\Component\Serialization\Json;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExportController extends ControllerBase {


    public function exportToCsvByUser($route) {

        try {
            
            $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
            $ckan_user_id = $user->get('field_ckan_user_id')->value;
            
            $query = ['ckan_user_id' => $ckan_user_id];
            $url = $url = \Drupal::config('dge_ckan.config')->get('ckan_url_internal'). '/' . 'dashboard/' . $route ;
            $url_with_param = $url . '?' . http_build_query($query);
            
            $response = \Drupal::httpClient()->get($url_with_param, [
                'headers' => [
                    'Authorization' => \Drupal::config('dge_ckan.config')->get('ckan_api_key'),
                ],
            ]);

        } catch (\Exception $e) {
            \Drupal::logger('dge_dashboard_querys')->error('Error api ckan conection: @error', ['@error' => $e->getMessage()]);
            return [];
        }
        
        return $response;
    }


    private function exportToCsvPublic($name_csv) {
        try {     
            $current_date = date("Y-m-d");
            $url =\Drupal::config('dge_ckan.config')->get('ckan_url_internal') .'/'  . 'dashboard-csv/' .$current_date . $name_csv;
            $response = \Drupal::httpClient()->get($url);

        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                $previous_date = date('Y-m-d', strtotime($current_date . ' -1 day'));
                
                $url = \Drupal::config('dge_ckan.config')->get('ckan_url_internal') .'/'  . 'dashboard-csv/' .$previous_date . $name_csv;
                
                try {
                    $response = \Drupal::httpClient()->get($url);
                    
                    return $response;
                } catch (\Exception $e) {
                    \Drupal::logger('dge_dashboard_querys')->error('No se pudo obtener el archivo para los días @fecha1 ni @fecha2', ['@fecha1' => $current_date, '@fecha2' => $previous_date]);
                    return [];
                }
            } else {
                \Drupal::logger('dge_dashboard_querys')->error('Error api ckan conection: @error', ['@error' => $e->getMessage()]);
                return [];
            }
        }
        
        return $response;
    }


    public function exportToCsvDatasetEvolution() {
        return $this->exportToCsvPublic("/catalogo_datos_publico_evolucion.csv");
    }

    public function exportToCsvDatasetLevelCategoryFormat() {
        return $this->exportToCsvPublic('/catalogo_datos_por_nivel_administracion.csv');
    }

    public function exportToCsvDataseByCategory() {
        return $this->exportToCsvPublic('/catalogo_datos_por_categoria.csv');
    }

    public function exportToCsvDatasetFormatDistribution() {
        return $this->exportToCsvPublic('/catalogo_datos_por_formato_distribucion.csv');
    }

    public function exportToCsvContentType() {
        return $this->exportToCsvPublic('/contenido_publico_por_tipo.csv');
    }

    public function exportToCsvVisitEvolution() {
        return $this->exportToCsvPublic('/visitas_publico_evolucion.csv');
    }

    public function exportToCsvVisitMoreView() {
        return $this->exportToCsvPublic('/visitas_publico_mas_vistos.csv');
    }

    public function exportToCsvVisitNationalDataset() {
        return $this->exportToCsvPublic('/visitas_publico_catalogo_nacional.csv');
    }

    public function exportToCsvVisitContents() {
        return $this->exportToCsvPublic('/visitas_publico_contenido.csv');
    }

    public function exportToCsvVisitSectors() {
        return $this->exportToCsvPublic('/visitas_publico_sectores.csv');
    }

    public function exportToCsvDatasetAdminEvolution() {
        return $this->exportToCsvPublic('/catalogo_admin_evolucion_nivel_administracion.csv');
    }

    public function exportToCsvDatasetAdminOrganismos() {
        return $this->exportToCsvPublic('/catalogo_admin_organismos.csv');
    }

    public function exportToCsvDatasetAdminDistributions() {
        return $this->exportToCsvPublic('/catalogo_admin_distribuciones.csv');
    }

    public function exportToCsvVisitAdminMoreView() {
        return $this->exportToCsvPublic('/visitas_admin_mas_vistos.csv');
    }

    public function exportToCsvContentAdminCommentsReceived() {
        return $this->exportToCsvPublic('/contenidos_admin_comentarios_recibidos.csv');
    }

    public function exportToCsvPublicadoresAdminEvolution() {
        return $this->exportToCsvPublic('/publicadores_admin_evolucion.csv');
    }

    public function exportToCsvPublicadoresAdminLevelAdministration() {
        return $this->exportToCsvPublic('/publicadores_admin_nivel_administracion.csv');
    }

    public function exportToCsvAdminContentByType() {
        return $this->exportToCsvPublic('/contenido_por_tipo_administracion.csv');
    }

    public function exportToCsvDataRequestByState() {
        return $this->exportToCsvPublic('/disponibilidad_datos_por_estado.csv');
    }

    public function exportToCsvUsersByOrganism() {
        return $this->exportToCsvPublic('/usuarios_por_organismo.csv');
    }

    public function exportToCsvUsersByAdministrationLevel() {
        return $this->exportToCsvPublic('/usuarios_por_nivel_administracion.csv');
    }

    public function exportToCsvUsersByAdministrationLevelAndUpdateForm() {
        return $this->exportToCsvPublic('/publicadores_admin_nivel_administracion_forma_actualizacion.csv');
    }

}
