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
		} catch (RequestException $e) {
			$message  = "Failed Instagram-API request:\n";
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
		$result = json_decode($response->getBody(), true);
		return $result['data'];
	}
}

?>