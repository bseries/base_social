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

class VimeoVideos extends \base_core\models\Base {

	protected $_meta = [
		'connection' => false
	];

	public function id($entity) {
		preg_match('#/([0-9]+)$#', $entity->raw['uri'], $matches);
		return $matches[1];
	}

	public function author($entity) {
		return $entity->raw['user']['name'];
	}

	public function title($entity) {
		return $entity->raw['name'];
	}

	public function url($entity) {
		return $entity->raw['link'];
	}

	public function body($entity, array $options = []) {
		$options += [
			'html' => true // currently noop
		];
		return $entity->raw['description'];
	}

	public function published($entity) {
		return date('Y-m-d H:i:s', strtotime($entity->raw['created_time']));
	}

	public function tags($entity) {
		return Set::extract($entity->raw, '/tags/tag');
	}

	// Vimeo Basic accounts do not expose raw video links. Embedding via ID must be used.
	// FIXME Find a way to support external media URLs.
	public function cover($entity, array $options = []) {
		$options += ['internal' => false];

		if (!$options['internal']) {
			return null;
		}
		return [
			'type' => 'video',
			'url' => 'vimeo://' . $entity->id(),
			'title' => $entity->title()
		];
	}

	public function media($entity) {
		return [];
	}

	/* Deprecated / BC */

	public function excerpt($entity) {
		trigger_error(
			'Vimeo excerpts are deprecated, manually truncate the body field.',
			E_USER_DEPRECATED
		);
		return Textual::limit($entity->raw['description'], 20);
	}
}

?>