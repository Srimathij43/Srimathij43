<?php declare(strict_types=1);

namespace RadiologiesWishlist\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use RadiologiesWishlist\Storefront\Page\RadiologiesWishlist\RadiologiesWishlistPageLoadedEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;

class MySubscriber implements EventSubscriberInterface
{
     /**
     * @var EntityRepository
     */
    private $wishlistRepository;
     /**
     * @var SystemConfigService
     */
    private $systemconfigService;
    /**
     * @var SalesChannelRepository
     */
    private $productRepository;
    /**
     * @param EntityRepository $wishlistRepository, 
     * @param SystemConfigService $systemconfigService, 
     * @param SalesChannelRepository $productRepository, 
     */
    public function __construct(
        EntityRepository $wishlistRepository, 
        SystemConfigService $systemconfigService,
        SalesChannelRepository $productRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->systemconfigService = $systemconfigService;
        $this->productRepository = $productRepository;

    }
    public static function getSubscribedEvents()
    {
        return [
            GenericPageLoadedEvent::class => 'onGenericPageLoadeded',
            RadiologiesWishlistPageLoadedEvent::class => 'onRadiologiesWishlistPageLoaded'

        ];
    }

    public function onGenericPageLoadeded(GenericPageLoadedEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        if($event->getSalesChannelContext()->getCustomer())
        {
            $customerId = $event->getSalesChannelContext()->getCustomer()->getId();
            $context = $event->getSalesChannelContext();
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter("customerId", $customerId));
            $criteria->addAssociations(["product", "media", "options", "cover", "options.group"]);
            $radiologiesWishlistsDetails = $this->wishlistRepository->search($criteria, $context->getContext())->getElements();
            $radiologiesWishlist = [];
            foreach ($radiologiesWishlistsDetails as $key => $value) {
                $radiologiesWishlist[] = $value;
            }
            // print"<pre>";print_r($radiologiesWishlist);die;
            $event->getPage()->addExtension("radiologiesWishlist", new ArrayEntity(['radiologiesWishlist' => $radiologiesWishlist]));
        }
    }
    public function onRadiologiesWishlistPageLoaded(RadiologiesWishlistPageLoadedEvent $event)
    {
        $wishlistItems = $event->getPage()->getRadiologiesWishlistData();
        $context = $event->getSalesChannelContext();

        foreach ($wishlistItems as $item) {
            $productId = $item->getProductId();
            $productData = $this->getProductData($productId, $context);
            if (!empty($productData)) {
                $item->setExtensions(['productData' => $productData]);
            }
        }

        $event->getPage()->setRadiologiesWishlistData($wishlistItems);
    }

    private function getProductData($productId, $context)
    {
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('id', $productId))
            ->addAssociations(["prices", "media", "options", "cover"])
            ->setLimit(1);

        return $this->productRepository->search($criteria, $context)->first();
    }
    /**
     * @param $key
     * @param SalesChannelContext $salesChannelContext
     * @return array|bool|float|int|string|null
     */
    private function getConfig($key, SalesChannelContext $salesChannelContext)
    {
        return $this->systemconfigService->get(
            'RadiologiesWishlist.config.' . $key,
            $salesChannelContext->getSalesChannel()->getId()
        );
    }
}
