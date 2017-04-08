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
 * License. If not, see https://atelierdisko.de/licenses.
 */

namespace base_social\config;

use base_core\extensions\cms\Settings;

Settings::register('service.tumblr.default', [
	'username' => null
]);

Settings::register('service.vimeo.default', [
	'username' => null
]);

Settings::register('service.youtube.default', [
	'username' => null
]);

Settings::register('service.facebook.default', [
	'appId' => null,
	'appSecret' => null,
	'pageUrl' => null
]);

Settings::register('service.soundcloud.default', [
	'clientId' => null,
	'clientSecret' => null
]);


// Twitter Settings
//
// - How to get your Twitter OAuth Tokens -
//
// Read:
// https://dev.twitter.com/oauth/overview/application-owner-access-tokens
//
// Ensure you've added your phone number to the account:
// https://support.twitter.com/articles/321492-hinzufugen-der-telefonnummer-zum-account
//
// 1. Goto https://apps.twitter.com/ and create a new App (in this case its the website)
// 2. Specify a callback URL using the website's domain i.e. `http://example.com/oauth/cb`.
//
Settings::register('service.twitter.default', [
	'username' => null,
	'consumerKey' => null,
	'consumerSecret' => null,
	'accessToken' => null,
	'accessTokenSecret' => null
]);

// Instagram Settings
//
// - How to get your Instagram Access Token -
//
//   1. Create app in Instagram developer interface.
//   2. As the redirect-URL use a non existent one on your website.
//   3. Open the following URL in your browser and note down the "code":
//      https://api.instagram.com/oauth/authorize/?client_id=[client_id]&redirect_uri=[redirect_uri]&response_type=code
//   4. Issue the following command to get the access token:
//      curl \
//        -F 'client_id=[your_client_id]' \
//        -F 'client_secret=[your_secret_key]' \
//        -F 'grant_type=authorization_code' \
//        -F 'redirect_uri=[redirect_url]' \
//        -F 'code=[code]' \
//        https://api.instagram.com/oauth/access_token; echo ''

// - How to get your Instagram user id -
//
// http://jelled.com/instagram/lookup-user-id
//
Settings::register('service.instagram.default', [
	'username' => null,
	'userId' => null,
	'accessToken' => null
]);

?>