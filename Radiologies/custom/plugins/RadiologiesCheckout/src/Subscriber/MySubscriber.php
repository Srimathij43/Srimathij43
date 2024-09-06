<?php declare(strict_types=1);

namespace RadiologiesCheckout\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\Context;

class MySubscriber implements EventSubscriberInterface
{
    private SystemConfigService $systemconfigService;
    private EntityRepository $orderRepository;
    private Context $context;

    public function __construct(
        SystemConfigService $systemconfigService,
        EntityRepository $orderRepository
    ) {
        $this->systemconfigService = $systemconfigService;
        $this->orderRepository = $orderRepository;
        $this->context = Context::createDefaultContext();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GenericPageLoadedEvent::class => 'onCheckoutPage',
        ];
    }

    public function onCheckoutPage(GenericPageLoadedEvent $event): void
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        $salesChannelContext = $event->getSalesChannelContext();
        $customer = $salesChannelContext->getCustomer();

        if (!$customer || !$this->isCheckoutEnabled($salesChannelContext)) {
            return;
        }

        if ($this->isRelevantRoute($routeName)) {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('order.orderCustomer.customerId', $customer->getId()));
            $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [
                new EqualsFilter('stateMachineState.name', 'cancelled'),
                new EqualsFilter('stateMachineState.name', 'completed'),
            ]));
            $criteria->addAssociation('stateMachineState');

            $orderRepo = $this->orderRepository->search($criteria, $salesChannelContext->getContext());
            $orderDetails = $this->buildOrderDetails($orderRepo);

            $salesChannelContext->addExtension("RadiologiesPartialOrders", new ArrayEntity(['ordersDetails' => $orderDetails]));
        }
    }

    private function isCheckoutEnabled($salesChannelContext): bool
    {
        return (bool) $this->systemconfigService->get(
            'RadiologiesCheckout.config.enableCheckout',
            $salesChannelContext->getSalesChannel()->getId()
        );
    }

    private function isRelevantRoute(string $routeName): bool
    {
        return in_array($routeName, [
            "frontend.checkout.cart.page",
            "frontend.checkout.confirm.page",
            "frontend.cart.offcanvas"
        ], true);
    }

    private function buildOrderDetails($orderRepo): array
    {
        $orderDetails = [
            "orderNumber" => [],
            "orderStatus" => []
        ];

        foreach ($orderRepo as $orderEntity) {
            $orderDetails["orderNumber"][] = $orderEntity->getOrderNumber();
            $orderDetails["orderStatus"][] = $orderEntity->getStateMachineState()->getTechnicalName();
        }

        return $orderDetails;
    }
}
