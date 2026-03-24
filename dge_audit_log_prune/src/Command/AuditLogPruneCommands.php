<?php

/**
  * Copyright (C) 2026 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_audit_log_prune (datos.gob.es)".
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

namespace Drupal\dge_audit_log_prune\Command;

use Drupal\Core\Database\Connection;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drush\Commands\DrushCommands;


class AuditLogPruneCommands extends DrushCommands {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   */
  public function __construct(Connection $database, TimeInterface $time, LoggerChannelFactoryInterface $logger_factory) {
    parent::__construct();
    $this->database = $database;
    $this->time = $time;
    $this->loggerFactory = $logger_factory;
  }

  /**
   * 
   *
   * @command dge_audit_log:prune
   * @option days Número de días de registros a mantener.
   * @option dry-run Ejecutar sin eliminar registros (solo mostrar cuántos se eliminarían).
   * @usage dge_audit_log:prune
   *   Elimina registros más antiguos de 90 días.
   * @usage dge_audit_log:prune --days=30
   *   Elimina registros más antiguos de 30 días.
   * @usage dge_audit_log:prune --dry-run
   *   Muestra cuántos registros se eliminarían sin ejecutar la eliminación.
   * @aliases alp
   */
  public function pruneAuditLogs($options = ['days' => 90, 'dry-run' => FALSE]) {
    $days = (int) $options['days'];
    $dry_run = $options['dry-run'];

    $timestamp_ago = $this->time->getRequestTime() - ($days * 24 * 60 * 60);
    $date_string = date('Y-m-d H:i:s', $timestamp_ago);

    $this->output()->writeln("Procesando registros más antiguos de {$days} días (antes de {$date_string})...");

    try {
      $count_changelog = $this->database->select('entity_changelog_entry', 'e')
        ->condition('timestamp', $timestamp_ago, '<')
        ->countQuery()
        ->execute()
        ->fetchField();

      if ($dry_run) {
        $this->output()->writeln("  [DRY-RUN] Se eliminarían {$count_changelog} registros de entity_changelog_entry.");
      }
      else {
        $deleted_changelog = $this->database->delete('entity_changelog_entry')
          ->condition('timestamp', $timestamp_ago, '<')
          ->execute();

        if ($deleted_changelog > 0) {
          $this->logger()->success("Eliminados {$deleted_changelog} registros de entity_changelog_entry.");
          $this->loggerFactory->get('audit_log_prune')->info('Eliminados @count registros antiguos de entity_changelog_entry.', [
            '@count' => $deleted_changelog,
          ]);
        }
      }

      
      $count_config = $this->database->select('config_log', 'c')
        ->condition('created', $timestamp_ago, '<')
        ->countQuery()
        ->execute()
        ->fetchField();

      if ($dry_run) {
        $this->output()->writeln("  [DRY-RUN] Se eliminarían {$count_config} registros de config_log.");
      }
      else {
        $deleted_config = $this->database->delete('config_log')
          ->condition('created', $timestamp_ago, '<')
          ->execute();

        if ($deleted_config > 0) {
          $this->logger()->success("Eliminados {$deleted_config} registros de config_log.");
          $this->loggerFactory->get('audit_log_prune')->info('Eliminados @count registros antiguos de config_log.', [
            '@count' => $deleted_config,
          ]);
        }
      }

      $total = $count_changelog + $count_config;
      if ($dry_run) {
        $this->logger()->success("Total de registros que se eliminarían: {$total}");
      }
      else {
        if ($total == 0) {
          $this->logger()->success('No se encontraron registros para eliminar.');
          $this->loggerFactory->get('audit_log_prune')->info('No se han eliminado registros.');
        }
        else {
          $this->logger()->success("Total de registros eliminados: {$total}");
        }
      }

    }
    catch (\Exception $e) {
      $this->logger()->error('Error al procesar registros: ' . $e->getMessage());
      $this->loggerFactory->get('audit_log_prune')->error('Error al eliminar registros antiguos: @message', [
        '@message' => $e->getMessage(),
      ]);
      return DrushCommands::EXIT_FAILURE;
    }

    return DrushCommands::EXIT_SUCCESS;
  }

}
