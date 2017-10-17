<?php
/**
 * Copyright 2014 David Persson. All rights reserved.
 * Copyright 2016 Atelier Disko. All rights reserved.
 *
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
 */

namespace base_social\models;

use GuzzleHttp\Client;
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
		$client = new Client(['base_uri' => 'https://vimeo.com/api/v2/']);

		try {
			$response = $client->request('GET', $url . '.json');
		} catch (\Exception $e) {
			return false;
		}
		return json_decode($response->getBody(), true);
	}
}

?>