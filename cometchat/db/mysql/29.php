<?php

if(!isset($errors)){
   $errors='';
}

$content = <<<EOD
ALTER TABLE `cometchat`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_announcements`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_block`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_chatroommessages`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_chatrooms`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_chatrooms_users`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_colors`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_guests`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_status`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_users`  ADD `custom_data` TEXT NULL DEFAULT NULL;
ALTER TABLE `cometchat_videochatsessions`  ADD `custom_data` TEXT NULL DEFAULT NULL;

ALTER TABLE `cometchat` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `cometchat_guests` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `cometchat_chatroommessages` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE table cometchat_archive LIKE cometchat;
CREATE table cometchat_guests_archive LIKE cometchat_guests;
CREATE table cometchat_chatroommessages_archive LIKE cometchat_chatroommessages;
CREATE table cometchat_chatrooms_archive LIKE cometchat_chatrooms;
CREATE table cometchat_chatrooms_users_archive LIKE cometchat_chatrooms_users;

ALTER TABLE `cometchat_archive` ENGINE = MYISAM;
ALTER TABLE `cometchat_guests_archive` ENGINE = MYISAM;
ALTER TABLE `cometchat_chatroommessages_archive` ENGINE = MYISAM;
ALTER TABLE `cometchat_chatrooms_archive` ENGINE = MYISAM;
ALTER TABLE `cometchat_chatrooms_users_archive` ENGINE = MYISAM;


CREATE TABLE `cometchat_report` (
	`id` bigint(20) NOT NULL,
	`timestamp_start` int(10) NOT NULL,
	`total_no_of_users` bigint(20) NOT NULL,
	`total_no_of_guest` bigint(20) NOT NULL,
	`no_of_active_users_last_24_hrs` int(10) NOT NULL,
	`no_of_active_guest_last_24_hrs` int(10) NOT NULL,
	`no_of_messages_exchange_one_on_one_last_24_hrs` bigint(20) NOT NULL,
	`no_of_messages_exchange_groupchat_last_24_hrs` bigint(20) NOT NULL,
	`no_of_group_created_last_24_hrs` int(10) NOT NULL,
	`active_users_from_last_15_min` int(10) NOT NULL,
	`active_guest_from_last_15_min` int(10) NOT NULL,
	`data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cometchat_report`
	ADD PRIMARY KEY (`id`);

ALTER TABLE `cometchat_report`
	MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

REPLACE INTO `cometchat_settings` (setting_key,value,key_type) values ('dbversion','29','1');

EOD;
$q = preg_split('/;[\r\n]+/',$content);
foreach ($q as $query) {
  if (strlen($query) > 4) {
    if (!sql_query($query, array(), 1)) {
      $errors .= sql_error($dbh)."<br/>\n";
    }
  }
}
removeCachedSettings($client.'settings');
