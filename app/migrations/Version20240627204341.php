<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240627204341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE guest (id INT AUTO_INCREMENT NOT NULL, guest_email VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, creation_count INT NOT NULL, last_creation_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_ACB79A35772E836A (identifier), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shorten (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, guest_id INT DEFAULT NULL, shorten_in VARCHAR(255) NOT NULL, shorten_out VARCHAR(191) NOT NULL, add_date DATETIME NOT NULL, click_counter INT NOT NULL, is_blocked TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9F67034510011180 (shorten_out), INDEX IDX_9F670345A76ED395 (user_id), INDEX IDX_9F6703459A4AA658 (guest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', title VARCHAR(255) NOT NULL, slug VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_shorten (tag_id INT NOT NULL, shorten_id INT NOT NULL, INDEX IDX_AD562EEFBAD26311 (tag_id), INDEX IDX_AD562EEFE8EB2DBC (shorten_id), PRIMARY KEY(tag_id, shorten_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX email_idx (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shorten ADD CONSTRAINT FK_9F670345A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE shorten ADD CONSTRAINT FK_9F6703459A4AA658 FOREIGN KEY (guest_id) REFERENCES guest (id)');
        $this->addSql('ALTER TABLE tag_shorten ADD CONSTRAINT FK_AD562EEFBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_shorten ADD CONSTRAINT FK_AD562EEFE8EB2DBC FOREIGN KEY (shorten_id) REFERENCES shorten (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shorten DROP FOREIGN KEY FK_9F670345A76ED395');
        $this->addSql('ALTER TABLE shorten DROP FOREIGN KEY FK_9F6703459A4AA658');
        $this->addSql('ALTER TABLE tag_shorten DROP FOREIGN KEY FK_AD562EEFBAD26311');
        $this->addSql('ALTER TABLE tag_shorten DROP FOREIGN KEY FK_AD562EEFE8EB2DBC');
        $this->addSql('DROP TABLE guest');
        $this->addSql('DROP TABLE shorten');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tag_shorten');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
