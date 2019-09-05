/* Custom Filters custom fields import */
INSERT IGNORE INTO `#__csvi_availablefields` (`csvi_name`, `component_name`, `component_table`, `component`, `action`) VALUES
('skip', 'skip', 'customfield', 'com_customfilters', 'import'),
('combine', 'combine', 'customfield', 'com_customfilters', 'import'),
('custom_title', 'custom_title', 'customfield', 'com_customfilters', 'import'),
('display_type', 'display_type', 'customfield', 'com_customfilters', 'import'),
('smart_search', 'smart_search', 'customfield', 'com_customfilters', 'import'),
('expanded', 'expanded', 'customfield', 'com_customfilters', 'import'),
('scrollbar_after', 'scrollbar_after', 'customfield', 'com_customfilters', 'import'),

/* Custom Filters custom fields export */
('custom', 'custom', 'customfield', 'com_customfilters', 'export'),
('custom_title', 'custom_title', 'customfield', 'com_customfilters', 'export'),
('display_type', 'display_type', 'customfield', 'com_customfilters', 'export'),
('smart_search', 'smart_search', 'customfield', 'com_customfilters', 'export'),
('expanded', 'expanded', 'customfield', 'com_customfilters', 'export'),
('scrollbar_after', 'scrollbar_after', 'customfield', 'com_customfilters', 'export');