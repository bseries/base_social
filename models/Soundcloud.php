<?php
/**
 * Base Social
 *
 * Copyright (c) 2016 Atelier Disko - All rights reserved.
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
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use lithium\analysis\Logger;
use lithium\storage\Cache;

// This implements access to the public SC API, the public API
// does not require an oAuth dance.
class Soundcloud {

	// Resolves a user/track URL (i.e. matas/hobnotropic) to a SC ID.
	public static function resolve($path, array $config) {
		$ids = Cache::read('default', 'soundcloud_resolved_ids') ?: [];

		if (isset($ids[$path])) {
			return $ids[$path];
		}
		$result = static::_api('resolve', $config, [
			'url' => 'http://soundcloud.com/' . $path
		]);
		if (!$result) {
			return false;
		}
		$ids[$path] = $result['id'];
		Cache::write('default', 'soundcloud_resolved_ids', $ids);
		return $result['id'];
	}

	protected static function _api($url, array $config, array $params = []) {
		$client = new Client(['base_uri' => 'https://api.soundcloud.com/']);

		try {
			$response = $client->request('GET', $url, [
				'query' => [
					'client_id' => $config['clientId']
				] + $params
			]);

			return json_decode($response->getBody(), true);

		} catch (RequestException $e) {
			$message  = "Failed Soundcloud-API request:\n";
			$message .= 'message: ' . $e->getMessage() . "\n";
			$message .= 'target: ' . $e->getRequest()->getRequestTarget() . "\n";
			$message .= 'request: ' . json_encode($e->getRequest()->getHeaders(), JSON_PRETTY_PRINT) . "\n";

			if ($e->hasResponse()) {
				$message .=  'response: ' . json_encode($e->getResponse()->getHeaders(), JSON_PRETTY_PRINT) . "\n";
			}
			Logger::notice($message);

			// Gracefully fail, API requests may fail also because service is down.
			return false;
		}
	}
}

?>