create database `visiboost`;

use `visiboost`;

create table `user`(
    `id` integer not null auto_increment,
    `firstname` varchar(100),
    `surname` varchar(100),
    `email` varchar(100),
    `password` varchar(1000),
    constraint `pk_user` primary key(`id`)
)engine=InnoDB auto_increment=1 default charset=utf8 collate=utf8_general_ci;