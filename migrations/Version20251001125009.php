<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251001125009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE conjoints CHANGE emprunteur_id emprunteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE conjoints ADD CONSTRAINT FK_6590221776C50E4A FOREIGN KEY (proprietaire_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE conjoints ADD CONSTRAINT FK_65902217F0840037 FOREIGN KEY (emprunteur_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4A76ED395');
        $this->addSql('ALTER TABLE conjoints DROP FOREIGN KEY FK_6590221776C50E4A');
        $this->addSql('ALTER TABLE conjoints DROP FOREIGN KEY FK_65902217F0840037');
        $this->addSql('ALTER TABLE conjoints CHANGE emprunteur_id emprunteur_id INT DEFAULT NULL');
    }
}
