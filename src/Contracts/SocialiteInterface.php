<?php

namespace Aimilink\Hyperf\Socialite\Contracts;

use Aimilink\Hyperf\Socialite\Two\AbstractProvider;

interface SocialiteInterface
{
    /**
     * @param string|null $driver
     * @return AbstractProvider
     */
    public function driver($driver = null);
}