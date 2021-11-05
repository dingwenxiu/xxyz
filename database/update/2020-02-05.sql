// 审核表 增加 审核内容集结展示
ALTER TABLE lottery.lottery_issue_bet ADD COLUMN total_bet_commission BIGINT(20) NOT NULL DEFAULT '0' AFTER total_cancel;
ALTER TABLE lottery.lottery_issue_bet ADD COLUMN total_child_commission BIGINT(20) NOT NULL DEFAULT '0' AFTER total_bet_commission;