<?php declare(strict_types=1);

namespace RadiologiesWishlist\Storefront\Page\RadiologiesWishlist;

use RadiologiesWishlist\Storefront\Page\RadiologiesWishlist\RadiologiesWishlistPage;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Checkout\Order\SalesChannel\AbstractOrderRoute;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;

class RadiologiesWishlistPageLoader
{
    private $genericLoader;
    private $eventDispatcher;
    private $orderRoute;
    private $wishlistRepository;
    private $productRepository;

    public function __construct(
        GenericPageLoaderInterface $genericLoader,
        EventDispatcherInterface $eventDispatcher,
        AbstractOrderRoute $orderRoute,
        EntityRepository $wishlistRepository,
        SalesChannelRepository $productRepository
    ) {
        $this->genericLoader = $genericLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->orderRoute = $orderRoute;
        $this->wishlistRepository = $wishlistRepository;
        $this->productRepository = $productRepository;
    }

    public function load(Request $request, SalesChannelContext $context): RadiologiesWishlistPage
    {
        $page = $this->genericLoader->load($request, $context);
        $page = RadiologiesWishlistPage::createFrom($page);

        if ($page->getMetaInformation()) {
            $page->getMetaInformation()->setRobots('nofollow');
        }

        $customerId = $context->getCustomer()->getId();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $customerId));

        // Fetch wishlist items
        $wishlistItems = $this->wishlistRepository->search($criteria, $context->getContext())->getElements();

        // Enhance wishlist items with product data
        foreach ($wishlistItems as $item) {
            $productId = $item->getProductId();
            $productData = $this->getProductData($productId, $context);
            if (!empty($productData)) {
                $item->setExtensions(['productData' => $productData]);
            }
        }

        $page->setRadiologiesWishlistData($wishlistItems);

        $this->eventDispatcher->dispatch(
            new RadiologiesWishlistPageLoadedEvent($page, $context, $request)
        );
        
        return $page;
    }

    private function getProductData($productId, $context)
    {
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('id', $productId))
            ->addAssociations(["prices", "media", "options", "cover"])
            ->setLimit(1);

        $product = $this->productRepository->search($criteria, $context)->first();
        return $product ? $product->jsonSerialize() : [];
    }
}
