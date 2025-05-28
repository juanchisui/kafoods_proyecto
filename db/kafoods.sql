-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema kfoods
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema kfoods
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `kfoods` DEFAULT CHARACTER SET utf8 ;
USE `kfoods` ;

-- -----------------------------------------------------
-- Table `kfoods`.`plato`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kfoods`.`plato` (
  `idplato` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `descripcion` VARCHAR(100) NOT NULL,
  `precio` INT NOT NULL,
  PRIMARY KEY (`idplato`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kfoods`.`mesero`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kfoods`.`mesero` (
  `idmesero` INT NOT NULL,
  `nombre` VARCHAR(45) NOT NULL,
  `apellido` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idmesero`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kfoods`.`orden`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kfoods`.`orden` (
  `idorden` INT NOT NULL AUTO_INCREMENT,
  `total` INT NOT NULL,
  `fecha` DATE NOT NULL,
  `mesero_idmesero` INT NOT NULL,
  PRIMARY KEY (`idorden`),
  INDEX `fk_orden_mesero_idx` (`mesero_idmesero` ASC) VISIBLE,
  CONSTRAINT `fk_orden_mesero`
    FOREIGN KEY (`mesero_idmesero`)
    REFERENCES `kfoods`.`mesero` (`idmesero`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `kfoods`.`detalle_orden`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `kfoods`.`detalle_orden` (
  `iddetalle_orden` INT NOT NULL AUTO_INCREMENT,
  `cantidad` INT NOT NULL,
  `plato_idplato` INT NOT NULL,
  `orden_idorden` INT NOT NULL,
  PRIMARY KEY (`iddetalle_orden`),
  INDEX `fk_detalle_orden_plato1_idx` (`plato_idplato` ASC) VISIBLE,
  INDEX `fk_detalle_orden_orden1_idx` (`orden_idorden` ASC) VISIBLE,
  CONSTRAINT `fk_detalle_orden_plato1`
    FOREIGN KEY (`plato_idplato`)
    REFERENCES `kfoods`.`plato` (`idplato`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_detalle_orden_orden1`
    FOREIGN KEY (`orden_idorden`)
    REFERENCES `kfoods`.`orden` (`idorden`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `kfoods`.`mesero`
-- -----------------------------------------------------
START TRANSACTION;
USE `kfoods`;
INSERT INTO `kfoods`.`mesero` (`idmesero`, `nombre`, `apellido`) VALUES (1, 'juan', 'riveros');
INSERT INTO `kfoods`.`mesero` (`idmesero`, `nombre`, `apellido`) VALUES (2, 'karen ', 'quintero');

COMMIT;

