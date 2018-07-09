<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventProducer;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPlaced;

final class OrderPlacedProducerSpec extends ObjectBehavior
{
    function let(EventBus $eventBus, DateTimeProvider $dateTimeProvider): void
    {
        $this->beConstructedWith($eventBus, $dateTimeProvider);
    }

    function it_dispatches_an_order_placed_event_for_an_order(
        EventBus $eventBus,
        DateTimeProvider $dateTimeProvider,
        OrderInterface $order
    ): void {
        $date = new \DateTimeImmutable('now');

        $dateTimeProvider->__invoke()->willReturn($date);

        $eventBus
            ->dispatch(Argument::that(function (OrderPlaced $event) use ($order, $date): bool {
                return $event->payload() === (new OrderPlaced($order->getWrappedObject(), $date))->payload();
            }))
            ->shouldBeCalled()
        ;

        $this($order);
    }
}