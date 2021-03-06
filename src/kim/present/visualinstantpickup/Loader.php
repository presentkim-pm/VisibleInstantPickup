<?php

/**
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection SpellCheckingInspection
 * @noinspection PhpDocSignatureInspection
 */

declare(strict_types=1);

namespace kim\present\visualinstantpickup;

use kim\present\visualinstantpickup\task\PickupTask;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

use function count;
use function is_dir;
use function rmdir;
use function scandir;

final class Loader extends PluginBase implements Listener{
    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        /**
         * This is a plugin that does not use data folders.
         * Delete the unnecessary data folder of this plugin for users.
         */
        $dataFolder = $this->getDataFolder();
        if(is_dir($dataFolder) && count(scandir($dataFolder)) <= 2){
            rmdir($dataFolder);
        }
    }

    /** @priority HIGHEST */
    public function onBlockBreakEvent(BlockBreakEvent $event) : void{
        $player = $event->getPlayer();
        if(!$player->hasFiniteResources())
            return;

        $blockPos = $event->getBlock()->getPos();
        foreach($event->getDrops() as $drop){
            $this->getScheduler()->scheduleDelayedTask(new PickupTask($player, $drop, $blockPos), 10);
        }
        $event->setDrops([]);
    }
}