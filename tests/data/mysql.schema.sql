DROP TABLE IF EXISTS `php_em_example`;

CREATE TABLE `php_em_example` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(256) NOT NULL,
    `order` INT NOT NULL,
    INDEX(`order`)
);
