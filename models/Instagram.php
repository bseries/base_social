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
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use base_social\models\InstagramMedia;
use lithium\analysis\Logger;

class Instagram {

	// Gets all media.
	// @link http://instagram.com/developer/endpoints/users/#get_users_media_recent
	public static function allMediaByAuthor($name, array $config) {
		if (!is_numeric($name)) {
			throw new InvalidArgumentException('Instagram author name must be used ID (numeric).');
		}
		$results = static::_api("users/{$name}/media/recent", $config);

		if (!$results) {
			return $results;
		}
		foreach ($results as &$result) {
			$result = InstagramMedia::create(['raw' => $result]);
		}
		return $results;
	}

	protected static function _api($url, array $config, array $params = []) {
		$client = new Client(['base_uri' => 'https://api.instagram.com/v1/']);

		try {
			$response = $client->request('GET', $url, [
				'query' => [
					'access_token' => $config['accessToken']
				]
			]);

			$result = json_decode($response->getBody(), true);
			return static::_resolvePaginated($result['pagination'], $result['data']);

		} catch (RequestException $e) {
			$message  = "Failed Instagram-API request:\n";
			$message .= 'message: ' . $e->getMessage() . "\n";
			$message .= 'target: ' . $e->getRequest()->getRequestTarget() . "\n";
			$message .= 'request: ' . json_encode(
				$e->getRequest()->getHeaders(), JSON_PRETTY_PRINT
			) . "\n";

			if ($e->hasResponse()) {
				$message .=  'response: ' . json_encode(
					$e->getResponse()->getHeaders(), JSON_PRETTY_PRINT
				) . "\n";
			}
			Logger::notice($message);

			// Gracefully fail, API requests may fail also because service is down.
			return false;
		}
	}

	// Recursively resolves paginated data.
	// Will never cache first page, but cache all subsequent pages.
	protected static function _resolvePaginated(array $pagination, array $data) {
		$client = new Client();

		// `pagination` key is an empty array if there are no more pages.
		if (!$pagination) {
			Logger::debug("Instagram-API finished resolving pagination.");
			return $data;
		}
		Logger::debug("Instagram-API retrieving next page `{$pagination['next_url']}`.");

		$response = $client->request('GET', $pagination['next_url']);
		$result = json_decode($response->getBody(), true);

		return static::_resolvePaginated(
			$result['pagination'], array_merge($data, $result['data'])
		);
	}
}

?>