# oauth2-weibo
Weibo OAuth 2.0 support for the PHP League's OAuth 2.0 Client
##Install
You can open a terminal and type in
```shell
composer require spoonwep/oauth2-weibo
```
or require in a composer.json
```json
"require": {
	"spoonwep/oauth2-weibo": "~1.3"
}
```
then run:
```shell
composer update
```
##Useage
```php
session_start();
$provider = new \spoonwep\OAuth2\Client\Provider\Weibo([
	'clientId' => '{weibo App Key}',
	'clientSecret' => '{weibo App Secret}',
	'redirectUri' => '{http://example.com/callback-url}',
]);
if (!isset($_GET['code'])) {
	$authUrl = $provider->getAuthorizationUrl();
	$_SESSION['oauth2state'] = $provider->getState();
	header('Location: '.$authUrl);
	exit;
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
	unset($_SESSION['oauth2state']);
	exit('Invalid state');
} else {
	$token = $provider->getAccessToken('authorization_code', [
		'code' => $_GET['code']
	]);

	//fetch userinfo returned by serverside
	$user = $provider->getResourceOwner($token);
	print_r($user->toArray());
}
```
###License
The MIT License (MIT). Please see [License](https://github.com/spoonwep/oauth2-weibo/blob/master/LICENSE.txt) File for more information.
