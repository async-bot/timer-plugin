<?php declare(strict_types=1);

namespace AsyncBot\Plugin\TimerTest\Unit\Event\Data;

use AsyncBot\Plugin\Timer\Event\Data\Tick;
use PHPUnit\Framework\TestCase;

final class TickTest extends TestCase
{
    public function testGetCurrentTimestamp(): void
    {
        $timestamp = new \DateTimeImmutable();

        $this->assertSame($timestamp, (new Tick($timestamp, null))->getCurrentTimestamp());
    }

    public function testGetPreviousTimestampReturnsNullWhenNotSet(): void
    {
        $this->assertNull((new Tick(new \DateTimeImmutable(), null))->getPreviousTimestamp());
    }

    public function testGetPreviousTimestampReturnsTimestamp(): void
    {
        $previousTimestamp = new \DateTimeImmutable();

        $this->assertSame(
            $previousTimestamp,
            (new Tick(new \DateTimeImmutable(), $previousTimestamp))->getPreviousTimestamp(),
        );
    }

    public function testGetDeltaReturnsNullWhenPreviousTimestampIsNotSet(): void
    {
        $this->assertNull((new Tick(new \DateTimeImmutable(), null))->getDelta());
    }

    public function testGetDeltaReturnsDelta(): void
    {
        $this->assertInstanceOf(
            \DateInterval::class,
            (new Tick(new \DateTimeImmutable(), new \DateTimeImmutable()))->getDelta(),
        );
    }
}
