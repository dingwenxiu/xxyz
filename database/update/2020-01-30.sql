// 删除弃用字段
ALTER TABLE lottery.admin_action_review DROP process_check;

// 修改部分字段
ALTER TABLE `lottery`.`admin_action_review` CHANGE COLUMN  `process_config` `value` VARCHAR(128) NOT NULL DEFAULT '' ;
ALTER TABLE `lottery`.`admin_action_review` CHANGE COLUMN `partner_id` `partner_admin_id` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `lottery`.`admin_action_review` CHANGE COLUMN `partner_username` `partner_admin_name` VARCHAR(64) NOT NULL DEFAULT '' ;

// 新增字段
//配置id
ALTER TABLE lottery.admin_action_review ADD COLUMN config_id INT(11) NOT NULL DEFAULT '0' AFTER id;
//配置pid
ALTER TABLE lottery.admin_action_review ADD COLUMN config_pid INT(11) NOT NULL DEFAULT '0' AFTER config_id;
//配置名
ALTER TABLE lottery.admin_action_review ADD COLUMN config_name VARCHAR(64) NOT NULL DEFAULT '' AFTER config_pid;
//配置标示
ALTER TABLE lottery.admin_action_review ADD COLUMN config_sign VARCHAR(64) NOT NULL DEFAULT '' AFTER config_name;
//配置值
ALTER TABLE lottery.admin_action_review ADD COLUMN config_value VARCHAR(128) NOT NULL DEFAULT '' AFTER config_sign;
//配置描述
ALTER TABLE lottery.admin_action_review ADD COLUMN config_description VARCHAR(128)  DEFAULT  ''  AFTER config_value;
//配置商户可否展示
ALTER TABLE lottery.admin_action_review ADD COLUMN config_partner_show TINYINT(4) NOT NULL DEFAULT '0' AFTER config_description;
// 是否可编辑
ALTER TABLE lottery.admin_action_review ADD COLUMN config_partner_edit TINYINT(4) NOT NULL DEFAULT '0' AFTER config_partner_show;
//添加或者修改配置
ALTER TABLE lottery.admin_action_review ADD COLUMN config_is_edit_pid TINYINT(4) NOT NULL DEFAULT '0' AFTER config_partner_edit;
//商户标示
ALTER TABLE lottery.admin_action_review ADD COLUMN config_partner_sign VARCHAR(64) NOT NULL DEFAULT '' AFTER config_is_edit_pid;