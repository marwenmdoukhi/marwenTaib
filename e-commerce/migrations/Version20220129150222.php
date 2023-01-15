<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220129150222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO 	plaquettes_de_nez SET name = 'Non Ajustable'");
        $this->addSql("INSERT INTO 	plaquettes_de_nez SET name = 'Ajustable'");
        $this->addSql("INSERT INTO 	matieres_du_lunette SET name = 'Métal'");
        $this->addSql("INSERT INTO 	matieres_du_lunette SET name = 'Plastique'");
        $this->addSql("INSERT INTO 	matieres_du_lunette SET name = 'Bois'");
        $this->addSql("INSERT INTO 	matiere_du_branche SET name = 'Métal'");
        $this->addSql("INSERT INTO 	matiere_du_branche SET name = 'Plastique'");
        $this->addSql("INSERT INTO 	matiere_du_branche SET name = 'Bois'");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
