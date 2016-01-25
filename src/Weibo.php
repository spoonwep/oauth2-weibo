<?php namespace spoonwep\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use spoonwep\OAuth2\Client\weiboResourceOwner;

class Weibo extends AbstractProvider
{
	use BearerAuthorizationTrait;

	/**
	 * @var string
	 */
	public $domain = "https://api.weibo.com";

	/**
	 * Get authorization url to begin OAuth flow
	 *
	 * @return string
	 */
	public function getBaseAuthorizationUrl ()
	{
		return $this->domain . '/oauth2/authorize';
	}

	/**
	 * Get access token url to retrieve token
	 * @param array $params
	 * @return string
	 */
	public function getBaseAccessTokenUrl (array $params)
	{
		return $this->domain . '/oauth2/access_token';
	}

	/**
	 * Get provider url to fetch user details
	 * @param AccessToken $token
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl (AccessToken $token)
	{
		return $this->domain.'/2/users/show.json?source='.$this->clientId.'&access_token='.$token.'&uid='.$token;
	}

	/**
	 * Check a provider response for errors.
	 *
	 * @throws IdentityProviderException
	 * @param  ResponseInterface $response
	 * @param  string $data Parsed response data
	 * @return void
	 */
	protected function checkResponse (ResponseInterface $response, $data)
	{
		if (isset($data['error'])) {
			throw new IdentityProviderException($data['error_description'], $response->getStatusCode(), $response);
		}
	}

	/**
	 * Get the default scopes used by this provider.
	 *
	 * This should not be a complete list of all scopes, but the minimum
	 * required for the provider user interface!
	 *
	 * @return array
	 */
	protected function getDefaultScopes ()
	{
		return [];
	}

	/**
	 * Generate a user object from a successful user details request.
	 * @param array $response
	 * @param AccessToken $token
	 * @return weiboResourceOwner
	 */
	protected function createResourceOwner (array $response, AccessToken $token)
	{
		return new weiboResourceOwner($response);
	}
}
