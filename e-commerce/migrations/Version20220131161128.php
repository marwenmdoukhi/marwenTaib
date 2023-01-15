<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220131161128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO 	fragrance_de_parfum SET name = 'Eau de parfum'");
        $this->addSql("INSERT INTO 	fragrance_de_parfum SET name = 'Eau de parfum'");
        $this->addSql("INSERT INTO 	fragrance_de_parfum SET name = 'Eau de cologne '");
        $this->addSql("INSERT INTO 	fragrance_de_parfum SET name = 'Eau Fraiche '");
        $this->addSql("INSERT INTO 	volume SET name = '25 ml' , category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '30 ml' , category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '35 ml' ,category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '40 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '45 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '50 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '60 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '65 ml' ,category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '70 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '75 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '80 ml',category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '90 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '100 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '110 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '120 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '125 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '150 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '175 ml' ,category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '200 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '236 ml' ,category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '300 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '400 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '500 ml', category_id =3");
        $this->addSql("INSERT INTO 	volume SET name = '1 l' ,category_id =3");
        $this->addSql("INSERT INTO 	type_de_maquillage SET name = 'Curl And Volume Mascara' ,category_id =3");
        $this->addSql("INSERT INTO 	type_de_maquillage SET name = 'Volume Mascara' ,category_id =3");
        $this->addSql("INSERT INTO 	type_de_maquillage SET name = 'False Lash Effect' ,category_id =3");
        $this->addSql("INSERT INTO 	type_de_maquillage SET name = 'Lash Lifting', category_id =3");
        $this->addSql("INSERT INTO 	type_de_maquillage SET name = 'Clump Free' ,category_id =3");
        $this->addSql("INSERT INTO 	type_de_maquillage SET name = 'Proof' ,category_id =3");


    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
