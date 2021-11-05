<?php
namespace App\Observers;

use App\Lib\Logic\Cache\PlayerCache;
use App\Models\Player\Player;

class PlayerObserver
{
    /**
     * 添加缓存
     * @param Player $player
     * @throws \Exception
     */
    public function created(Player $player)
    {
        PlayerCache::update($player);
    }

    /**
     * 添加缓存
     * @param Player $player
     * @throws \Exception
     */
    public function updated(Player $player)
    {
        PlayerCache::update($player);
    }

    /**
     * Handle the player "deleted" event.
     *
     * @param  \App\Models\Player\Player  $player
     * @return void
     */
    public function deleted(Player $player)
    {
        //
    }

    /**
     * Handle the player "restored" event.
     *
     * @param  \App\Models\Player\Player  $player
     * @return void
     */
    public function restored(Player $player)
    {
        //
    }

    /**
     * Handle the player "force deleted" event.
     *
     * @param  \App\Models\Player\Player  $player
     * @return void
     */
    public function forceDeleted(Player $player)
    {

    }

    /**
     * 修改用户的相关参数
     * @param Player $player
     */
    public function retrieved(Player $player) {

    }
}
