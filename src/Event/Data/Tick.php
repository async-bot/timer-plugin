<?php declare(strict_types=1);

namespace AsyncBot\Plugin\Timer\Event\Data;

final class Tick
{
    private \DateTimeImmutable $currentTimestamp;

    private ?\DateTimeImmutable $previousTimestamp = null;

    private ?\DateInterval $delta = null;

    public function __construct(\DateTimeImmutable $currentTimestamp, ?\DateTimeImmutable $previousTimestamp)
    {
        $this->currentTimestamp = $currentTimestamp;

        if ($previousTimestamp === null) {
            return;
        }

        $this->previousTimestamp = $previousTimestamp;
        $this->delta             = $currentTimestamp->diff($previousTimestamp);
    }

    public function getCurrentTimestamp(): \DateTimeImmutable
    {
        return $this->currentTimestamp;
    }

    public function getPreviousTimestamp(): ?\DateTimeImmutable
    {
        return $this->previousTimestamp;
    }

    public function getDelta(): ?\DateInterval
    {
        return $this->delta;
    }
}
