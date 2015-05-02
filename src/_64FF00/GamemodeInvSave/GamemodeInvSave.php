<?php

namespace _64FF00\GamemodeInvSave;

use pocketmine\item\Item;

use pocketmine\plugin\PluginBase;

use pocketmine\Player;

use pocketmine\utils\Config;

class GamemodeInvSave extends PluginBase
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

    public function onEnable()
    {
        @mkdir($this->getDataFolder() . "players/", 0777, true);
        
        $this->getServer()->getPluginManager()->registerEvents(new GISListener($this), $this);
    }
    
    public function onDisable()
    {
    }
    
    public function configExists(Player $player)
    {
        return file_exists($this->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml");
    }
    
    public function getPlayerConfig(Player $player)
    {
        if(!(file_exists($this->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml")))
        {
            return new Config($this->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
                "userName" => $player->getName(),
                "armor" => array(
                ),
                "items" => array(
                )
            ));
        }
        
        return new Config($this->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml", Config::YAML, array(
        ));
    }
    
    public function isHelmet(Item $item)
    {
        $id = $item->getId();
        
        return (($id == 298) or ($id == 302) or ($id == 306) or ($id == 310) or ($id == 314));
    }
    
    public function isChestplate(Item $item)
    {
        $id = $item->getId();
        
        return (($id == 299) or ($id == 303) or ($id == 307) or ($id == 311) or ($id == 315));
    }
    
    public function isLeggings(Item $item)
    {
        $id = $item->getId();
        
        return (($id == 300) or ($id == 304) or ($id == 308) or ($id == 312) or ($id == 316));
    }
    
    public function isBoots(Item $item)
    {
        $id = $item->getId();
        
        return (($id == 301) or ($id == 305) or ($id == 309) or ($id == 313) or ($id == 317));
    }
}