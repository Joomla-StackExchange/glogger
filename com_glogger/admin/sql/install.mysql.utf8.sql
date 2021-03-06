CREATE TABLE `#__glogger` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL COMMENT 'Title to identify the session',
  `logtime` datetime NOT NULL COMMENT 'Creation date/time of gLogger Object',
  `created_by` int(11) DEFAULT NULL COMMENT 'User ID',
  `remote_addr` varchar(39) DEFAULT NULL COMMENT 'Remote Address ip4/vp6',
  `identifier` char(32) NOT NULL COMMENT 'Joomla SessionID or Unique across gLog Saves',
  `source` varchar(50) DEFAULT NULL COMMENT 'Where the logging was done from, ie.e Cron, API, User...',
  `table_name` varchar(50) DEFAULT NULL COMMENT 'If the logger Table-if-interest was set',
  `table_id` int(11) DEFAULT NULL COMMENT 'Record ID for Table-of-interest',
  `textlog` mediumtext NOT NULL COMMENT 'The extracted Text of the Log',
  `data` longtext NOT NULL COMMENT 'Raw data collected during logging session',
  `flagged_by` int(11) DEFAULT '0' COMMENT 'Flagged-by userid to prevent purging',
  `ref_num` varchar(50) DEFAULT NULL COMMENT 'Reference Number',
  `logs_count` int(11) DEFAULT '0' COMMENT 'How many Log Entries',
  `data_count` int(11) DEFAULT '0' COMMENT 'How many Data Entires',
  PRIMARY KEY (`id`),
  KEY `idxIdentifier` (`identifier`),
  KEY `idxTableRow` (`table_name`,`table_id`),
  KEY `idxRefnum` (`ref_num`),
  KEY `idxSource` (`source`)
) CHARACTER SET utf8;

CREATE TABLE `#__glogger_audit` (
  `id` int(11) unsigned NOT NULL,
  `title` varchar(250) NOT NULL COMMENT 'Title to identify the session',
  `logtime` datetime NOT NULL COMMENT 'Creation date/time of gLogger Object',
  `created_by` int(11) DEFAULT NULL COMMENT 'User ID',
  `remote_addr` varchar(39) DEFAULT NULL COMMENT 'Remote Address ip4/vp6',
  `identifier` char(32) NOT NULL COMMENT 'Joomla SessionID or Unique across gLog Saves',
  `source` varchar(50) DEFAULT NULL COMMENT 'Where the logging was done from, ie.e Cron, API, User...',
  `table_name` varchar(50) DEFAULT NULL COMMENT 'If the logger Table-if-interest was set',
  `table_id` int(11) DEFAULT NULL COMMENT 'Record ID for Table-of-interest',
  `textlog` mediumtext NOT NULL COMMENT 'The extracted Text of the Log',
  `data` longtext NOT NULL COMMENT 'Raw data collected during logging session',
  `flagged_by` int(11) DEFAULT '0' COMMENT 'Flagged-by userid to prevent purging',
  `ref_num` varchar(50) DEFAULT NULL COMMENT 'Reference Number',
  `logs_count` int(11) DEFAULT '0' COMMENT 'How many Log Entries',
  `data_count` int(11) DEFAULT '0' COMMENT 'How many Data Entires',
  PRIMARY KEY (`id`),
  KEY `idxIdentifier` (`identifier`),
  KEY `idxTableRow` (`table_name`,`table_id`),
  KEY `idxRefnum` (`ref_num`),
  KEY `idxSource` (`source`)
) CHARACTER SET utf8;

