#
#<?php die('Forbidden.'); ?>
#Date: 2020-02-17 09:36:28 UTC
#Software: Joomla Platform 13.1.0 Stable [ Curiosity ] 24-Apr-2013 00:00 GMT

#Fields: date	time	line_nr	action	comment
2020-02-17	09:36:28	0	[DEBUG]	Clean up old logs. Found 25 logs and threshold is 25 logs
2020-02-17	09:36:28	0	[DEBUG]	Clean up old logs. Found 25 logs and threshold is 25 logs
2020-02-17	09:36:28	0	[DEBUG]	CSVI Pro Version: 7.9.0
2020-02-17	09:36:28	0	[DEBUG]	Joomla! version: 3.8.12
2020-02-17	09:36:28	0	[DEBUG]	PHP version: 5.6.38
2020-02-17	09:36:28	0	[DEBUG]	=========================
2020-02-17	09:36:28	0	[DEBUG]	action: export
2020-02-17	09:36:28	0	[DEBUG]	component: com_modules
2020-02-17	09:36:28	0	[DEBUG]	operation: modules
2020-02-17	09:36:28	0	[DEBUG]	override: 
2020-02-17	09:36:28	0	[DEBUG]	0: todownload
2020-02-17	09:36:28	0	[DEBUG]	output_empty_file: Да
2020-02-17	09:36:28	0	[DEBUG]	localpath: D:\OPS\OSPanel\domains\bat6
2020-02-17	09:36:28	0	[DEBUG]	ftphost: 
2020-02-17	09:36:28	0	[DEBUG]	ftpport: 
2020-02-17	09:36:28	0	[DEBUG]	sftp: Нет
2020-02-17	09:36:28	0	[DEBUG]	ftproot: 
2020-02-17	09:36:28	0	[DEBUG]	ftpfile: 
2020-02-17	09:36:28	0	[DEBUG]	export_email_subject: 
2020-02-17	09:36:28	0	[DEBUG]	export_email_body: 
2020-02-17	09:36:28	0	[DEBUG]	database_username: 
2020-02-17	09:36:28	0	[DEBUG]	database_host: 
2020-02-17	09:36:28	0	[DEBUG]	database_portno: 
2020-02-17	09:36:28	0	[DEBUG]	database_name: 
2020-02-17	09:36:28	0	[DEBUG]	database_table: 
2020-02-17	09:36:28	0	[DEBUG]	primary_key_source: module
2020-02-17	09:36:28	0	[DEBUG]	primary_key_target: id
2020-02-17	09:36:28	0	[DEBUG]	create_new_columns: Да
2020-02-17	09:36:28	0	[DEBUG]	export_filename: mod_warranty.xml
2020-02-17	09:36:28	0	[DEBUG]	export_file: xml
2020-02-17	09:36:28	0	[DEBUG]	field_delimiter: ,
2020-02-17	09:36:28	0	[DEBUG]	text_enclosure: 
2020-02-17	09:36:28	0	[DEBUG]	include_column_headers: Да
2020-02-17	09:36:28	0	[DEBUG]	signature: Нет
2020-02-17	09:36:28	0	[DEBUG]	publish_state: Да
2020-02-17	09:36:28	0	[DEBUG]	recordstart: 
2020-02-17	09:36:28	0	[DEBUG]	recordend: 
2020-02-17	09:36:28	0	[DEBUG]	export_date_format: d/m/Y H:i:s
2020-02-17	09:36:28	0	[DEBUG]	header: 
2020-02-17	09:36:28	0	[DEBUG]	body: 
2020-02-17	09:36:28	0	[DEBUG]	footer: 
2020-02-17	09:36:28	0	[DEBUG]	content_language: *
2020-02-17	09:36:28	0	[DEBUG]	groupbyfields: 
2020-02-17	09:36:28	0	[DEBUG]	sortfields: 
2020-02-17	09:36:28	0	[DEBUG]	use_system_limits: Нет
2020-02-17	09:36:28	0	[DEBUG]	max_execution_time: 
2020-02-17	09:36:28	0	[DEBUG]	memory_limit: 
2020-02-17	09:36:28	0	[DEBUG]	csvi_template_id: 74
2020-02-17	09:36:28	0	[DEBUG]	tags: 
2020-02-17	09:36:28	0	[DEBUG]	=========================
2020-02-17	09:36:28	0	[DEBUG]	Export field: title
2020-02-17	09:36:28	0	[DEBUG]	Export field: module
2020-02-17	09:36:28	0	[DEBUG]	Export field: params
2020-02-17	09:36:28	0	[DEBUG]	Export query
SELECT `modules`.`id`,
`modules`.`title`,
`modules`.`module`,
`modules`.`params`
FROM `#__modules` AS `modules`
LEFT JOIN `#__modules_menu` AS `modules_menu` ON `modules`.`id` = `modules_menu`.`menuid`
WHERE `modules`.`published` = 1
