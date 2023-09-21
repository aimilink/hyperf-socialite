<?php

namespace Sunny\Hyperf\Socialite\Contracts;

use Sunny\Hyperf\Socialite\Two\AbstractProvider;

interface SocialiteInterface
{
    /**
     * @param string|null $driver
     * @return AbstractProvider
     */
    public function driver($driver = null);
}