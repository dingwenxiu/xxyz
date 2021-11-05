<?php

use App\Models\Admin\AdminUser;
use App\Models\Player\Player;
use Illuminate\Database\Seeder;

class PlayerGenTableSeeder extends Seeder
{
    // 生成
    public function run()
    {
        // 加测试资金
        $adminUser = AdminUser::find(1);

        // 添加test总代
        $top = Player::addTop("YX", "test1980", "1234qwer", 'qwer1234', 1980, 1);
        if (!is_object($top)) {
            echo $top;
            return true;
        }

        $res = $top->manualTransfer('add', 1, 20000, '测试用户初始化', $adminUser);
        if ($res !== true) {
            echo $res;
            return true;
        }

        // 生成下级
        for($i = 1960; $i > 1800; $i-- ) {
            // 添加总代
            $parent = $top->addChild("test" . $i, "1234qwer", Player::PLAYER_TYPE_PROXY, $i, 1);
            if (!is_object($parent)) {
                info($parent);
                return true;
            }

            $res = $parent->manualTransfer('add', 1, 20000, '测试用户初始化', $adminUser);

            if ($res !== true) {
                echo $res;
                return true;
            }

            $level = random_int(2, 3);

            for($k = 2; $k < $level + 1; $k++ ) {
                $prizeGroup = $i - $k *2;
                $prizeGroup = $prizeGroup < 1800 ? 1800 : $prizeGroup;

                $_parent = $parent->addChild("test" . $i . $k, "1234qwer", Player::PLAYER_TYPE_PROXY, $prizeGroup, 1);
                if (!is_object($_parent)) {
                    info("-----user-init---" . $_parent);
                    return true;
                }

                $_parent->manualTransfer('add', 1, 20000, '测试用户初始化', $adminUser);

                $parent = $_parent;
            }
        }
    }
}
