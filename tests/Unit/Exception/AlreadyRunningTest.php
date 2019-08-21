<?php declare(strict_types=1);

namespace AsyncBot\Plugin\TimerTest\Unit\Exception;

use AsyncBot\Plugin\Timer\Exception\AlreadyRunning;
use PHPUnit\Framework\TestCase;

final class AlreadyRunningTest extends TestCase
{
    public function testConstructorCorrectlyFormatsMessage(): void
    {
        $this->expectException(AlreadyRunning::class);
        $this->expectExceptionMessage('AsyncBot\Plugin\Timer\Plugin is already running');

        throw new AlreadyRunning();
    }
}
