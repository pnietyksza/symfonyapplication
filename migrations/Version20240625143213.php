<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240625143213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP createat, DROP updateat, CHANGE total total NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F46AF89CCED');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F466F90D45B');
        $this->addSql('DROP INDEX UNIQ_ED896F46AF89CCED ON order_detail');
        $this->addSql('DROP INDEX IDX_ED896F466F90D45B ON order_detail');
        $this->addSql('ALTER TABLE order_detail ADD order_id INT NOT NULL, ADD product_id INT NOT NULL, DROP productid_id, DROP orderid_id, CHANGE price price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F468D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F464584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_ED896F468D9F6D38 ON order_detail (order_id)');
        $this->addSql('CREATE INDEX IDX_ED896F464584665A ON order_detail (product_id)');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD createat DATETIME NOT NULL, ADD updateat DATETIME NOT NULL, DROP created_at, DROP updated_at, CHANGE total total NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F468D9F6D38');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F464584665A');
        $this->addSql('DROP INDEX IDX_ED896F468D9F6D38 ON order_detail');
        $this->addSql('DROP INDEX IDX_ED896F464584665A ON order_detail');
        $this->addSql('ALTER TABLE order_detail ADD productid_id INT DEFAULT NULL, ADD orderid_id INT DEFAULT NULL, DROP order_id, DROP product_id, CHANGE price price NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F46AF89CCED FOREIGN KEY (productid_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F466F90D45B FOREIGN KEY (orderid_id) REFERENCES `order` (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ED896F46AF89CCED ON order_detail (productid_id)');
        $this->addSql('CREATE INDEX IDX_ED896F466F90D45B ON order_detail (orderid_id)');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(10, 0) NOT NULL');
    }
}
