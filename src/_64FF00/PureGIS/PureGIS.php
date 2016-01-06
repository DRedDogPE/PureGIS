<?php

namespace _64FF00\PureGIS;

use pocketmine\item\Item;

use pocketmine\Server;

use pocketmine\plugin\PluginBase;

use pocketmine\Player;

use pocketmine\level\Level;

use pocketmine\utils\Config;

class PureGIS extends PluginBase
{  
    /* PureGIS by 64FF00 (xktiverz@gmail.com, @64ff00 for Twitter) */

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
		foreach ($this->getServer()->getLevels() as $world) {
			if ($world instanceof Level) $world = $world->getName();
			@mkdir($this->getDataFolder() . $world . "/players/", 0777, true);
			$this->getLogger()->info(TextFormat::GREEN . $world . "loaded." );
        }
        $this->getServer()->getPluginManager()->registerEvents(new GISListener($this), $this);
    }
    
    public function onDisable()
    {
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function configExists(Player $player, $world)
    {
        return file_exists($this->getDataFolder() . $world . "/players/" . strtolower($player->getName()) . ".yml");
    }

    /**
     * @param Player $player
     * @return Config
     */
    public function getPlayerConfig(Player $player, $world)
    {
        if(!(file_exists($this->getDataFolder() . $world . "/players/" . strtolower($player->getName()) . ".yml")))
        {
            return new Config($this->getDataFolder() . $world . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML, [
                "userName" => $player->getName(),
                "armor" => [
                ],
                "items" => [
                ]
            ]);
        }
        
        return new Config($this->getDataFolder() . $world . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML, [
        ]);
    }

    /**
     * @param Player $player
     */
    public function loadArmorContents(Player $player)
    {
        if($this->configExists($player))
        {
            $player->getInventory()->clearAll();
			
			$world = $player->getLevel()->getName();

            $config = $this->getPlayerConfig($player, $world);

            $armorList = $config->getNested("armor");

            if(!empty($armorList))
            {
                foreach($armorList as $armorId)
                {
                    $item = Item::get($armorId, 0, 1);

                    $player->getInventory()->setArmorContents([$item]);
                }

                $player->getInventory()->sendArmorContents($player);

                $config->setNested("armor", []);
                $config->save();
            }
        }
    }

    /**
     * @param Player $player
     */
    public function loadContents(Player $player)
    {
        if($this->configExists($player))
        {
            $player->getInventory()->clearAll();

			$world = $player->getLevel()->getName();

            $config = $this->getPlayerConfig($player, $world);

            $itemsList = $config->getNested("items");

            if(!empty($itemsList))
            {
                foreach($itemsList as $itemInfo)
                {
                    $tmp = explode(":", $itemInfo);

                    $id = (int) $tmp[0];
                    $damage = (int) $tmp[1];
                    $count = (int) $tmp[2];
                    $item = Item::get($id, $damage, $count);

                    $player->getInventory()->addItem($item);
                }

                $config->setNested("items", []);
                $config->save();
            }
        }
    }

    /**
     * @param Player $player
     */
    public function saveArmorContents(Player $player)
    {
        $armor = [];

        $armor[] = $player->getInventory()->getHelmet()->getId();
        $armor[] = $player->getInventory()->getChestplate()->getId();
        $armor[] = $player->getInventory()->getLeggings()->getId();
        $armor[] = $player->getInventory()->getBoots()->getId();

		$world = $player->getLevel()->getName();

		$config = $this->getPlayerConfig($player, $world);

        $config->setNested("armor", $armor);
        $config->save();
    }

    /**
     * @param Player $player
     */
    public function saveContents(Player $player)
    {
        $items = [];

        $helmetId = $player->getInventory()->getHelmet()->getId();
        $chestplateId = $player->getInventory()->getChestplate()->getId();
        $leggingsId = $player->getInventory()->getLeggings()->getId();
        $bootsId = $player->getInventory()->getBoots()->getId();

        foreach($player->getInventory()->getContents() as $slot => $item)
        {
            $id = $item->getId();
            $damage = $item->getDamage();
            $count = $item->getCount();

            if($slot > $player->getInventory()->getSize())
            {
                if($id == $helmetId or $id == $chestplateId or $id == $leggingsId or $id == $bootsId)
                {
                    --$count;
                }
            }

            $items[] = "$id:$damage:$count";
        }

		$world = $player->getLevel()->getName();

		$config = $this->getPlayerConfig($player, $world);

        $config->setNested("items", $items);
        $config->save();
    }
}
