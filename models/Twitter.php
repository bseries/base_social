<?php
/**
 * Copyright 2014 David Persson. All rights reserved.
 * Copyright 2016 Atelier Disko. All rights reserved.
 *
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
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