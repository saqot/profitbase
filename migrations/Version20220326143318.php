<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220326143318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sprints ADD `week` SMALLINT NOT NULL, ADD `year` SMALLINT NOT NULL, ADD `is_active` TINYINT(1) DEFAULT 0 NOT NULL, ADD `create_at` DATE NOT NULL, ADD `start_at` DATE NOT NULL');
        $this->addSql('ALTER TABLE tasks ADD sprint_id VARCHAR(50) DEFAULT NULL, ADD `is_active` TINYINT(1) DEFAULT 0 NOT NULL, ADD `estimation` SMALLINT NOT NULL, ADD `title` VARCHAR(250) NOT NULL, ADD `description` LONGTEXT NOT NULL, ADD `at` DATE NOT NULL');
        $this->addSql('ALTER TABLE tasks ADD CONSTRAINT FK_505865978C24077B FOREIGN KEY (sprint_id) REFERENCES `sprints` (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_505865978C24077B ON tasks (sprint_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `sprints` DROP `week`, DROP `year`, DROP `is_active`, DROP `create_at`, DROP `start_at`');
        $this->addSql('ALTER TABLE `tasks` DROP FOREIGN KEY FK_505865978C24077B');
        $this->addSql('DROP INDEX IDX_505865978C24077B ON `tasks`');
        $this->addSql('ALTER TABLE `tasks` DROP sprint_id, DROP `is_active`, DROP `estimation`, DROP `title`, DROP `description`, DROP `at`');
    }
}
