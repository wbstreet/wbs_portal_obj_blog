DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_wbs_portal_obj_blog_publication`;
CREATE TABLE `{TABLE_PREFIX}mod_wbs_portal_obj_blog_publication` (
  `obj_id` int(11) NOT NULL,
  `image_storage_id` int(11),
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `text` text NOT NULL,
   PRIMARY KEY (`obj_id`)
){TABLE_ENGINE=MyISAM};

DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_wbs_portal_obj_blog_hashtag`;
CREATE TABLE `{TABLE_PREFIX}mod_wbs_portal_obj_blog_hashtag` (
  `hashtag_id` int(11) NOT NULL AUTO_INCREMENT,
  `hashtag_name` varchar(255) NOT NULL,
  `is_deleted` int(11) NOT NULL,
   PRIMARY KEY (`hashtag_id`)
){TABLE_ENGINE=MyISAM};

DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_wbs_portal_obj_blog_hashtag_publication`;
CREATE TABLE `{TABLE_PREFIX}mod_wbs_portal_obj_blog_hashtag_publication` (
  `hashtag_publication_id` int(11) NOT NULL AUTO_INCREMENT,
  `obj_id` int(11) NOT NULL,
  `hashtag_id` int(11) NOT NULL,
  `is_deleted` int(11) NOT NULL,
   PRIMARY KEY (`hashtag_publication_id`)
){TABLE_ENGINE=MyISAM};