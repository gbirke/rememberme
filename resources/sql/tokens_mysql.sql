CREATE TABLE IF NOT EXISTS `tokens` (
  `credential` varchar(128) NOT NULL DEFAULT '',
  `token` char(40) NOT NULL DEFAULT '',
  `persistent_token` char(40) NOT NULL DEFAULT '',
  `expires` datetime NOT NULL,
  KEY `credential` (`credential`,`persistent_token`,`expires`)
);