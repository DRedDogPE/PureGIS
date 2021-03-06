<?php

namespace _64FF00\PureGIS;

use pocketmine\scheduler\PluginTask;

class GISPluginTask extends PluginTask
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

    /**
     * @param PureGIS $plugin
     * @param $funcName
     * @param array $args
     */
    public function __construct(PureGIS $plugin, $funcName, array $args)
    {
        parent::__construct($plugin);

        $this->funcName = $funcName;
        $this->args = $args;
    }

    public function onRun($currentTick)
    {
        call_user_func_array(array($this->owner, $this->funcName), $this->args);
    }
}