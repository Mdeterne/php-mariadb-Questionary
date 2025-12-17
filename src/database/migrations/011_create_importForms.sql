-- Active: 1758301938336@@localhost@3306@questionary
create table importForms (
    survey_id int(11) PRIMARY KEY,
    user_id varchar(25),
    FOREIGN KEY (user_id) REFERENCES users (id),
    Foreign Key (survey_id) REFERENCES surveys (id)
);