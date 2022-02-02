CREATE TABLE IF NOT EXISTS `questions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NULL DEFAULT NULL,
  `question` TEXT NULL DEFAULT NULL,
  `position` TINYINT(4) NULL DEFAULT NULL,
  `short_description` TEXT NULL DEFAULT NULL,
  `type` ENUM('M-1', 'M-2', 'M-3', 'M-4') NULL DEFAULT NULL COMMENT 'Module type for questions' ,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `options` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `option_value` VARCHAR(255) NULL DEFAULT NULL,
  `option_label` VARCHAR(255) NULL DEFAULT NULL,
  `position` TINYINT(4) NULL DEFAULT NULL,
  `answer_status` ENUM('correct', 'wrong', 'partly_correct') NULL DEFAULT NULL COMMENT 'If the selected option is correct or not.',
  `questions_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_options_questions1_idx` (`questions_id` ASC),
  CONSTRAINT `fk_options_questions1`
    FOREIGN KEY (`questions_id`)
    REFERENCES `questions` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `questionnaires` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `users_id` INT(11) NOT NULL,
  `survey_date` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_education_module_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_education_module_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `questionnaires_has_options` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `questionnaires_id` INT(11) NOT NULL,
  `options_id` INT(11) NOT NULL,
  `questions_id` INT(11) NOT NULL,
  `response` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` DATETIME NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_questionnaires_has_options_options1_idx` (`options_id` ASC),
  INDEX `fk_questionnaires_has_options_questionnaires1_idx` (`questionnaires_id` ASC),
  INDEX `fk_questionnaires_has_options_questions1_idx` (`questions_id` ASC),
  CONSTRAINT `fk_questionnaires_has_options_questionnaires1`
    FOREIGN KEY (`questionnaires_id`)
    REFERENCES `firefighter`.`questionnaires` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_questionnaires_has_options_options1`
    FOREIGN KEY (`options_id`)
    REFERENCES `firefighter`.`options` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_questionnaires_has_options_questions1`
    FOREIGN KEY (`questions_id`)
    REFERENCES `firefighter`.`questions` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;




CREATE TABLE IF NOT EXISTS `countries` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `country` VARCHAR(255) NULL DEFAULT NULL,
  `iso_code` VARCHAR(45) NULL DEFAULT NULL,
  `position` INT(11) NULL DEFAULT NULL COMMENT 'To show countries byt position',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `users` 
ROW_FORMAT = DEFAULT ,
ADD COLUMN `countries_id` INT(11) NULL DEFAULT NULL AFTER `updated_at`,
ADD INDEX `fk_users_countries1_idx` (`countries_id` ASC);
ALTER TABLE `users` 
ADD CONSTRAINT `fk_users_countries1`
  FOREIGN KEY (`countries_id`)
  REFERENCES `countries` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `users` 
ADD COLUMN `address` TEXT NULL DEFAULT NULL AFTER `countries_id`;


INSERT INTO `countries` (`id`, `country`, `iso_code`, `position`) VALUES
(1, 'Afghanistan', 'AF', 2),
(2, 'Albania', 'AL', 3),
(3, 'Algeria', 'DZ', 4),
(4, 'American Samoa', 'DS', 5),
(5, 'Andorra', 'AD', 6),
(6, 'Angola', 'AO', 7),
(7, 'Anguilla', 'AI', 8),
(8, 'Antarctica', 'AQ', 9),
(9, 'Antigua and Barbuda', 'AG', 10),
(10, 'Argentina', 'AR', 11),
(11, 'Armenia', 'AM', 12),
(12, 'Aruba', 'AW', 13),
(13, 'Australia', 'AU', 14),
(14, 'Austria', 'AT', 15),
(15, 'Azerbaijan', 'AZ', 16),
(16, 'Bahamas', 'BS', 17),
(17, 'Bahrain', 'BH', 18),
(18, 'Bangladesh', 'BD', 19),
(19, 'Barbados', 'BB', 20),
(20, 'Belarus', 'BY', 21),
(21, 'Belgium', 'BE', 22),
(22, 'Belize', 'BZ', 23),
(23, 'Benin', 'BJ', 24),
(24, 'Bermuda', 'BM', 25),
(25, 'Bhutan', 'BT', 26),
(26, 'Bolivia', 'BO', 27),
(27, 'Bosnia and Herzegovina', 'BA', 28),
(28, 'Botswana', 'BW', 29),
(29, 'Bouvet Island', 'BV', 30),
(30, 'Brazil', 'BR', 31),
(31, 'British Indian Ocean Territory', 'IO', 32),
(32, 'Brunei Darussalam', 'BN', 33),
(33, 'Bulgaria', 'BG', 34),
(34, 'Burkina Faso', 'BF', 35),
(35, 'Burundi', 'BI', 36),
(36, 'Cambodia', 'KH', 37),
(37, 'Cameroon', 'CM', 38),
(38, 'Canada', 'CA', 39),
(39, 'Cape Verde', 'CV', 40),
(40, 'Cayman Islands', 'KY', 41),
(41, 'Central African Republic', 'CF', 42),
(42, 'Chad', 'TD', 43),
(43, 'Chile', 'CL', 44),
(44, 'China', 'CN', 45),
(45, 'Christmas Island', 'CX', 46),
(46, 'Cocos (Keeling) Islands', 'CC', 47),
(47, 'Colombia', 'CO', 48),
(48, 'Comoros', 'KM', 49),
(49, 'Congo', 'CG', 50),
(50, 'Cook Islands', 'CK', 51),
(51, 'Costa Rica', 'CR', 52),
(52, 'Croatia (Hrvatska)', 'HR', 53),
(53, 'Cuba', 'CU', 54),
(54, 'Cyprus', 'CY', 55),
(55, 'Czech Republic', 'CZ', 56),
(56, 'Denmark', 'DK', 57),
(57, 'Djibouti', 'DJ', 58),
(58, 'Dominica', 'DM', 59),
(59, 'Dominican Republic', 'DO', 60),
(60, 'East Timor', 'TP', 61),
(61, 'Ecuador', 'EC', 62),
(62, 'Egypt', 'EG', 63),
(63, 'El Salvador', 'SV', 64),
(64, 'Equatorial Guinea', 'GQ', 65),
(65, 'Eritrea', 'ER', 66),
(66, 'Estonia', 'EE', 67),
(67, 'Ethiopia', 'ET', 68),
(68, 'Falkland Islands (Malvinas)', 'FK', 69),
(69, 'Faroe Islands', 'FO', 70),
(70, 'Fiji', 'FJ', 71),
(71, 'Finland', 'FI', 72),
(72, 'France', 'FR', 73),
(73, 'France, Metropolitan', 'FX', 74),
(74, 'French Guiana', 'GF', 75),
(75, 'French Polynesia', 'PF', 76),
(76, 'French Southern Territories', 'TF', 77),
(77, 'Gabon', 'GA', 78),
(78, 'Gambia', 'GM', 79),
(79, 'Georgia', 'GE', 80),
(80, 'Germany', 'DE', 81),
(81, 'Ghana', 'GH', 82),
(82, 'Gibraltar', 'GI', 83),
(83, 'Guernsey', 'GK', 84),
(84, 'Greece', 'GR', 85),
(85, 'Greenland', 'GL', 86),
(86, 'Grenada', 'GD', 87),
(87, 'Guadeloupe', 'GP', 88),
(88, 'Guam', 'GU', 89),
(89, 'Guatemala', 'GT', 90),
(90, 'Guinea', 'GN', 91),
(91, 'Guinea-Bissau', 'GW', 92),
(92, 'Guyana', 'GY', 93),
(93, 'Haiti', 'HT', 94),
(94, 'Heard and Mc Donald Islands', 'HM', 95),
(95, 'Honduras', 'HN', 96),
(96, 'Hong Kong', 'HK', 97),
(97, 'Hungary', 'HU', 98),
(98, 'Iceland', 'IS', 99),
(99, 'India', 'IN', 100),
(100, 'Isle of Man', 'IM', 101),
(101, 'Indonesia', 'ID', 102),
(102, 'Iran (Islamic Republic of)', 'IR', 103),
(103, 'Iraq', 'IQ', 104),
(104, 'Ireland', 'IE', 105),
(105, 'Israel', 'IL', 106),
(106, 'Italy', 'IT', 107),
(107, 'Ivory Coast', 'CI', 108),
(108, 'Jersey', 'JE', 109),
(109, 'Jamaica', 'JM', 110),
(110, 'Japan', 'JP', 111),
(111, 'Jordan', 'JO', 112),
(112, 'Kazakhstan', 'KZ', 113),
(113, 'Kenya', 'KE', 114),
(114, 'Kiribati', 'KI', 115),
(115, 'Korea, Democratic People\'s Republic of', 'KP', 116),
(116, 'Korea, Republic of', 'KR', 117),
(117, 'Kosovo', 'XK', 118),
(118, 'Kuwait', 'KW', 119),
(119, 'Kyrgyzstan', 'KG', 120),
(120, 'Lao People\'s Democratic Republic', 'LA', 121),
(121, 'Latvia', 'LV', 122),
(122, 'Lebanon', 'LB', 123),
(123, 'Lesotho', 'LS', 124),
(124, 'Liberia', 'LR', 125),
(125, 'Libyan Arab Jamahiriya', 'LY', 126),
(126, 'Liechtenstein', 'LI', 127),
(127, 'Lithuania', 'LT', 128),
(128, 'Luxembourg', 'LU', 129),
(129, 'Macau', 'MO', 130),
(130, 'North Macedonia', 'MK', 131),
(131, 'Madagascar', 'MG', 132),
(132, 'Malawi', 'MW', 133),
(133, 'Malaysia', 'MY', 134),
(134, 'Maldives', 'MV', 135),
(135, 'Mali', 'ML', 136),
(136, 'Malta', 'MT', 137),
(137, 'Marshall Islands', 'MH', 138),
(138, 'Martinique', 'MQ', 139),
(139, 'Mauritania', 'MR', 140),
(140, 'Mauritius', 'MU', 141),
(141, 'Mayotte', 'TY', 142),
(142, 'Mexico', 'MX', 143),
(143, 'Micronesia, Federated States of', 'FM', 144),
(144, 'Moldova, Republic of', 'MD', 145),
(145, 'Monaco', 'MC', 146),
(146, 'Mongolia', 'MN', 147),
(147, 'Montenegro', 'ME', 148),
(148, 'Montserrat', 'MS', 149),
(149, 'Morocco', 'MA', 150),
(150, 'Mozambique', 'MZ', 151),
(151, 'Myanmar', 'MM', 152),
(152, 'Namibia', 'NA', 153),
(153, 'Nauru', 'NR', 154),
(154, 'Nepal', 'NP', 155),
(155, 'Netherlands', 'NL', 156),
(156, 'Netherlands Antilles', 'AN', 157),
(157, 'New Caledonia', 'NC', 158),
(158, 'New Zealand', 'NZ', 159),
(159, 'Nicaragua', 'NI', 160),
(160, 'Niger', 'NE', 161),
(161, 'Nigeria', 'NG', 162),
(162, 'Niue', 'NU', 163),
(163, 'Norfolk Island', 'NF', 164),
(164, 'Northern Mariana Islands', 'MP', 165),
(165, 'Norway', 'NO', 166),
(166, 'Oman', 'OM', 167),
(167, 'Pakistan', 'PK', 168),
(168, 'Palau', 'PW', 169),
(169, 'Palestine', 'PS', 170),
(170, 'Panama', 'PA', 171),
(171, 'Papua New Guinea', 'PG', 172),
(172, 'Paraguay', 'PY', 173),
(173, 'Peru', 'PE', 174),
(174, 'Philippines', 'PH', 175),
(175, 'Pitcairn', 'PN', 176),
(176, 'Poland', 'PL', 177),
(177, 'Portugal', 'PT', 178),
(178, 'Puerto Rico', 'PR', 179),
(179, 'Qatar', 'QA', 180),
(180, 'Reunion', 'RE', 181),
(181, 'Romania', 'RO', 182),
(182, 'Russian Federation', 'RU', 183),
(183, 'Rwanda', 'RW', 184),
(184, 'Saint Kitts and Nevis', 'KN', 185),
(185, 'Saint Lucia', 'LC', 186),
(186, 'Saint Vincent and the Grenadines', 'VC', 187),
(187, 'Samoa', 'WS', 188),
(188, 'San Marino', 'SM', 189),
(189, 'Sao Tome and Principe', 'ST', 190),
(190, 'Saudi Arabia', 'SA', 191),
(191, 'Senegal', 'SN', 192),
(192, 'Serbia', 'RS', 193),
(193, 'Seychelles', 'SC', 194),
(194, 'Sierra Leone', 'SL', 195),
(195, 'Singapore', 'SG', 196),
(196, 'Slovakia', 'SK', 197),
(197, 'Slovenia', 'SI', 198),
(198, 'Solomon Islands', 'SB', 199),
(199, 'Somalia', 'SO', 200),
(200, 'South Africa', 'ZA', 201),
(201, 'South Georgia South Sandwich Islands', 'GS', 202),
(202, 'South Sudan', 'SS', 203),
(203, 'Spain', 'ES', 204),
(204, 'Sri Lanka', 'LK', 205),
(205, 'St. Helena', 'SH', 206),
(206, 'St. Pierre and Miquelon', 'PM', 207),
(207, 'Sudan', 'SD', 208),
(208, 'Suriname', 'SR', 209),
(209, 'Svalbard and Jan Mayen Islands', 'SJ', 210),
(210, 'Swaziland', 'SZ', 211),
(211, 'Sweden', 'SE', 212),
(212, 'Switzerland', 'CH', 213),
(213, 'Syrian Arab Republic', 'SY', 214),
(214, 'Taiwan', 'TW', 215),
(215, 'Tajikistan', 'TJ', 216),
(216, 'Tanzania, United Republic of', 'TZ', 217),
(217, 'Thailand', 'TH', 218),
(218, 'Togo', 'TG', 219),
(219, 'Tokelau', 'TK', 220),
(220, 'Tonga', 'TO', 221),
(221, 'Trinidad and Tobago', 'TT', 222),
(222, 'Tunisia', 'TN', 223),
(223, 'Turkey', 'TR', 224),
(224, 'Turkmenistan', 'TM', 225),
(225, 'Turks and Caicos Islands', 'TC', 226),
(226, 'Tuvalu', 'TV', 227),
(227, 'Uganda', 'UG', 228),
(228, 'Ukraine', 'UA', 229),
(229, 'United Arab Emirates', 'AE', 230),
(230, 'United Kingdom', 'GB', 231),
(231, 'United States', 'US', 1),
(232, 'United States minor outlying islands', 'UM', 233),
(233, 'Uruguay', 'UY', 234),
(234, 'Uzbekistan', 'UZ', 235),
(235, 'Vanuatu', 'VU', 236),
(236, 'Vatican City State', 'VA', 237),
(237, 'Venezuela', 'VE', 238),
(238, 'Vietnam', 'VN', 239),
(239, 'Virgin Islands (British)', 'VG', 240),
(240, 'Virgin Islands (U.S.)', 'VI', 241),
(241, 'Wallis and Futuna Islands', 'WF', 242),
(242, 'Western Sahara', 'EH', 243),
(243, 'Yemen', 'YE', 244),
(244, 'Zaire', 'ZR', 245),
(245, 'Zambia', 'ZM', 246),
(246, 'Zimbabwe', 'ZW', 247);


INSERT INTO `questions` (`id`, `title`, `question`, `position`, `short_description`, `type`) VALUES
(1, 'Question 1', 'Firefighters are significantly more likely to be diagnosed with cancer compared to the general population.', 1, 'Firefighters have a 9% higher risk of being diagnosed with cancer compared to the general population.', 'M-1'),
(2, 'Question 1', 'Firefighters face risk of exposure mainly through', 1, 'Firefighters face risk of exposure from all of the above', 'M-2'),
(3, 'Question 2', 'Off gassing of contaminants from gear increases when', 2, 'Firefighters mainly face risk of exposure when temperature increases or contaminated gear is not bagged', 'M-2'),
(4, 'Question 1', 'Exposure at the station can be minimized by', 1, 'Keeping doors from the bay to other areas and showering within the hour will minimize exposure at the station', 'M-3'),
(5, 'Question 2', 'At the station, actions that can help reduce risks include:', 2, 'Keeping doors from the bay to other areas and showering within the hour will minimize exposure at the station', 'M-3'),
(6, 'Question 1', 'Which of the following organizational level factors help reduce cancer risk for firefighters:', 1, 'Written policies and dedicated occupational health and safety officers will help reduce cancer risk for firefighters', 'M-4');


INSERT INTO `options` (`id`, `option_value`, `option_label`, `position`, `answer_status`, `questions_id`) VALUES
(1, '0', 'True', 1, 'correct', 1),
(2, '1', 'False', 2, 'wrong', 1),
(3, '1', 'Absorption', 1, 'wrong', 2),
(4, '2', 'Inhalation', 2, 'wrong', 2),
(5, '3', 'Ingestion', 3, 'wrong', 2),
(6, '4', 'Inhalation and Absorption', 4, 'wrong', 2),
(7, '5', 'All of the above', 5, 'correct', 2),
(8, 'A', 'A) Temperature increases', 1, 'partly_correct', 3),
(9, 'B', 'B) Gear becomes wet', 2, 'wrong', 3),
(10, 'C', 'C) Contaminated gear is not bagged', 3, 'partly_correct', 3),
(11, 'D', 'D) Bagged gear is placed in designated outside compartments', 4, 'wrong', 3),
(12, 'E', 'E) A & C', 5, 'correct', 3),
(13, 'A', 'A) Keeping doors between the garage and other areas closed', 1, 'partly_correct', 4),
(14, 'B', 'B) Keeping engine running in a closed bay', 2, 'wrong', 4),
(15, 'C', 'C) Showering within the hour at the station', 3, 'partly_correct', 4),
(16, 'D', 'D) A & C', 4, 'correct', 4),
(17, 'E', 'All of the above', 5, 'wrong', 4),
(18, 'A', 'A) Reducing diesel exhaust in bays', 1, 'wrong', 5),
(19, 'B', 'B) Keeping gear away from living quarters', 2, 'wrong', 5),
(20, 'C', 'C) Using commercial grade washers designed for bunker gear', 3, 'wrong', 5),
(21, 'D', 'D) Keeping lockers in well-ventilated areas', 4, 'wrong', 5),
(22, 'E', 'All of the above', 5, 'correct', 5),
(23, 'A', 'A) Keeping doors between the garage and other areas closed', 1, 'wrong', 6),
(24, 'B', 'B) Keeping engine running in a closed bay', 2, 'partly_correct', 6),
(25, 'C', 'C) Showering within the hour at the station', 3, 'partly_correct', 6),
(26, 'D', 'D) A & C', 4, 'correct', 6),
(27, 'E', 'All of the above', 5, 'wrong', 6);






