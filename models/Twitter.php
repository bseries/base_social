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

use Exception;
use TwitterOAuth\Auth\SingleUserAuth as ClientAuth;
use TwitterOAuth\Serializer\ArraySerializer as ClientSerializer;
use base_social\models\TwitterTweets;

class Twitter {

	// https://dev.twitter.com/rest/reference/get/statuses/user_timeline
	public static function allByAuthor($name, array $config) {
		return static::_api('/statuses/user_timeline', $config, [
			'screen_name' => $name,
			'include_rts' => true,
			'exclude_replies' => false
		]);
	}

	public static function allByTag($name, array $config) {
		return static::_api('/search/tweets', $config, [
			'q' => '#' . $search['tag']
		]);
	}

	public static function search($q, array $config) {
		return static::_api('/search/tweets', $config, [
			'q' => $q
		]);
	}

	protected static function _api($url, array $config, array $params = []) {
		$connection = new ClientAuth([
			'consumer_key' => $config['consumerKey'],
			'consumer_secret' => $config['consumerSecret'],
			'oauth_token' => $config['accessToken'],
			'oauth_token_secret' => $config['accessTokenSecret']
		], new ClientSerializer());

		$data = $connection->get($url, $params);

		$results = [];
		foreach ($data as $result) {
			$results[] = TwitterTweets::create([
				'raw' => $result
			]);
		}
		return $results;
	}
}

?>