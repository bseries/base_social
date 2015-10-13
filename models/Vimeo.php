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