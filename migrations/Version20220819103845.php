<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220819103845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE origine (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plat (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_2038A207B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, adress VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, INDEX IDX_EB95123F8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_category (restaurant_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_26E9D72EB1E7706E (restaurant_id), INDEX IDX_26E9D72E12469DE2 (category_id), PRIMARY KEY(restaurant_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_origine (restaurant_id INT NOT NULL, origine_id INT NOT NULL, INDEX IDX_DF40EA1AB1E7706E (restaurant_id), INDEX IDX_DF40EA1A87998E (origine_id), PRIMARY KEY(restaurant_id, origine_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plat ADD CONSTRAINT FK_2038A207B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123F8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE restaurant_category ADD CONSTRAINT FK_26E9D72EB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_category ADD CONSTRAINT FK_26E9D72E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_origine ADD CONSTRAINT FK_DF40EA1AB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_origine ADD CONSTRAINT FK_DF40EA1A87998E FOREIGN KEY (origine_id) REFERENCES origine (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plat DROP FOREIGN KEY FK_2038A207B1E7706E');
        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123F8BAC62AF');
        $this->addSql('ALTER TABLE restaurant_category DROP FOREIGN KEY FK_26E9D72EB1E7706E');
        $this->addSql('ALTER TABLE restaurant_category DROP FOREIGN KEY FK_26E9D72E12469DE2');
        $this->addSql('ALTER TABLE restaurant_origine DROP FOREIGN KEY FK_DF40EA1AB1E7706E');
        $this->addSql('ALTER TABLE restaurant_origine DROP FOREIGN KEY FK_DF40EA1A87998E');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE origine');
        $this->addSql('DROP TABLE plat');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE restaurant_category');
        $this->addSql('DROP TABLE restaurant_origine');
    }
}
