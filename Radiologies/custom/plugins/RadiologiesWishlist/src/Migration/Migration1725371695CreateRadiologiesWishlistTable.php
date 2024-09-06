<?php declare(strict_types=1);

namespace RadiologiesWishlist\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1725371695CreateRadiologiesWishlistTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1725371695;
    }

    public function update(Connection $connection): void
    {
       $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `radiologies_wishlist` (
              `id` BINARY(16),
              `name` VARCHAR(255),
              `customer_id` BINARY(16),
              `product_id` BINARY(16),
              `custom_fields` JSON NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`id`),
                KEY `fk.radiologies_wishlist.product_id` (`product_id`),
                KEY `fk.radiologies_wishlist.customer_id` (`customer_id`),
                CONSTRAINT `json.radiologies_wishlist.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
                CONSTRAINT `fk.radiologies_wishlist.product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)  ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.radiologies_wishlist.customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
