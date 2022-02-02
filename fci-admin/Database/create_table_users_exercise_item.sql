CREATE TABLE IF NOT EXISTS `users_has_exercise_item` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `users_id` INT(11) NOT NULL,
  `type` ENUM('TEXT_ITEM', 'RADIO', 'CHECKBOX', 'RATING', 'TWO_COL', 'GOAL', 'GOAL_TRACKING') NULL DEFAULT NULL,
  `exercise_item_id` INT(11) NULL DEFAULT NULL,
  `exercises_id` INT(11) NOT NULL,
  `exercise_item_details_id` INT(11) NULL DEFAULT NULL,
  `goals_id` INT(11) NULL DEFAULT NULL,
  `response_1` VARCHAR(255) NULL DEFAULT NULL,
  `response_2` VARCHAR(255) NULL DEFAULT NULL,
  `week_info_id` INT(11) NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_users_has_exercise_item_exercise_item1_idx` (`exercise_item_id` ASC),
  INDEX `fk_users_has_exercise_item_users1_idx` (`users_id` ASC),
  INDEX `fk_users_has_exercise_item_exercises1_idx` (`exercises_id` ASC),
  INDEX `fk_users_has_exercise_item_exercise_item_details1_idx` (`exercise_item_details_id` ASC),
  INDEX `fk_users_has_exercise_item_goals1_idx` (`goals_id` ASC),
  INDEX `fk_users_has_exercise_item_week_info1_idx` (`week_info_id` ASC),
  CONSTRAINT `fk_users_has_exercise_item_exercise_item1`
    FOREIGN KEY (`exercise_item_id`)
    REFERENCES `exercise_item` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_exercise_item_exercise_item_details1`
    FOREIGN KEY (`exercise_item_details_id`)
    REFERENCES `exercise_item_details` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_exercise_item_exercises1`
    FOREIGN KEY (`exercises_id`)
    REFERENCES `exercises` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_exercise_item_goals1`
    FOREIGN KEY (`goals_id`)
    REFERENCES `goals` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_exercise_item_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_exercise_item_week_info1`
    FOREIGN KEY (`week_info_id`)
    REFERENCES `week_info` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;
