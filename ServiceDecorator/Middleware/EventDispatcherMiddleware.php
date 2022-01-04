<?php

declare(strict_types=1);

namespace App\ServiceDecorator\Middleware;

use App\EventBus\Recorder\RecordsMessages;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcherMiddleware implements MiddlewareInterface
{
    public function __construct(
        private RecordsMessages $eventRecorder,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function execute($proxy, $instance, $method, $params, &$returnEarly, object $decoratedService, callable $next)
    {
        echo 'event_dispatcher:start;';

        $returnValue = $next($proxy, $instance, $method, $params, $returnEarly, $decoratedService);

        $recordedMessages = $this->eventRecorder->recordedMessages();

        foreach ($recordedMessages as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        if (count($recordedMessages) !== count($this->eventRecorder->recordedMessages())) {
            $expectedMessages = array_map(
                fn($messageItem): string => get_class($messageItem),
                $recordedMessages
            );
            $actualMessages = array_map(
                fn($messageItem): string => get_class($messageItem),
                $this->eventRecorder->recordedMessages()
            );

            throw new LogicException(
                sprintf(
                    'You cannot record new event during dispatching: %s',
                    implode(
                        ',',
                        array_diff($actualMessages, $expectedMessages)
                    )
                )
            );
        }

        $this->eventRecorder->eraseMessages();

        echo 'event_dispatcher:stop;';

        return $returnValue;
    }
}
