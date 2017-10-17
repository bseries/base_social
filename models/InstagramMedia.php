<?php
/**
 * Copyright 2014 David Persson. All rights reserved.
 * Copyright 2016 Atelier Disko. All rights reserved.
 *
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
 */

namespace base_social\models;

use textual\Modulation as Textual;
use lithium\util\Set;

// Needs raw data from the Instagram API.
class InstagramMedia extends \base_core\models\Base {

	protected $_meta = [
		'connection' => false
	];

	// Works with shortcodes instead of IDs. These have proven
	// to be more versatile.
	public function id($entity) {
		preg_match('#instagr(\.am|am\.com)/p/([^/]+)/?#i', $entity->raw['link'], $matches);
		return $matches[2];
	}

	public function author($entity) {
		return $entity->raw['user']['username'];
	}

	public function title($entity) {
		return Textual::limit($entity->raw['caption']['text'], 20);
	}

	public function url($entity) {
		return $entity->raw['link'];
	}

	public function body($entity, array $options = []) {
		return $entity->raw['caption']['text'];
	}

	public function published($entity) {
		return date('Y-m-d H:i:s', $entity->raw['created_time']);
	}

	public function tags($entity) {
		return $entity->raw['tags'];
	}

	// Chooses highest resolution possible.
	public function cover($entity, array $options = []) {
		$options += ['internal' => false];
		$result = [
			'type' => $entity->raw['type'],
			'title' => $entity->title()
		];

		if ($options['internal']) {
			return ['url' => 'instagram://' . $entity->id()] + $result;
		}
		if (!$url = static::_url($entity->raw)) {
			return false;
		}
		return compact('url') + $result;
	}

	public function media($entity, array $options = []) {
		$options += ['internal' => false];
		$results = [];

		if ($entity->raw['type'] !== 'carousel') {
			return $results;
		}
		foreach ($entity->raw['carousel_media'] as $medium) {
			if (!$url = static::_url($medium)) {
				continue;
			}
			$results[] = [
				'type' => 'image',
				'title' => $entity->title(),
				'url' => $url
			];
		}
		return $results;
	}

	// Selects (and upgrades) URL to resource from a media array as returned
	// by the service.
	//
	// Enforce HTTPS, old instagram resources may still have HTTP protocol, but
	// service supports HTTPS. We don't know if this is embedded in HTTPS pages
	// or not. So we ensure highest standart possible to get arround broken page
	// errors.
	//
	// Chooses highest resolution possible.
	protected static function _url(array $item) {
		switch ($item['type']) {
			case 'image':
			case 'carousel': // Allow to pick a cover image.
				return str_replace(
					'http://', 'https://', $item['images']['standard_resolution']['url']
				);
			break;
			case 'video':
				return str_replace(
					'http://', 'https://', $item['videos']['standard_resolution']['url']
				);
				break;
			default:
				return false;
		}
	}
}

?>