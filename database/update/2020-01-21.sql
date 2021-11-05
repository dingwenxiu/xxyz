alter table lottery.report_stat_stack add is_has int(11) default 0 after is_first;

CREATE TABLE report_stat_casino_days (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  partner_sign varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  main_game_plat_code varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  day int(10) NOT NULL DEFAULT '0',
  month int(10) NOT NULL DEFAULT '0',
  bet_amount decimal(13,3) NOT NULL DEFAULT '0.000',
  company_win_amount decimal(13,3) NOT NULL DEFAULT '0.000',
  company_payout_amount decimal(13,3) NOT NULL DEFAULT '0.000',
  casino_transfer_out decimal(13,3) NOT NULL DEFAULT '0.000',
  casino_transfer_in decimal(13,3) NOT NULL DEFAULT '0.000',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY report_stat_casino_days_partner_sign_index (partner_sign),
  KEY report_stat_casino_days_plat_code_index (main_game_plat_code),
  KEY report_stat_casino_days_month_index (month),
  KEY report_stat_casino_days_day_index (day)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;