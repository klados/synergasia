create database if not exists synergasia;
use synergasia;

drop table if exists friends;
drop table if exists project_data;
drop table if exists project_members;
drop table if exists projects;
drop table if exists users;


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` enum('inactive','ban','active') DEFAULT 'inactive',
  `join_day` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fullname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB ;


CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_email` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `description` varchar(500) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
   FOREIGN KEY (`owner_email`) REFERENCES `users` (`email`)
) ENGINE=InnoDB ;


CREATE TABLE `project_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
   FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
   FOREIGN KEY (`email`) REFERENCES `users` (`email`)
) ENGINE=InnoDB ;


CREATE TABLE `friends` (
  `myself` varchar(50) NOT NULL,
  `target` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`myself`,`target`),
  KEY `target` (`target`),
  FOREIGN KEY (`myself`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`target`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


CREATE TABLE `project_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `type` enum('link','notes','file') NOT NULL,
  `data` varchar(1000) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `owner_email` varchar(50) DEFAULT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
   FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
   FOREIGN KEY (`owner_email`) REFERENCES `users` (`email`)
) ENGINE=InnoDB ;







