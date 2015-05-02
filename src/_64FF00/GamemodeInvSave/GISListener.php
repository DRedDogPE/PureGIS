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

    public function __construct(GamemodeInvSave $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function onGameModeChange(PlayerGameModeChangeEvent $event)
    {
        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        $config = $this->plugin->getPlayerConfig($player);
        
        $newGamemode = $event->getNewGamemode();
        
        switch($newGamemode)
        {
            case 0:
                
                if($this->plugin->configExists($player))
                {
                    $armorList = $config->getNested("armor");
                    $itemsList = $config->getNested("items");
                    
                    if(!empty($armorList))
                    {
                        foreach($armorList as $armorType => $id)
                        {
                            $item = Item::get($id, 0, 1);
                                    
                            if($this->plugin->isHelmet(clone $item))
                            {
                                $inventory->setHelmet(clone $item);
                            }
                            
                            if($this->plugin->isChestplate(clone $item))
                            {
                                $inventory->setChestplate(clone $item);
                            }
                            
                            if($this->plugin->isLeggings(clone $item))
                            {
                                $inventory->setLeggings(clone $item);
                            }
                            
                            if($this->plugin->isBoots(clone $item))
                            {
                                $inventory->setBoots(clone $item);
                            }
                            
                            $inventory->sendArmorContents($player);
                        }
                        
                        $config->setNested("armor", []);
                    }
                    
                    if(!empty($itemsList))
                    {
                        foreach($itemsList as $slot => $itemInfo)
                        {   
                            $tmp = explode(",", $itemInfo);
                            
                            $id = (int) $tmp[0];
                            $damage = (int) $tmp[1];
                            $count = (int) $tmp[2];
                            
                            $item = Item::get($id, $damage, $count);
                            
                            $player->getInventory()->setItem($slot, $item);
                        }
                        
                        $config->setNested("items", []);
                    }
                    
                     $config->save();
                }
                
                break;
                
            case 1:
                
                $armor = [];
                $items = [];
                
                $armor["helmet"] = $inventory->getHelmet()->getId();
                $armor["chestplate"] = $inventory->getChestplate()->getId();
                $armor["leggings"] = $inventory->getLeggings()->getId();
                $armor["boots"] = $inventory->getBoots()->getId();
                
                foreach($inventory->getContents() as $slot => $item)
                {
                    $id = $item->getId();
                    $damage = $item->getDamage();
                    $count = $item->getCount();
                    
                    if(!isset($items[$slot])) $items[$slot] = "$id,$damage,0";
                    
                    if($id == $armor["helmet"] or $id == $armor["chestplate"] or $id == $armor["leggings"] or $id == $armor["boots"])
                    {
                        --$count;
                    }
                    
                    $items[$slot] = "$id,$damage,$count";
                }
                
                $config->setNested("armor", $armor);
                $config->setNested("items", $items);
			
                $config->save();
                
                break;
        }
    }
}