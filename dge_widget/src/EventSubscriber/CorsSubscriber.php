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
namespace Drupal\dge_widget\EventSubscriber;

use Drupal\Core\EventSubscriber\MainContentResponseSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsSubscriber implements EventSubscriberInterface {
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['onResponse'];

    return $events;
  }

  public function onResponse(ResponseEvent $event) {
    $request = $event->getRequest();
    if ($this->isDgeWidgetPage($request)) {
      $response = $event->getResponse();
      $response->headers->set('Access-Control-Allow-Origin', '*');
      $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
      $response->headers->set('Access-Control-Allow-Credentials', 'true');
      $response->headers->set('Access-Control-Allow-Headers', 'Authorization, Origin, Content-Type, X-CSRF-Token');

      if ($request->getMethod() === 'OPTIONS') {
        $event->setResponse(new Response());
      }
    }
  }

  private function isDgeWidgetPage($request) {
    $route_name = \Drupal::routeMatch()->getRouteName();
    return in_array($route_name, ['dge_widget.widget_rss_alpha',
    'dge_widget.widget_rss_fecha', 'dge_widget.datos_widget_rss', 'dge_widget.widget_rss']);
  }

}
