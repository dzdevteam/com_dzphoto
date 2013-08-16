CREATE TABLE IF NOT EXISTS `#__dzphoto_images` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`caption` TEXT NOT NULL ,
`links` TEXT  NOT NULL ,
`params` TEXT NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__dzphoto_relations` (
`catid` INT(11)  NOT NULL ,
`imageid` INT(11)  NOT NULL ,
PRIMARY KEY (`catid`, `imageid`)
) DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__content_types` (`type_id`, `type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`) VALUES (NULL, 'DZ Photo Image', 'com_dzphoto.image', '{"special":{"dbtable":"#__dzphoto_images","key":"id","type":"Image","prefix":"DZPhotoTable","config":"array()"}}', '', '{"common":[{"core_content_item_id":"id","core_title":"title","core_state":"state","core_alias":"alias","core_body":"caption", "core_params":"params", "core_ordering":"ordering", "asset_id":"asset_id"}]}', 'DZPhotoHelperRoute::getImageRoute');