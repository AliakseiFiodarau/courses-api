<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230113150230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE course (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN course.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE lecture (id INT NOT NULL, blog_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C16779488FABDD9F ON lecture (blog_id_id)');
        $this->addSql('COMMENT ON COLUMN lecture.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE lecture ADD CONSTRAINT FK_C16779488FABDD9F FOREIGN KEY (blog_id_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE lecture DROP CONSTRAINT FK_C16779488FABDD9F');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE lecture');
    }
}
