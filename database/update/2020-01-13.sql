alter table lottery.partner_notice add  no_popup TEXT DEFAULT NULL AFTER status;
alter table lottery.partner_advertisings add pid int(10) unsigned;
alter table lottery.partner_advertisings add game_id varchar(64);
alter table lottery.templates add partner_name varchar(64);
alter table lottery.templates add module_name varchar(256);