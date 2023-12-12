create database dataware2;

create table utilisateur(
    id int auto_increment primary key,
    nom varchar(25),
    email varchar(50),
    password varchar(60),
    statut varchar(55),
    tache varchar(55),
    role varchar(50)
);

create table projet(
    id int auto_increment primary key,
    nom varchar(55),
    description varchar(255),
    date_creation date NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    date_limite date,
    statut varchar(55),
    id_user int,
    foreign key (id_user) references utilisateur(id)
);

create table equipe(
    id int auto_increment primary key,
    nom varchar(55),
    date_creation date NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    id_user int,
    id_projet int,
    foreign key (id_projet) references projet(id) ON DELETE CASCADE,
    foreign key (id_user) references utilisateur(id)
);


CREATE TABLE MembreEquipe(
    id int auto_increment primary key,
    id_user int,
    id_equipe int,
    foreign key (id_user) references utilisateur(id) ON DELETE CASCADE,
    foreign key (id_equipe) references equipe(id) ON DELETE CASCADE
);


alter table
    utilisateur drop column tache;

alter table
    membreequipe
add
    column tache varchar(25);