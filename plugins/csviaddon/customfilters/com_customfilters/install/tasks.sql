DELETE FROM `#__csvi_availabletables` WHERE `component` = 'com_customfilters';
INSERT IGNORE INTO `#__csvi_availabletables` (`task_name`, `template_table`, `component`, `action`, `enabled`) VALUES
('customfield', 'customfield', 'com_customfilters', 'export', '0'),
('customfield', 'cf_customfields', 'com_customfilters', 'export', '1'),
('customfield', 'customfield', 'com_customfilters', 'import', '0'),
('customfield', 'cf_customfields', 'com_customfilters', 'import', '1');

DELETE FROM `#__csvi_tasks` WHERE `component` = 'com_customfilters';
INSERT IGNORE INTO `#__csvi_tasks` (`task_name`, `action`, `component`, `url`, `options`) VALUES
('customfield', 'export', 'com_customfilters', 'index.php?option=com_customfilters&view=customfilters', 'source,file,layout,fields,limit.advancedUser'),
('customfield', 'import', 'com_customfilters', 'index.php?option=com_customfilters&view=customfilters', 'source,file,fields,limit.advancedUser');