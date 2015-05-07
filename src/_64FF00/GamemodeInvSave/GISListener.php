<?php

namespace _64FF00\GamemodeInvSave;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerGameModeChangeEvent;

use pocketmine\item\Item;

use pocketmine\Player;

use pocketmine\utils\Config;

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
        
        $config = $this->plugin->getPlayerConfig($player);
        
        $newGamemode = $event->getNewGamemode();
        
        switch($newGamemode)
        {
            case 0:
                
                if($this->plugin->configExists($player))
                {
                    $player->getInventory()->clearAll();
                    
                    $armorList = $config->getNested("armor");
                    $itemsList = $config->getNested("items");
                    
                    if(!empty($armorList))
                    {
                        foreach($armorList as $armorType => $id)
                        {
                            $item = Item::get($id, 0, 1);
                                    
                            if($this->plugin->isHelmet(clone $item)) $player->getInventory()->setHelmet(clone $item);
                            if($this->plugin->isChestplate(clone $item)) $player->getInventory()->setChestplate(clone $item);
                            if($this->plugin->isLeggings(clone $item)) $player->getInventory()->setLeggings(clone $item);
                            if($this->plugin->isBoots(clone $item)) $player->getInventory()->setBoots(clone $item);
                            
                            $player->getInventory()->sendArmorContents($player);
                        }
                        
                        $config->setNested("armor", []);
                    }
                    
                    if(!empty($itemsList))
                    {
                        foreach($itemsList as $slot => $itemInfo)
                        {      
                            $tmp = explode(":", $itemInfo);
                            
                            $id = (int) $tmp[0];
                            $damage = (int) $tmp[1];
                            $count = (int) $tmp[2];

                            $item = Item::get($id, $damage, $count);
                            
                            // ...
                            $player->getInventory()->addItem(clone $item);
                        }
                        
                        $config->setNested("items", []);
                    }
                    
                     $config->save();
                }
                
                break;
                
            case 1:
                
                $armor = [];
                $items = [];
                
                $armor["helmet"] = $player->getInventory()->getHelmet()->getId();
                $armor["chestplate"] = $player->getInventory()->getChestplate()->getId();
                $armor["leggings"] = $player->getInventory()->getLeggings()->getId();
                $armor["boots"] = $player->getInventory()->getBoots()->getId();
                
                foreach($player->getInventory()->getContents() as $slot => $item)
                {
                    $id = $item->getId();
                    $damage = $item->getDamage();
                    $count = $item->getCount();
                    
                    if(!isset($items[$slot])) $items[$slot] = "$id:$damage:0";
                    
                    if($slot > $player->getInventory()->getSize())
                    {
                        if($id == $armor["helmet"] or $id == $armor["chestplate"] or $id == $armor["leggings"] or $id == $armor["boots"])
                        {
                            --$count;
                        }
                    }                    
                    
                    $items[$slot] = "$id:$damage:$count";
                }
                
                $config->setNested("armor", $armor);
                $config->setNested("items", $items);
			
                $config->save();
                
                break;
        }
    }
}