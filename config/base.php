<?php
/**
 * Copyright 2014 David Persson. All rights reserved.
 * Copyright 2016 Atelier Disko. All rights reserved.
 *
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
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

// Soundcloud Settings
//
// 1. Get a SC account
// 2. Register an new "app" and submit it
//    https://developers.soundcloud.com/
// 3. Once reviewed you can access the app and
//    get the required credentials.
//
Settings::register('service.soundcloud.default', [
	'username' => null, // the username from the URL i.e. `'ki-records`'
	'clientId' => null, // required for API access
	'clientSecret' => null // required for API access
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

Settings::register('service.bandcamp.default', [
	'username' => null // the name as used in the URL `'<ki-records>.bandcamp.com'`
]);

?>