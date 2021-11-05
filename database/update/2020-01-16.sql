DELETE FROM lottery.admin_menus WHERE title='商户审核权限列表';
DELETE FROM lottery.admin_menus WHERE title='添加权限';
DELETE FROM lottery.admin_menus WHERE title='修改权限';
DELETE FROM lottery.admin_menus WHERE title='删除权限';
//新增部分
INSERT INTO lottery.admin_menus (id, pid, rid, title, route, api_path, sort, type, level, status, admin_id) VALUES ('10400', '10000', '10000|10400', '审核管理', 'system/reviewList', 'system/review-list', '9', '0', '1', '1', '0');