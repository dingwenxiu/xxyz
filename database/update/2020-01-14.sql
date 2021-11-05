ALTER TABLE lottery.partner_notice ADD COLUMN type_desc TEXT DEFAULT NULL AFTER title;
INSERT INTO lottery.sys_telegram_channel (partner_sign,channel_sign,channel_group_name,channel_id,status) VALUES ('system','send_admin_behavior','总控_管理员行为','-372728334',1);
alter table lottery.partner_advertisings add type_name varchar(256) after id;
alter table lottery.partner_advertisings add module_name varchar(256) after id;
alter table lottery.partner_advertisings add sign_name varchar(256) after id;