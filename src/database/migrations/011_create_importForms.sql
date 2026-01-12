-- Active: 1758301938336@@localhost@3306@questionary
create table importedSurveys (
    id int AUTO_INCREMENT PRIMARY KEY,
    survey_id int,
    user_id varchar(30),
    FOREIGN KEY (user_id) REFERENCES users (id),
    Foreign Key (survey_id) REFERENCES surveys (id)
);

drop table importedSurveys;