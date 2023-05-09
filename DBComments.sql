CREATE TABLE `Comms` (
  `id` int(10),
  `title` varchar(255) default '',
  `description` text NOT NULL,
  `data` int(11) NOT NULL default '0',
  `comments_count` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
)
