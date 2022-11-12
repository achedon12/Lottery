<?php

namespace achedon\loto;

use achedon\loto\class\Loto;
use achedon\loto\commands\LotoCommand;
use achedon\loto\tasks\LotoTask;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase{

    use SingletonTrait;

    public Loto|null $loto = null;
    private string $prefix;
    public int $maxDeposit;

    protected function onEnable(): void
    {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();

        $this->getServer()->getCommandMap()->register('commands', new LotoCommand("loto","open loto menu","/loto"));

        if(!$this->getServer()->getPluginManager()->getPlugin("BedrockEconomy")){
            $this->getLogger()->alert("You don't have BedrockEconomy on your server, please download it before use this plugin");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        $timer = $this->getConfig()->getNested("loto.timer");
        $defaultProfit = $this->getConfig()->getNested("loto.default-profit");
        $this->prefix = $this->getConfig()->getNested("loto.prefix")."§r ";
        $this->maxDeposit = $this->getConfig()->getNested("loto.max-deposit");

        $this->getScheduler()->scheduleRepeatingTask(new LotoTask($timer,$defaultProfit),20);
    }

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onDisable(): void
    {
        if($this->loto != null){
            foreach($this->loto->getParticipants() as $player){
                $player = Server::getInstance()->getPlayerByPrefix($player);
                BedrockEconomyAPI::legacy()->addToPlayerBalance($player->getName(), Main::getInstance()->loto->getParticipants()[$player->getName()]);
            }
        }
    }

    public function getConfig(): Config
    {
        return new Config($this->getDataFolder()."config.yml",Config::YAML);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

}