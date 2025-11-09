<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109180323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_events (user_id INT NOT NULL, events_id INT NOT NULL, INDEX IDX_36D54C77A76ED395 (user_id), INDEX IDX_36D54C779D6A1065 (events_id), PRIMARY KEY(user_id, events_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_events ADD CONSTRAINT FK_36D54C77A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_events ADD CONSTRAINT FK_36D54C779D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_7169709271F7E88B');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_716970929D1C3019');
        $this->addSql('DROP TABLE participants');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participants (id INT AUTO_INCREMENT NOT NULL, participant_id INT NOT NULL, event_id INT NOT NULL, registration_datetime DATETIME NOT NULL, INDEX IDX_716970929D1C3019 (participant_id), INDEX IDX_7169709271F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_7169709271F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_716970929D1C3019 FOREIGN KEY (participant_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE user_events DROP FOREIGN KEY FK_36D54C77A76ED395');
        $this->addSql('ALTER TABLE user_events DROP FOREIGN KEY FK_36D54C779D6A1065');
        $this->addSql('DROP TABLE user_events');
    }
}
