// 审核表 增加 审核内容集结展示
ALTER TABLE lottery.admin_action_review ADD COLUMN process_config VARCHAR(256) NOT NULL  AFTER process_desc;
