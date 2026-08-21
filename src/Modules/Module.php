<?php

namespace Javaabu\DhivehiGpt\Modules;

use Javaabu\DhivehiGpt\Http\Client;

abstract class Module
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
