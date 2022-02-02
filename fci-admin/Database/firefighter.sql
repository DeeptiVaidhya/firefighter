CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `arm` enum('STUDY') DEFAULT 'STUDY',
  `type` enum('CONTENT','TOPIC') DEFAULT NULL,
  `content_name` varchar(255) DEFAULT NULL COMMENT 'Chapter/Content name.',
  `slug` varchar(255) DEFAULT NULL COMMENT 'Unique name for routing.',
  `icon_class` varchar(255) DEFAULT NULL COMMENT 'Uploaded image name.',
  `title` varchar(255) DEFAULT NULL COMMENT 'Title of a content/topic/sub-topic',
  `intro_text` text COMMENT 'First paragraph text.',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `content_id` int(11) DEFAULT NULL COMMENT 'Parent content id like a chapter has many topics...',
  `position` int(11) DEFAULT NULL COMMENT 'Position of a chapter/topic/sub-topic.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='All contents and the topics/sub-topics.';

-- --------------------------------------------------------

--
-- Table structure for table `content_details`
--

CREATE TABLE `content_details` (
  `id` int(11) NOT NULL,
  `text` text,
  `content_id` int(11) DEFAULT NULL COMMENT 'Sub content for a page content/topic.',
  `resources_id` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `image_credit` varchar(255) DEFAULT NULL COMMENT 'Image credit to be shown after image as a image source.',
  `position` int(11) DEFAULT NULL COMMENT 'Page content position.',
  `topic_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Content of a page content/chapter, topic and or sub-topic.';

-- --------------------------------------------------------

--
-- Table structure for table `content_has_resources`
--

CREATE TABLE `content_has_resources` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `resources_id` int(11) NOT NULL,
  `position` int(11) DEFAULT NULL COMMENT 'Resource position to be showned for a Content.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of all resources shown on sidebar section.';

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `sub_header` varchar(255) DEFAULT NULL,
  `description` text,
  `content_id` int(11) NOT NULL COMMENT 'Chapter reference.',
  `position` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `worksheet_id` int(11) DEFAULT NULL COMMENT 'PDF file reference.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `exercise_item`
--

CREATE TABLE `exercise_item` (
  `id` int(11) NOT NULL,
  `type` enum('TEXT_ITEM','RADIO','CHECKBOX','RATING','TWO_COL','GOAL','GOAL_TRACKING') DEFAULT NULL,
  `primary_prompt` varchar(255) DEFAULT NULL,
  `secondary_prompt` varchar(255) DEFAULT NULL,
  `text_field_size` enum('T_1_LINE','T_2_LINE','T_3_LINE') DEFAULT NULL,
  `first_heading` varchar(255) DEFAULT NULL COMMENT 'First column text',
  `second_heading` varchar(255) DEFAULT NULL COMMENT 'Second column text',
  `number_of_items` tinyint(4) DEFAULT NULL COMMENT 'Total number of other rating items or repeats in two columns',
  `position` tinyint(4) DEFAULT '0',
  `exercises_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `exercise_item_details`
--

CREATE TABLE `exercise_item_details` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `exercise_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `users_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Favourite sub topics';

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Original file name',
  `unique_name` varchar(255) DEFAULT NULL COMMENT 'System generated name for uniqueness.',
  `type` varchar(45) DEFAULT NULL COMMENT 'Type of File',
  `size` int(11) DEFAULT NULL COMMENT 'File size in KB.',
  `created_at` datetime DEFAULT NULL,
  `is_active` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `type` enum('READING','AUDIO','VIDEO','WEBSITE') DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT 'Resources title.',
  `link` varchar(255) DEFAULT NULL COMMENT 'External link for the resource.',
  `files_id` int(11) DEFAULT NULL COMMENT 'Audio file reference for Resource audio.',
  `description` text COMMENT 'Description if any.',
  `created_at` varchar(45) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Having list of all audio, video, website, reading resources.';

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `key` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Having site settings like enabling questionnaire in week 1.';

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(1000) DEFAULT NULL COMMENT 'Unique username to login in app.',
  `password` varchar(100) DEFAULT NULL COMMENT 'User password.',
  `salt` varchar(100) DEFAULT NULL COMMENT 'Salt used to login a user.',
  `email` varchar(1000) DEFAULT NULL COMMENT 'User''s email.',
  `first_name` varchar(1000) DEFAULT NULL COMMENT 'First name.',
  `last_name` varchar(1000) DEFAULT NULL COMMENT 'Last name.',
  `gender` enum('male','female','other') DEFAULT NULL COMMENT 'User''s Gender.',
  `phone_number` varchar(1000) DEFAULT NULL COMMENT 'Encrypted Phone Number of a user.',
  `profile_picture` varchar(255) DEFAULT NULL COMMENT 'Profile picture.',
  `subject_id` varchar(50) DEFAULT NULL COMMENT 'Used for patients.',
  `is_active` tinyint(4) DEFAULT NULL COMMENT '0 for inactive, 1 for active',
  `is_authorized` tinyint(4) DEFAULT NULL COMMENT 'Is user is authorized or not. 0 for not and 1 for authorized.',
  `user_type` tinyint(4) DEFAULT NULL COMMENT '1 for Admin, 2 for Research Staff, 3 for Patients.',
  `arm_alloted` enum('study') DEFAULT 'study' COMMENT 'Arm alot to a participant.',
  `last_login` datetime DEFAULT NULL,
  `last_access_date` datetime DEFAULT NULL COMMENT 'Last date when a use is accessing the app',
  `login_attempts` tinyint(4) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL COMMENT 'Forgot password code sent to user email id.',
  `forgotten_password_time` int(11) DEFAULT NULL COMMENT 'Forgotton password time, will expire in some duration',
  `authorization_code` varchar(255) DEFAULT NULL COMMENT 'Verfication code sent to user when user is registered.',
  `authorization_time` int(11) DEFAULT NULL COMMENT 'Verification code time and will expire in a time duration',
  `created_by` int(11) DEFAULT NULL COMMENT 'Who creates a user.',
  `created_at` datetime DEFAULT NULL COMMENT 'When a user is created.',
  `activation_date` datetime DEFAULT NULL COMMENT 'When a week has started.',
  `access_code` varchar(20) DEFAULT NULL COMMENT 'Used when a participant will unable to set password using the verification link.',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time when a user is updated.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of all users. User type will differentiate whether a user is patient/provider/researcher/admin.' ROW_FORMAT=COMPACT;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `salt`, `email`, `first_name`, `last_name`, `gender`, `phone_number`, `profile_picture`, `subject_id`, `is_active`, `is_authorized`, `user_type`, `arm_alloted`, `last_login`, `last_access_date`, `login_attempts`, `forgotten_password_code`, `forgotten_password_time`, `authorization_code`, `authorization_time`, `created_by`, `created_at`, `activation_date`, `access_code`, `updated_at`) VALUES
(1, 'ddb80587ab9ef79c1d296f9a1adff7441209de78da4fc2acb8120ccbf72c8e1c8a28e88074424b4309fc1610564b8e1286eadc90da39b0bfefecc6e89188b72eHWljb5hSs/zpgnWGzQHRAZ9xSyqQ8ZmxdIbvGnY0evI=', '$2y$08$ejghcnpDcu/ASbu8QY1Pd.CndOHxzHrE9WwcXgNGpUkxiIqM/pgW6', 'jehE.UrErAtIaZtRspTTou', '6e5c964567ba26a6b2ee8a4a9601062c4860d7daf3124799a2e97a5b92f982fd144c6d7e9d49ddf66edddc3a353dd65d697e17a02daa8aa616f58ef9113f8cd5KM1/eHNyaLr9cXHHQjLI6ycjkcbTfB4owRYz6DLMyTJuQE5k18x0aHAARRjDAMpm', '3de20a9994b1fee4b6b405dde13b32e3a5b262cebedd4d35e0423ba7b04539d41290395547eb9561c9497ed6540c8ae89c07c00b4e2240a1185593b4d1d6bdd7cAPBbIPQhigkXi4+Y5RAPpxOCTyVb90GYwqizzmwKHw=', 'ddb80587ab9ef79c1d296f9a1adff7441209de78da4fc2acb8120ccbf72c8e1c8a28e88074424b4309fc1610564b8e1286eadc90da39b0bfefecc6e89188b72eHWljb5hSs/zpgnWGzQHRAZ9xSyqQ8ZmxdIbvGnY0evI=', 'male', '0b1655c76c31a349e3c70daef4739842942859aaa7eeb92284f732b5c9d994b3a727fb2c619b72dabd21996fa1ad3ce7762ed5a19c98ac0b6212a4ac7f59111eNZGIuriWx6eq/cVGVKDZnhuEpkRX+7v8ra4ZNkGDbZ8=', 'Koala-5dc4ff62cf597.jpg', NULL, 1, 1, 1, NULL, '2020-02-05 14:40:32', '2020-02-05 14:40:32', 0, NULL, NULL, NULL, NULL, NULL, '2018-06-28 11:54:49', NULL, NULL, '2019-10-29 18:22:57');

-- --------------------------------------------------------

--
-- Table structure for table `users_has_content`
--

CREATE TABLE `users_has_content` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `resources_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `total_player_time` int(11) DEFAULT NULL COMMENT 'Total video/audio time.',
  `left_player_time` int(11) DEFAULT NULL COMMENT 'Left time for audio/video to get completed status.',
  `content_callee_page` varchar(255) DEFAULT NULL COMMENT 'From where a content is called.',
  `created_at` datetime DEFAULT NULL,
  `content_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_has_content_has_exercise_item`
--

CREATE TABLE `users_has_content_has_exercise_item` (
  `users_has_content_id` int(11) NOT NULL,
  `exercise_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_secure`
--

CREATE TABLE `users_secure` (
  `id` int(11) NOT NULL,
  `password_history` varchar(100) DEFAULT NULL COMMENT 'Last password used by user.',
  `salt_history` varchar(100) DEFAULT NULL COMMENT 'Salt used by user.',
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Password''s last updated time.',
  `users_id` int(11) NOT NULL COMMENT 'Link to user.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Track password history';

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL,
  `type` enum('page','topic','button') DEFAULT NULL COMMENT 'Type of action like clicking on page, topic, button.',
  `action` varchar(255) DEFAULT NULL COMMENT 'Actions refers to View video, Expand topic, Read more etc.',
  `page_name` varchar(255) DEFAULT NULL COMMENT 'Page title, from where a user activity log generated.',
  `title` text COMMENT 'The title of the resource associated with the type field.',
  `device_info` varchar(255) DEFAULT NULL COMMENT 'Detail of device like browser and OS.',
  `users_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tracking user activity like page, topic, etc clicks.';

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `users_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Managing tokens and device detils (if any) for user.';

-- --------------------------------------------------------

--
-- Table structure for table `week_info`
--

CREATE TABLE `week_info` (
  `id` int(11) NOT NULL,
  `week_number` int(11) DEFAULT NULL COMMENT 'Showing week number for a patient.',
  `week_starts_at` datetime DEFAULT NULL COMMENT 'When a week is started.',
  `week_ends_at` datetime DEFAULT NULL COMMENT 'When a week ends.',
  `users_id` int(11) NOT NULL COMMENT 'Link to patient.',
  `total_time_spent_in_week` int(11) DEFAULT NULL COMMENT 'Total time spent in a week in seconds.',
  `event` text,
  `is_study_week` enum('0','1') DEFAULT '0' COMMENT 'If a participant access the site after eight week study.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Having week information for a user.';

-- --------------------------------------------------------

--
-- Table structure for table `week_sessions`
--

CREATE TABLE `week_sessions` (
  `id` int(11) NOT NULL,
  `week_info_id` int(11) NOT NULL COMMENT 'Reference for a week',
  `start_time` datetime DEFAULT NULL COMMENT 'When starts a session for a week.',
  `end_time` datetime DEFAULT NULL COMMENT 'Seesions end time for a week.',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Saving time details for a week.';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_content_content1_idx` (`content_id`);

--
-- Indexes for table `content_details`
--
ALTER TABLE `content_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_content_details_content1_idx` (`content_id`),
  ADD KEY `fk_content_details_resources1_idx` (`resources_id`),
  ADD KEY `fk_content_details_files1_idx` (`image_id`),
  ADD KEY `fk_content_details_content2_idx` (`topic_id`);

--
-- Indexes for table `content_has_resources`
--
ALTER TABLE `content_has_resources`
  ADD PRIMARY KEY (`id`,`content_id`,`resources_id`),
  ADD KEY `fk_content_has_resources_resources1_idx` (`resources_id`),
  ADD KEY `fk_content_has_resources_content1_idx` (`content_id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_exercises_content1_idx` (`content_id`),
  ADD KEY `fk_exercises_files1_idx` (`worksheet_id`);

--
-- Indexes for table `exercise_item`
--
ALTER TABLE `exercise_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_exercise_item_exercises1_idx` (`exercises_id`);

--
-- Indexes for table `exercise_item_details`
--
ALTER TABLE `exercise_item_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_exercise_item_details_exercise_item1_idx` (`exercise_item_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`users_id`,`content_id`),
  ADD KEY `fk_users_has_content_content1_idx` (`content_id`),
  ADD KEY `fk_users_has_content_users1_idx` (`users_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_resources_files1_idx` (`files_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_users1_idx1` (`created_by`);

--
-- Indexes for table `users_has_content`
--
ALTER TABLE `users_has_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_has_content_resources1_idx` (`resources_id`),
  ADD KEY `fk_users_has_content_users2_idx` (`users_id`),
  ADD KEY `fk_users_has_content_content2_idx` (`content_id`);

--
-- Indexes for table `users_has_content_has_exercise_item`
--
ALTER TABLE `users_has_content_has_exercise_item`
  ADD PRIMARY KEY (`users_has_content_id`,`exercise_item_id`),
  ADD KEY `fk_users_has_content_has_exercise_item_exercise_item1_idx` (`exercise_item_id`),
  ADD KEY `fk_users_has_content_has_exercise_item_users_has_content1_idx` (`users_has_content_id`);

--
-- Indexes for table `users_secure`
--
ALTER TABLE `users_secure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_secure_users1_idx` (`users_id`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_activity_users1_idx` (`users_id`);

--
-- Indexes for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_tokens_users1_idx` (`users_id`);

--
-- Indexes for table `week_info`
--
ALTER TABLE `week_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_week_info_users1_idx` (`users_id`);

--
-- Indexes for table `week_sessions`
--
ALTER TABLE `week_sessions`
  ADD PRIMARY KEY (`id`,`week_info_id`),
  ADD KEY `fk_week_sessions_week_info1_idx` (`week_info_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_details`
--
ALTER TABLE `content_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_has_resources`
--
ALTER TABLE `content_has_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exercise_item`
--
ALTER TABLE `exercise_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exercise_item_details`
--
ALTER TABLE `exercise_item_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users_has_content`
--
ALTER TABLE `users_has_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_secure`
--
ALTER TABLE `users_secure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `week_info`
--
ALTER TABLE `week_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `week_sessions`
--
ALTER TABLE `week_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `fk_content_content1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `content_details`
--
ALTER TABLE `content_details`
  ADD CONSTRAINT `fk_content_details_content1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_content_details_content2` FOREIGN KEY (`topic_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_content_details_files1` FOREIGN KEY (`image_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_content_details_resources1` FOREIGN KEY (`resources_id`) REFERENCES `resources` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `content_has_resources`
--
ALTER TABLE `content_has_resources`
  ADD CONSTRAINT `fk_content_has_resources_content1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_content_has_resources_resources1` FOREIGN KEY (`resources_id`) REFERENCES `resources` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `exercises`
--
ALTER TABLE `exercises`
  ADD CONSTRAINT `fk_exercises_content1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_exercises_files1` FOREIGN KEY (`worksheet_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `exercise_item`
--
ALTER TABLE `exercise_item`
  ADD CONSTRAINT `fk_exercise_item_exercises1` FOREIGN KEY (`exercises_id`) REFERENCES `exercises` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `exercise_item_details`
--
ALTER TABLE `exercise_item_details`
  ADD CONSTRAINT `fk_exercise_item_details_exercise_item1` FOREIGN KEY (`exercise_item_id`) REFERENCES `exercise_item` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_users_has_content_content1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_has_content_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `fk_resources_files1` FOREIGN KEY (`files_id`) REFERENCES `files` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_users1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `users_has_content`
--
ALTER TABLE `users_has_content`
  ADD CONSTRAINT `fk_users_has_content_content2` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_has_content_resources1` FOREIGN KEY (`resources_id`) REFERENCES `resources` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_has_content_users2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `users_has_content_has_exercise_item`
--
ALTER TABLE `users_has_content_has_exercise_item`
  ADD CONSTRAINT `fk_users_has_content_has_exercise_item_exercise_item1` FOREIGN KEY (`exercise_item_id`) REFERENCES `exercise_item` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_users_has_content_has_exercise_item_users_has_content1` FOREIGN KEY (`users_has_content_id`) REFERENCES `users_has_content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `users_secure`
--
ALTER TABLE `users_secure`
  ADD CONSTRAINT `fk_users_secure_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD CONSTRAINT `fk_user_activity_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `fk_user_tokens_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `week_info`
--
ALTER TABLE `week_info`
  ADD CONSTRAINT `fk_week_info_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `week_sessions`
--
ALTER TABLE `week_sessions`
  ADD CONSTRAINT `fk_week_sessions_week_info1` FOREIGN KEY (`week_info_id`) REFERENCES `week_info` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

