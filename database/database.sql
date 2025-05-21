create database `visiboost`;

use `visiboost`;

create table `user`(
    `id` integer not null auto_increment,
    `firstname` varchar(100),
    `surname` varchar(100),
    `sub` CHAR(1),
    `email` varchar(100),
    `password` varchar(1000),
    constraint `pk_user` primary key(`id`)
)engine=InnoDB auto_increment=1 default charset=utf8 collate=utf8_general_ci;

create table `website`(
    `id` integer not null auto_increment,
    `domainname` varchar(200) not null,
    `idUser` integer not null,
    constraint `pk_website` primary key(`id`),
    constraint `fk_user` foreign key(`idUser`) references `user`(`id`)
)engine=InnoDB auto_increment=1 default charset=utf8 collate=utf8_general_ci;

create table `analysisType`(
    `id` integer not null auto_increment,
    `label` varchar(50),
    constraint `pk_analysisType` primary key(`id`) 
)engine=InnoDB auto_increment=1 default charset=utf8 collate=utf8_general_ci;

create table `analysis`(
    `date` DateTime not null,
    `idWebsite` integer not null,
    `idAnalysisType` integer not null,
    `result` TEXT,
    constraint `pk_analysis` primary key(`date`, `idWebsite`, `idAnalysisType`),
    constraint `fk_website` foreign key(`idWebsite`) references `website`(`id`),
    constraint `fk_analysisType` foreign key(`idAnalysisType`) references `analysisType`(`id`)
)engine=InnoDB auto_increment=1 default charset=utf8 collate=utf8_general_ci;