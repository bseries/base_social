<?php
/**
 * Base Social
 *
 * Copyright (c) 2014 Atelier Disko - All rights reserved.
 *
 * This software is proprietary and confidential. Redistribution
 * not permitted. Unless required by applicable law or agreed to
 * in writing, software distributed on an "AS IS" BASIS, WITHOUT-
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 */

namespace base_social\models;

use Guzzle\Http\Client;
use base_social\models\VimeoVideos;

// Currently doesn't need any credential details as
// we access just the public API.
class Vimeo {

	public static function first($id) {
		if (!$result = static::_api('video/' . $id)) {
			return false;
		}
		return VimeoVideos::create(['raw' => array_shift($result)]);
	}

	public static function latest($username) {
		$results = static::_api($username . '/videos');
		return VimeoVideos::create(['raw' => array_shift($results)]);
	}

	public static function all($username) {
		if (!$results = static::_api($username . '/videos')) {
			return [];
		};
		foreach ($results as &$result) {
			$result = VimeoVideos::create(['raw' => $result]);
		}
		return $results;
	}

	protected static function _api($url) {
		$client = new Client('https://vimeo.com/api/v2/');
		$request = $client->get($url . '.json');

		try {
			$response = $request->send();
		} catch (\Exception $e) {
			return false;
		}
		return json_decode($response->getBody(), true);
	}
}

?>