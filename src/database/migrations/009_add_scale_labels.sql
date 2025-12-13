-- Active: 1765462021089@@127.0.0.1@3306@questionary
ALTER TABLE questions
ADD COLUMN scale_min_label VARCHAR(64) DEFAULT 'Pas du tout',
ADD COLUMN scale_max_label VARCHAR(64) DEFAULT 'Tout à fait';