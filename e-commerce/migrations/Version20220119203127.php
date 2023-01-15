<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119203127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO style SET name = 'Basique' , category_id =1 ");
        $this->addSql("INSERT INTO style SET name = 'Casual' , category_id =1 ");
        $this->addSql("INSERT INTO style SET name = 'Classique' , category_id =1 ");
        $this->addSql("INSERT INTO style SET name = 'Girlay' , category_id =1 ");
        $this->addSql("INSERT INTO style SET name = 'Habillé' , category_id =1 ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
