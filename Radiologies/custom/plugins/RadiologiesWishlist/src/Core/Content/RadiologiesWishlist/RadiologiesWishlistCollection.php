<?php declare(strict_types=1);

namespace RadiologiesWishlist\Core\Content\RadiologiesWishlist;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(RadiologiesWishlistEntity $entity)
 * @method void set(string $key, RadiologiesWishlistEntity $entity)
 * @method RadiologiesWishlistEntity[] getIterator()
 * @method RadiologiesWishlistEntity[] getElements()
 * @method RadiologiesWishlistEntity|null get(string $key)
 * @method RadiologiesWishlistEntity|null first()
 * @method RadiologiesWishlistEntity|null last()
 */
class RadiologiesWishlistCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'radiologies_wishlist';
    }
    protected function getExpectedClass(): string
    {
        return RadiologiesWishlistEntity::class;
    }
}
