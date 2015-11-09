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

// Needs untruncated, untrimmed raw data from Twitter API.
class TwitterTweets extends \base_core\models\Base {

	protected $_meta = [
		'connection' => false
	];

	public function id($entity) {
		return $entity->raw['id'];
	}

	public function author($entity) {
		return $entity->raw['user']['screen_name'];
	}

	public function title($entity) {
		return Textual::limit($entity->raw['text'], 20);
	}

	public function url($entity) {
		return 'https://twitter.com/' . $entity->raw['user']['screen_name'] . '/status/' . $entity->raw['id'];
	}

	public function body($entity, array $options = []) {
		$options += [
			'html' => true
		];

		$text = $entity->raw['text'];
		$entities = $entity->raw['entities'];

		if (!$options['html']) {
			return $text;
		}

		foreach ($entities['hashtags'] as $item) {
			$text = str_replace(
				'#' . $item['text'],
				'<a class="tweet-hashtag" href="https://search.twitter.com/search?q=' . $item['text'] . '" target="new">' . '#' . $item['text'] . '</a>',
				$text
			);
		}
		foreach ($entities['urls'] as $item) {
			$text = str_replace(
				$item['url'],
				"<a class=\"tweet-url\" href=\"{$item['expanded_url']}\" target=\"new\">" . Textual::limit($item['display_url'], 21) . "</a>",
				$text
			);
		}
		foreach ($entities['user_mentions'] as $item) {
			$text = str_replace(
				'@' . $item['screen_name'],
				"<a class=\"tweet-user-mention\" href=\"https://twitter.com/{$item['screen_name']}\" target=\"new\">@{$item['screen_name']}</a>",
				$text
			);
		}

		foreach ($entity->media() as $item) {
			$text = str_replace(
				$item['url'],
				"<a class=\"tweet-media\" href=\"{$item['url']}\" target=\"new\">{$item['display_url']}</a>",
				$text
			);
		}
		return Textual::autoLink($text, ['html' => true]);
	}

	public function published($entity) {
		return date('Y-m-d H:i:s', strtotime($entity->raw['created_at']));
	}

	public function tags($entity) {
		return Set::extract($entity->raw, '/entities/hashtags/text');
	}

	public function cover($entity, array $options = []) {}

	// Currently only supports photos as it is limited by service.
	// https://dev.twitter.com/overview/api/entities-in-twitter-objects#media
	// FIXME Find a way to support internal media URLs.
	public function media($entity, array $options = []) {
		$options += ['internal' => false];
		$results = [];

		if ($options['internal']) {
			return [];
		}
		$entities = $entity->raw['entities'];

		// Twitter does not always includes this parameter in API responses.
		if (!isset($entities['media'])) {
			return $results;
		}
		foreach ($entities['media'] as $item) {
			if ($item['type'] !== 'photo') { // Future proof.
				continue;
			}
			$results[] = [
				'type' => 'image',
				'url' => $item['media_url_https'],
				'display_url' => $item['display_url']
			];
		}
		return $results;
	}

	/* Type specific methods */

	public function retweeted($entity) {
		return $entity->raw['retweeted'];
	}

	public function replied($entity) {
		return (boolean) $entity->raw['in_reply_to_status_id'];
	}

	public function faved($entity) {
		return $entity->raw['favorited'];
	}

	/* Deprecated / BC */

	public function excerpt($entity) {
		trigger_error(
			'Tweet excerpts are deprecated, use title instead.',
			E_USER_DEPRECATED
		);
		return Textual::limit($entity->raw['text'], 20);
	}
}

?>