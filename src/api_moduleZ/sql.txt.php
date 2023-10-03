CREATE TABLE `API_moduleZ_files` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`add_user` varchar(10) DEFAULT NULL,
`add_date` datetime DEFAULT NULL,
`file` varchar(200) NOT NULL,
`portefeuille` varchar(35) NOT NULL,
`batch` varchar(40) NOT NULL,
PRIMARY KEY (`id`)
);