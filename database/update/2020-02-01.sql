// 审核表 增加审核原因字段
ALTER TABLE lottery.admin_action_review ADD COLUMN request_desc VARCHAR(128) NOT NULL  AFTER type;
