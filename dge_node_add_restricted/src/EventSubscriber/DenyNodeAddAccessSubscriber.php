<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_node_add_restricted (datos.gob.es)".
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

namespace Drupal\dge_node_add_restricted\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Url;

class DenyNodeAddAccessSubscriber implements EventSubscriberInterface {

  protected $currentUser;
  protected $adminContext;

  public function __construct(AccountInterface $current_user, AdminContext $admin_context) {
    $this->currentUser = $current_user;
    $this->adminContext = $admin_context;
  }

  public function onKernelRequest(RequestEvent $event) {
    $request = $event->getRequest();
    $path = $request->getPathInfo();
    $route_name = $request->attributes->get('_route');
    if ($route_name === 'node.add_page' && $this->currentUser->isAnonymous()) {
      $url = Url::fromRoute('<front>')->toString();
      $event->setResponse(new RedirectResponse($url));
    }
  }

  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['onKernelRequest', 30],
    ];
  }
}
