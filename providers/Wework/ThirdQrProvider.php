<?php

namespace HyperfSocialiteProviders\Wework;

use Cblink\Hyperf\Socialite\Two\AbstractProvider;
use Cblink\Hyperf\Socialite\Two\User;
use GuzzleHttp\RequestOptions;
use Hyperf\Utils\Arr;

class ThirdQrProvider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'THIRD_WEWORK_QR';

    /**
     * @var string ticket
     */
    protected $ticket;

    /**
     * {@inheritdoc}.
     */
    public function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.work.weixin.qq.com/wwopen/sso/3rd_qrConnect', $state);
    }

    /**
     * {@inheritdoc}.
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        $query = http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);

        return $url.'?'.$query;
    }

    /**
     * {@inheritdoc}.
     */
    protected function getCodeFields($state = null)
    {
        return [
            'appid'         => $this->getClientId(),
            'redirect_uri'  => $this->getRedirectUrl(),
            'usertype'      => 'member',
            'state'         => $state,
            'lang'          => 'zh',
        ];
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenUrl()
    {
        return 'https://qyapi.weixin.qq.com/cgi-bin/service/get_provider_token';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://qyapi.weixin.qq.com/cgi-bin/service/get_login_info', [
            RequestOptions::QUERY => [
                'access_token' => $token,
                'auth_code'    => $this->getCode(),
            ],
        ]);

        $user = json_decode((string) $response->getBody(), true);

        return $user;
    }

    /**
     * {@inheritdoc}.
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['UserId'] ?? $user['OpenId'] ?? null,
            'unionid'  => $user['open_userid'] ?? null,
            'nickname' => null,
            'avatar'   => null,
            'name'     => null,
            'email'    => null,
        ]);
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenFields($code)
    {
        return [
            'corpid' => $this->getClientId(),
            'provider_secret' => $this->getClientSecret(),
        ];
    }

    /**
     * {@inheritdoc}.
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            RequestOptions::QUERY => $this->getTokenFields($code),
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Get the access token from the token response body.
     *
     * @param array $body
     *
     * @return string
     */
    protected function parseAccessToken($body)
    {
        return Arr::get($body, 'provider_access_token');
    }
}
