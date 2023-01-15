<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220120185100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO cadre SET name = 'Cadré ' , category_id =1 ");
        $this->addSql("INSERT INTO cadre SET name = 'Demi cadré ' , category_id =1 ");
        $this->addSql("INSERT INTO cadre SET name = 'Sans cadre ' , category_id =1 ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
