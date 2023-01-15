<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119205400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO forme SET name = 'Aviator' , category_id =1 ");
        $this->addSql("INSERT INTO forme SET name = 'Carré' , category_id =1 ");
        $this->addSql("INSERT INTO forme SET name = 'Masque' , category_id =1 ");
        $this->addSql("INSERT INTO forme SET name = 'Papillon' , category_id =1 ");
        $this->addSql("INSERT INTO forme SET name = 'Rectangulaire' , category_id =1 ");
        $this->addSql("INSERT INTO forme SET name = 'Round' , category_id =1 ");
        $this->addSql("INSERT INTO forme SET name = 'Wayfarer' , category_id =1 ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
