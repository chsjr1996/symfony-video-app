<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220515190430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE comments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE videos_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categories (id INT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(45) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)');
        $this->addSql('CREATE INDEX IDX_3AF34668727ACA70 ON categories (parent_id)');
        $this->addSql('CREATE TABLE comments (id INT NOT NULL, owner_id INT NOT NULL, video_id INT NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F9E962A7E3C61F9 ON comments (owner_id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A29C1004E ON comments (video_id)');
        $this->addSql('COMMENT ON COLUMN comments.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(45) NOT NULL, last_name VARCHAR(45) NOT NULL, video_api_key VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TABLE videos (id INT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, duration INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29AA643212469DE2 ON videos (category_id)');
        $this->addSql('CREATE INDEX title_idx ON videos (title)');
        $this->addSql('CREATE TABLE likes (video_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(video_id, user_id))');
        $this->addSql('CREATE INDEX IDX_49CA4E7D29C1004E ON likes (video_id)');
        $this->addSql('CREATE INDEX IDX_49CA4E7DA76ED395 ON likes (user_id)');
        $this->addSql('CREATE TABLE dislikes (video_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(video_id, user_id))');
        $this->addSql('CREATE INDEX IDX_2DF3BE1129C1004E ON dislikes (video_id)');
        $this->addSql('CREATE INDEX IDX_2DF3BE11A76ED395 ON dislikes (user_id)');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A29C1004E FOREIGN KEY (video_id) REFERENCES videos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA643212469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D29C1004E FOREIGN KEY (video_id) REFERENCES videos (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dislikes ADD CONSTRAINT FK_2DF3BE1129C1004E FOREIGN KEY (video_id) REFERENCES videos (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dislikes ADD CONSTRAINT FK_2DF3BE11A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE categories DROP CONSTRAINT FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE videos DROP CONSTRAINT FK_29AA643212469DE2');
        $this->addSql('ALTER TABLE comments DROP CONSTRAINT FK_5F9E962A7E3C61F9');
        $this->addSql('ALTER TABLE likes DROP CONSTRAINT FK_49CA4E7DA76ED395');
        $this->addSql('ALTER TABLE dislikes DROP CONSTRAINT FK_2DF3BE11A76ED395');
        $this->addSql('ALTER TABLE comments DROP CONSTRAINT FK_5F9E962A29C1004E');
        $this->addSql('ALTER TABLE likes DROP CONSTRAINT FK_49CA4E7D29C1004E');
        $this->addSql('ALTER TABLE dislikes DROP CONSTRAINT FK_2DF3BE1129C1004E');
        $this->addSql('DROP SEQUENCE categories_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE comments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE videos_id_seq CASCADE');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE videos');
        $this->addSql('DROP TABLE likes');
        $this->addSql('DROP TABLE dislikes');
    }
}
