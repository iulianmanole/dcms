SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `dcms` ;
USE `dcms`;

-- -----------------------------------------------------
-- Table `dcms`.`equipments_manufacturers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `dcms`.`equipments_manufacturers` ;

CREATE  TABLE IF NOT EXISTS `dcms`.`equipments_manufacturers` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `dcms`.`equipments_racks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `dcms`.`equipments_racks` ;

CREATE  TABLE IF NOT EXISTS `dcms`.`equipments_racks` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `current_weight` INT(10) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `dcms`.`equipments_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `dcms`.`equipments_types` ;

CREATE  TABLE IF NOT EXISTS `dcms`.`equipments_types` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `type` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Primary type is mapped 1:1 to a class' ,
  `mappedClass` VARCHAR(100) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = latin1
COMMENT = 'Contains all the equipments types';


-- -----------------------------------------------------
-- Table `dcms`.`locations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `dcms`.`locations` ;

CREATE  TABLE IF NOT EXISTS `dcms`.`locations` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `type` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `dcms`.`equipments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `dcms`.`equipments` ;

CREATE  TABLE IF NOT EXISTS `dcms`.`equipments` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(60) NULL ,
  `is_model` BOOLEAN NULL DEFAULT FALSE COMMENT 'Determine whitch equipment is a model ( template). \n\n\n' ,
  `model_id` INT(10) NOT NULL COMMENT 'it the equipment is not a model ( is_model=false), then model_id represents the model it inherits.\n' ,
  `type` INT(10) NOT NULL ,
  `manufacturer` INT(10) NOT NULL ,
  `location` INT(10) NOT NULL ,
  `parent_equipment` INT(10) NOT NULL DEFAULT -1 COMMENT 'Specify parent ID. Will be used to group couple equipments under one ID. Example : IBM Servers with extensions.\n\nDefault: -1 ( does not have a parent.)' ,
  `SN` VARCHAR(45) NULL ,
  `PN` VARCHAR(45) NULL ,
  `weight` INT(10) NULL ,
  `width` INT NULL ,
  `depth` INT NULL ,
  `height` INT NULL ,
  `is_rackable` BOOLEAN NULL DEFAULT FALSE ,
  `height_eia_units` INT NULL ,
  `thermal_output` INT NULL ,
  `air_flow` INT NULL ,
  `require_grounding` BOOLEAN NULL ,
  INDEX fk_manufacturer (`manufacturer` ASC) ,
  INDEX fk_location (`location` ASC) ,
  INDEX fk_type (`type` ASC) ,
  INDEX fk_model_id (`model_id` ASC) ,
  INDEX fk_parent (`parent_equipment` ASC) ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_manufacturer`
    FOREIGN KEY (`manufacturer` )
    REFERENCES `dcms`.`equipments_manufacturers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_location`
    FOREIGN KEY (`location` )
    REFERENCES `dcms`.`locations` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_type`
    FOREIGN KEY (`type` )
    REFERENCES `dcms`.`equipments_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_model_id`
    FOREIGN KEY (`model_id` )
    REFERENCES `dcms`.`equipments` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_parent`
    FOREIGN KEY (`parent_equipment` )
    REFERENCES `dcms`.`equipments` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
