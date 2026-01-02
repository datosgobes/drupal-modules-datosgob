<?php
/**
  * Copyright (C) 2025 Entidad Pública Empresarial Red.es
  *
  * This file is part of "dge_social_media_share (datos.gob.es)".
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

namespace Drupal\dge_social_media_share\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\social_media\Event\SocialMediaEvent;

class DgeSocialMediaShareSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    $events['social_media.add_more_link'][] = ['addCopyToClipboard', 0];
    return $events;
  }

  public function addCopyToClipboard(SocialMediaEvent $event) {
    $element = $event->getElement();
    $element['copy'] = 'Copiar enlace';
    $event->setElement($element);
  }
}