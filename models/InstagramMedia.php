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

	public function id($entity) {
		return $entity->raw['id'];
	}

	public function author($entity) {
		return $entity->raw['user']['username'];
	}

	public function title($entity) {
		return $entity->raw['caption']['text'];
	}

	public function url($entity) {
		return $entity->raw['link'];
	}

	public function body($entity, array $options = []) {
		$options += [
			'html' => true
		];
		$cover = $entity->cover();

		if ($cover && $cover['type'] === 'image') {
			return $options['html'] ? "<img src=\"{$cover['url']}\">" : $cover['url'];
		}
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
	public function cover($entity) {
		if ($entity->raw['type'] === 'image') {
			return [
				'type' => 'image',
				'url' => str_replace('http://', 'https://', $entity->raw['images']['standard_resolution']['url'])
			];
		}
		if ($entity->raw['type'] === 'video') {
			return [
				'type' => 'video',
				'url' => str_replace('http://', 'https://', $entity->raw['videos']['standard_resolution']['url'])
			];
		}
	}
}

?>