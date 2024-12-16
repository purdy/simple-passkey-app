CREATE TABLE `user` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  `modified_at` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `passkey` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `unique_id` varchar(16) NOT NULL DEFAULT '',
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `credential_id` varchar(100) NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  `modified_at` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
);

