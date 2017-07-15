<?php
namespace StatusBossBar;

use StatusBossBar\Main;
use pocketmine\Server;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

class BossBarTask extends PluginTask{	

    public function __construct(Main $owner){
        parent::__construct($owner);
        $this->plugin = $owner;
    }

    public function onRun($currentTick){	
        $this->getOwner()->sendBossBar();
    }

    public function cancel(){
        $this->getHandler()->cancel();
    }
}