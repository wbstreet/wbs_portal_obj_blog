DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_wbs_portal_obj_blog_publication`;
CREATE TABLE `{TABLE_PREFIX}mod_wbs_portal_obj_blog_publication` (
  `obj_id` int(11) NOT NULL,
  `image_id` int(11),
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `text` text NOT NULL,
   PRIMARY KEY (`obj_id`)
){TABLE_ENGINE=MyISAM};