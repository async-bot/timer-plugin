<?php declare(strict_types=1);

namespace AsyncBot\Plugin\Timer;

use Amp\Loop;
use Amp\Promise;
use Amp\Success;
use AsyncBot\Core\Logger\Logger;
use AsyncBot\Core\Plugin\Runnable;
use AsyncBot\Plugin\Timer\Event\Data\Tick;
use AsyncBot\Plugin\Timer\Event\Listener\Tick as EventListener;
use AsyncBot\Plugin\Timer\Exception\AlreadyRunning;

final class Plugin implements Runnable
{
    private Logger $logger;

    private int $intervalInMilliseconds;

    private ?string $watcherId = null;

    private ?\DateTimeImmutable $previousTimestamp = null;

    /** @var array<EventListener> */
    private array $listeners = [];

    public function __construct(Logger $logger, \DateInterval $interval)
    {
        $this->logger   = $logger;

        $currentTimestamp = new \DateTimeImmutable();
        $targetTimestamp  = $currentTimestamp->add($interval);

        $this->intervalInMilliseconds = ($targetTimestamp->getTimestamp() - $currentTimestamp->getTimestamp()) * 1000;
    }

    public function onTick(EventListener $listener): void
    {
        $this->logger->registeredListener($this, __METHOD__);

        $this->listeners[] = $listener;
    }

    /**
     * @return Promise<null>
     * @throws AlreadyRunning
     */
    public function run(): Promise
    {
        if ($this->watcherId !== null) {
            throw new AlreadyRunning();
        }

        $this->watcherId = Loop::repeat($this->intervalInMilliseconds, function (): void {
            $newTimestamp = new \DateTimeImmutable();

            $eventData = new Tick($newTimestamp, $this->previousTimestamp);

            $this->previousTimestamp = $newTimestamp;

            array_walk($this->listeners, fn (EventListener $listener) => $listener($eventData));
        });

        return new Success();
    }

    /**
     * @return Promise<null>
     */
    public function stop(): Promise
    {
        Loop::cancel($this->watcherId);

        $this->watcherId = null;

        $this->previousTimestamp = null;

        return new Success();
    }
}
