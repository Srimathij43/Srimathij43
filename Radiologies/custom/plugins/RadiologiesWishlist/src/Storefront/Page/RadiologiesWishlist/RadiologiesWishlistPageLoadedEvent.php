<?php declare(strict_types=1);
namespace RadiologiesWishlist\Storefront\Page\RadiologiesWishlist;

use RadiologiesWishlist\Storefront\Page\RadiologiesWishlist\RadiologiesWishlistPage;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\PageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

class RadiologiesWishlistPageLoadedEvent extends PageLoadedEvent
{
    protected $page;

    public function __construct(RadiologiesWishlistPage $page, SalesChannelContext $salesChannelContext, Request $request)
    {
        $this->page = $page;
        parent::__construct($salesChannelContext, $request);
    }

    public function getPage(): RadiologiesWishlistPage
    {
        return $this->page;
    }
}