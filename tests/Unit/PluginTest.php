<?php declare(strict_types=1);

namespace AsyncBot\Plugin\TimerTest\Unit;

use Amp\Loop;
use Amp\Promise;
use Amp\Success;
use AsyncBot\Core\Logger\Logger;
use AsyncBot\Plugin\Timer\Event\Data\Tick;
use AsyncBot\Plugin\Timer\Event\Listener\Tick as EventListener;
use AsyncBot\Plugin\Timer\Exception\AlreadyRunning;
use AsyncBot\Plugin\Timer\Plugin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class PluginTest extends TestCase
{
    private MockObject $psrLogger;

    private Plugin $plugin;

    public function setUp(): void
    {
        $this->psrLogger = $this->createMock(LoggerInterface::class);
        $this->plugin    = new Plugin(new Logger($this->psrLogger), new \DateInterval('PT1S'));
    }

    public function testOnTickLogsRegistrationOfNewListener(): void
    {
        $this->psrLogger
            ->expects($this->once())
            ->method('info')
        ;

        /** @var MockObject|EventListener $listener */
        $listener = $this->createMock(EventListener::class);

        $this->plugin->onTick($listener);
    }

    public function testRunCallsListener(): void
    {
        Loop::run(function () {
            Loop::delay(2500, fn () => Loop::stop());

            /** @var MockObject|EventListener $listener */
            $listener = $this->createMock(EventListener::class);

            $listener
                ->expects($this->exactly(2))
                ->method('__invoke')
            ;

            $this->plugin->onTick($listener);

            yield $this->plugin->run();
        });
    }

    public function testStopStopsTicks(): void
    {
        Loop::run(function () {
            Loop::delay(1500, fn () => yield $this->plugin->stop());

            Loop::delay(2500, fn () => Loop::stop());

            /** @var MockObject|EventListener $listener */
            $listener = $this->createMock(EventListener::class);

            $listener
                ->expects($this->once())
                ->method('__invoke')
            ;

            $this->plugin->onTick($listener);

            yield $this->plugin->run();
        });
    }

    public function testRunThrowsWhenCallingItAgainWhileItIsAlreadyRunning(): void
    {
        Loop::run(function () {
            yield $this->plugin->run();

            $this->expectException(AlreadyRunning::class);

            yield $this->plugin->run();
        });
    }

    public function testRunPassesPreviousDateTimeWhenAvailableAsEventData(): void
    {
        Loop::run(function () {
            Loop::delay(2500, fn () => Loop::stop());

            /** @var MockObject|EventListener $listener */
            $listener = $this->createMock(EventListener::class);

            $listener
                ->expects($this->at(0))
                ->method('__invoke')
                ->willReturnCallback(function (Tick $eventData): Promise {
                    $this->assertInstanceOf(\DateTimeImmutable::class, $eventData->getCurrentTimestamp());
                    $this->assertNull($eventData->getPreviousTimestamp());
                    $this->assertNull($eventData->getDelta());

                    return new Success();
                })
            ;

            $listener
                ->expects($this->at(1))
                ->method('__invoke')
                ->willReturnCallback(function (Tick $eventData): Promise {
                    $this->assertInstanceOf(\DateTimeImmutable::class, $eventData->getCurrentTimestamp());
                    $this->assertInstanceOf(\DateTimeImmutable::class, $eventData->getPreviousTimestamp());
                    $this->assertInstanceOf(\DateInterval::class, $eventData->getDelta());

                    return new Success();
                })
            ;

            $this->plugin->onTick($listener);

            yield $this->plugin->run();
        });
    }
}
