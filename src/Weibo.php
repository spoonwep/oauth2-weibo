<?php namespace spoonwep\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use spoonwep\OAuth2\Client\WeiboResourceOwner;

class Weibo extends AbstractProvider
{
	use BearerAuthorizationTrait;

	/**
	 * @var
	 */
	public $uid;
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
	 * get user's uid
	 * @return string
	 */
	protected function getUidUrl (AccessToken $token)
	{
		return $this->domain.'/2/account/get_uid.json?access_token='.$token;
	}

	public function fetchUid (AccessToken $token)
	{
		$url     = $this->getUidUrl($token);
		$request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);
        return $this->getSpecificResponse($request);
	}

    /**
     * @param RequestInterface $request
     * @return mixed
     * @throws IdentityProviderException
     */
    protected function getSpecificResponse (RequestInterface $request)
    {
        $response = $this->getResponse($request);
        $parsed   = $this->parseSpecificResponse($response);

        $this->checkResponse($response, $parsed);

        return $parsed;
    }

    /**
     * A specific parseResponse function
     * @param ResponseInterface $response
     * @return mixed
     */
    protected function parseSpecificResponse (ResponseInterface $response)
    {
        $content = (string)$response->getBody();
        parse_str($content, $parsed);

        return $parsed;
    }

    /**
	 * Get provider url to fetch user details
	 * @param AccessToken $token
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl (AccessToken $token)
	{
        $uid          = json_decode(array_keys($this->fetchUid($token))[0], true);
        $this->uid    = $uid['uid'];
		return $this->domain.'/2/users/show.json?source='.$this->clientId.'&access_token='.$token.'&uid='.$this->uid;
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
