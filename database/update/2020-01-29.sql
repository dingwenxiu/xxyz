INSERT INTO lottery.sys_configures (id, pid,name, sign, value, partner_edit, partner_show, status) VALUES ('6017', '6', '测试用户是否记入统计','system_tester_stat', '0', '1', '1', '1');
INSERT INTO lottery.partner_configures (id, partner_sign,pid,name, sign, value, can_edit, can_show, status) VALUES ('1006017', 'YX','1006000', '测试用户是否记入统计','system_tester_stat', '0', '1', '1', '1');
INSERT INTO lottery.partner_configures (id, partner_sign,pid,name, sign, value, can_edit, can_show, status) VALUES ('2006017', 'KLC','2006000', '测试用户是否记入统计','system_tester_stat', '0', '1', '1', '1');

//添加是否測試用戶字段
alter table lottery.report_stat_stack add is_tester tinyint(4) default 0 after user_id;
alter table lottery.report_user_dividend add is_tester tinyint(4) default 0 after user_id;
alter table lottery.report_user_salary add is_tester tinyint(4) default 0 after user_id;