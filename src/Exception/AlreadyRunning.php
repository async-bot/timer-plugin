<?php declare(strict_types=1);

namespace AsyncBot\Plugin\Timer\Exception;

use AsyncBot\Plugin\Timer\Plugin;

class AlreadyRunning extends Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('%s is already running', Plugin::class));
    }
}
