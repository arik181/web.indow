SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- -----------------------------------------------------
-- Schema indowwin_db
-- -----------------------------------------------------
DROP DATABASE IF EXISTS indowwin_db;
GRANT USAGE ON *.* TO `indowwin_user`@`localhost`;
DROP USER `indowwin_user`@`localhost`;

CREATE DATABASE indowwin_db;
CREATE USER `indowwin_user`@`localhost` IDENTIFIED BY 'VM9{fpmugait';
USE `indowwin_db` ;
GRANT ALL ON indowwin_db.* TO `indowwin_user`@`localhost`;

