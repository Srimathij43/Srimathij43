<?php declare(strict_types=1);
/**
 * To plugin file.
 *
 * Copyright (C) BrandCrock GmbH. All rights reserved
 *
 * If you have found this script useful a small
 * recommendation as well as a comment on our
 * home page(https://brandcrock.com/)
 * would be greatly appreciated.
 *
 * @author BrandCrock GmbH
 * @package BrandCrockSmartBasket
 */
namespace RadiologiesWishlist\Storefront\Page\RadiologiesWishlist;

use Shopware\Storefront\Page\Page;

class RadiologiesWishlistPage extends Page
{

    public function getRadiologiesWishlistData(): array
    {
        return $this->radiologiesWishlistData;
    }

    public function setRadiologiesWishlistData(array $radiologiesWishlistData): void
    {
        $this->radiologiesWishlistData = $radiologiesWishlistData;
    }
}