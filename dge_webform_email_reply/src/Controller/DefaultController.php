<?php 
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_webform_email_reply (datos.gob.es)".
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

 /* @file
 * Contains \Drupal\dge_webform_email_reply\Controller\DefaultController.
 */

namespace Drupal\dge_webform_email_reply\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\webform\WebformRequestInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Datetime\DateFormatterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class DefaultController extends ControllerBase {
  /**
   * A webform submission.
   *
   * @var \Drupal\webform\WebformSubmissionInterface
   */
  protected $webformSubmission;

  /**
   * Webform request handler.
   *
   * @var \Drupal\webform\WebformRequestInterface
   */
  protected $requestHandler;

  /**
   * Current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $current_user;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a WebformResultsResendForm object.
   *
   * @param \Drupal\webform\WebformRequestInterface $request_handler
   *   The webform request handler.
   */
  public function __construct(WebformRequestInterface $request_handler, AccountInterface $current_user, DateFormatterInterface $date_formatter) {
    $this->requestHandler = $request_handler;
    $this->currentUser = $current_user;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('webform.request'),
      $container->get('current_user'),
      $container->get('date.formatter')
    );
  }

  public static function checkReplyAccess(AccountInterface $account) {
    if ($account->hasPermission('send email replies to contact webforms')) {
      return AccessResult::allowed();
    }
    return AccessResult::forbidden();
  }

  public function dge_webform_email_reply_previous($webform, $webform_submission) {

    $header = [
      ['data' => t('#'), 'field' => 'eid', 'sort' => 'desc',],
      ['data' => t('Sent by')],
      ['data' => t('Sent at'), 'field' => 'replied', ],
      ['data' => t('Message')],
    ];

    $replies = dge_webform_email_reply_get_replies($webform, $webform_submission);
    $rows = [];
    foreach ($replies as $key => $reply) {
      $row = [];
      $row['eid'] = ++$key;
      $row['from'] = $reply->from_address;
      $row['replied'] = $this->dateFormatter->format($reply->replied, 'short');
      $row['message'] = $reply->message;
      $rows[] = $row;
    }
    $output = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];
    $output['pager'] = ['#type' => 'pager'];
    return $output;
  }

}