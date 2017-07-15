<?php
/**
 * Created by PhpStorm.
 * User: danil
 * Date: 15.07.2017
 * Time: 18:40
 */

namespace StatusBossBar;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use StatusBossBar\BossBarTask;
use xenialdan\BossBarAPI\API;
use pocketmine\utils\TextFormat as F;

class Main extends PluginBase
{
    private $eid = [];

    public function onEnable(){
        $this->getLogger()->info(F::YELLOW."StatusBossBar включён!");
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new BossBarTask($this), 15);
        if(!$this->eco = $this->getServer()->getPluginManager()->getPlugin('EconomyAPI')){
            $this->getServer()->getLogger()->alert("[StatusBossBar] EconomyAPI не найден!");
        }
        if(!$this->pp = $this->getServer()->getPluginManager()->getPlugin('PurePerms')){
            $this->getServer()->getLogger()->alert("[StatusBossBar] PurePerms не найден!");
        }
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        if(!$this->bb = $this->getServer()->getPluginManager()->getPlugin('BossBarAPI')){
            $this->getServer()->getLogger()->error("[StatusBossBar] BossBarAPI не найден!");
            $this->getServer()->getLogger()->error("[StatusBossBar] Отключаюсь");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    public function sendBossBar(){
        $online = $this->getServer()->getOnlinePlayers();
        $monline = $this->getServer()->getMaxPlayers();
        if(count($online) == 0) return;
        foreach($online as $player){
            $name = $player->getName();
            $text = $this->config->get("text");
            if($this->eco){
                $text = str_replace("{money}", $this->eco->myMoney($name), $text);
            }
            if($this->pp){
                $text = str_replace("{group}", $this->pp->getUserDataMgr()->getGroup($player), $text);
            }
            $text = str_replace("{name}", $name, $text);
            $text = str_replace("{online}", count($online), $text);
            $text = str_replace("{max_online}", $monline, $text);
            if(!isset($this->eid[$player->getName()])){
                $eid = API::addBossBar([$player], $text);
                $this->eid[$name] = $eid;
            }
            API::sendBossBarToPlayer($player, $this->eid[$name], $text);
            API::setPercentage((count($online)/$monline)*100 , $this->eid[$player->getName()]);
        }
    }

}