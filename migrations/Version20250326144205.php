<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326144205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE wishlists_product (wishlists_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_7388009325F8D7C (wishlists_id), INDEX IDX_738800934584665A (product_id), PRIMARY KEY(wishlists_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wishlists_product ADD CONSTRAINT FK_7388009325F8D7C FOREIGN KEY (wishlists_id) REFERENCES wishlists (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlists_product ADD CONSTRAINT FK_738800934584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD last_login_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wishlists_product DROP FOREIGN KEY FK_7388009325F8D7C');
        $this->addSql('ALTER TABLE wishlists_product DROP FOREIGN KEY FK_738800934584665A');
        $this->addSql('DROP TABLE wishlists_product');
        $this->addSql('ALTER TABLE `user` DROP last_login_at');
    }
}
