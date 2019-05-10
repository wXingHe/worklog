DELETE FROM 'zt_grouppriv' WHERE 'module' = 'worklog' and 'group' = 1;
INSERT INTO `zt_grouppriv` (`group`, `module`, `method`) VALUES
(1, 'worklog', 'add'),
(1, 'worklog', 'browse'),
(1, 'worklog', 'edit'),
(1, 'worklog', 'index'),
(1, 'worklog', 'export');



