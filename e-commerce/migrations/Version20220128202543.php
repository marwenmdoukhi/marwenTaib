<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220128202543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("INSERT INTO style SET name = 'Basique' , category_id =2 ");
        $this->addSql("INSERT INTO style SET name = 'Casual' , category_id =2 ");
        $this->addSql("INSERT INTO style SET name = 'Classique' , category_id =2 ");
        $this->addSql("INSERT INTO style SET name = 'Enfant', category_id =2 ");
        $this->addSql("INSERT INTO style SET name = 'Habillé' , category_id =2 ");
        $this->addSql("INSERT INTO style SET name = 'Sport' , category_id =2 ");
        $this->addSql("INSERT INTO 	type_du_mouvement SET name = 'Quatrz' , category_id =2 ");
        $this->addSql("INSERT INTO 	type_du_mouvement SET name = 'Quatrz solaire' , category_id =2 ");
        $this->addSql("INSERT INTO 	type_du_mouvement SET name = 'Quatrz kinétique' , category_id =2 ");
        $this->addSql("INSERT INTO 	type_du_mouvement SET name = 'Manuel' , category_id =2 ");
        $this->addSql("INSERT INTO 	type_du_mouvement SET name = 'Automatique ' , category_id =2 ");
        $this->addSql("INSERT INTO 	fonction_montre SET name = 'Chronographe'");
        $this->addSql("INSERT INTO 	fonction_montre SET name = 'Multifonctions'");
        $this->addSql("INSERT INTO 	forme_du_cadran SET name = 'Carré', category_id =2");
        $this->addSql("INSERT INTO 	forme_du_cadran SET name = 'Round', category_id =2");
        $this->addSql("INSERT INTO 	forme_du_cadran SET name = 'Rectangle', category_id =2");
        $this->addSql("INSERT INTO 	forme_du_cadran SET name = 'Ovales', category_id =2");
        $this->addSql("INSERT INTO 	forme_du_cadran SET name = 'Coussin', category_id =2");
        $this->addSql("INSERT INTO 	forme_du_cadran SET name = 'Tonneau', category_id =2");
        $this->addSql("INSERT INTO 	verre_de_montre SET name = 'Minéral'");
        $this->addSql("INSERT INTO 	verre_de_montre SET name = 'Acrylique'");
        $this->addSql("INSERT INTO 	verre_de_montre SET name = 'Saphir'");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
