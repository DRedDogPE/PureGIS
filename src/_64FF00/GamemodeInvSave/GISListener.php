<?php

namespace _64FF00\GamemodeInvSave;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerGameModeChangeEvent;

class GISListener implements Listener
{    
    /* GamemodeInvSave by 64FF00 (xktiverz@gmail.com, @64ff00 for Twitter) */

    /*
          # #    #####  #       ####### #######   ###     ###   
          # #   #     # #    #  #       #        #   #   #   #  
        ####### #       #    #  #       #       #     # #     # 
          # #   ######  #    #  #####   #####   #     # #     # 
        ####### #     # ####### #       #       #     # #     # 
          # #   #     #      #  #       #        #   #   #   #  
          # #    #####       #  #       #         ###     ###                                        
                                                                                       
    */

    /**
     * @param GamemodeInvSave $plugin
     */
    public function __construct(GamemodeInvSave $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerGameModeChangeEvent $event
     */
    public function onGameModeChange(PlayerGameModeChangeEvent $event)
    {
        $player = $event->getPlayer();

        $newGamemode = $event->getNewGamemode();

        switch($newGamemode)
        {
            case 0:

                $this->plugin->getServer()->getScheduler()->scheduleDelayedTask(new GISPluginTask($this->plugin, "loadArmorContents", array($player)), 15);
                $this->plugin->getServer()->getScheduler()->scheduleDelayedTask(new GISPluginTask($this->plugin, "loadContents", array($player)), 15);

                break;

            case 1:

                $this->plugin->saveArmorContents($player);
                $this->plugin->saveContents($player);

                break;

            default:

                break;
        }
    }
}