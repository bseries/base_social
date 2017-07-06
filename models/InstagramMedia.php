<?php
/**
 * Base Social
 *
 * Copyright (c) 2014 Atelier Disko - All rights reserved.
 *
 * Licensed under the AD General Software License v1.
 *
 * This software is proprietary and confidential. Redistribution
 * not permitted. Unless required by applicable law or agreed to
 * in writing, software distributed on an "AS IS" BASIS, WITHOUT-
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *
 * You should have received a copy of the AD General Software
 * License. If not, see http://atelierdisko.de/licenses.
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

	// Enforce HTTPS, old instagram resources may still have HTTP protocol, but
	// service supports HTTPS. We don't know if this is embedded in HTTPS pages
	// or not. So we ensure highest standart possible to get arround broken page
	// errors.
	//
	// Chooses highest resolution possible.
	public function cover($entity, array $options = []) {
		$options += ['internal' => false];

		$result = [
			'type' => $entity->raw['type'],
			'title' => $entity->title()
		];

		if ($options['internal']) {
			$result['url'] = 'instagram://' . $entity->id();
			return $result;
		}

		switch ($entity->raw['type']) {
			case 'image':
			case 'carousel':
				$result['url'] = str_replace(
					'http://', 'https://', $entity->raw['images']['standard_resolution']['url']
				);
			break;
			case 'video':
				$result['url'] = str_replace(
					'http://', 'https://', $entity->raw['videos']['standard_resolution']['url']
				);
				break;
			default:
				return false;
		}
		return $result;
	}

	public function media($entity, array $options = []) {
		return [];
	}
}

?>