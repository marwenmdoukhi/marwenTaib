<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220130130830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO matier_bracelet SET name = 'Acier' , category_id =2 ");
        $this->addSql("INSERT INTO matier_bracelet SET name = 'Ceramique' , category_id =2 ");
        $this->addSql("INSERT INTO matier_bracelet SET name = 'Silicone' , category_id =2 ");
        $this->addSql("INSERT INTO matier_bracelet SET name = 'Tissu' , category_id =2 ");
        $this->addSql("INSERT INTO matier_bracelet SET name = 'Cuir' , category_id =2 ");


        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
