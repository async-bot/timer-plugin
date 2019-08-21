<?php declare(strict_types=1);

namespace AsyncBot\Plugin\Timer\Event\Listener;

use Amp\Promise;
use AsyncBot\Plugin\Timer\Event\Data\Tick as EventData;

interface Tick
{
    /**
     * @return Promise<null>
     */
    public function __invoke(EventData $eventdata): Promise;
}
