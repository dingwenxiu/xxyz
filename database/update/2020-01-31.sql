// partner表增加控水字段
ALTER TABLE lottery.partners ADD COLUMN rate_open tinyint(4) NOT NULL DEFAULT '0' AFTER status;
// 商户彩票控水字段
ALTER TABLE lottery.partner_lottery ADD COLUMN rate_open tinyint(4) NOT NULL DEFAULT '0' AFTER status;
