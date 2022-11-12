<?php

namespace achedon\loto\tasks;

use achedon\loto\class\Loto;
use achedon\loto\Main;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class LotoTask extends Task{

    private int $timer;
    private int $defaultSum;
    private int $time = 0;

    /**
     * @param int $timer
     * @param int $defaultSum
     */
    public function __construct(int $timer, int $defaultSum)
    {
        $this->timer = $timer;
        $this->defaultSum = $defaultSum;
    }

    public function onRun(): void
    {
        if(Main::getInstance()->loto == null){
            if($this->time === 0){
                $this->time = mt_rand(25,120);
            }

            $this->time--;

            if($this->time <= 0){
                Main::getInstance()->loto = new Loto($this->timer,$this->defaultSum);
                foreach(Server::getInstance()->getOnlinePlayers() as $player){
                    $player->sendMessage(Main::getInstance()->getPrefix()."A new lottery has been started ! The default amount is 1000 $");
                }
                $this->time = 0;
            }
        }else{
            Main::getInstance()->loto->setTimer(Main::getInstance()->loto->getTimer() - 1);

            if(Main::getInstance()->loto->getTimer() <= 0){

                if(count(Main::getInstance()->loto->getParticipants()) === 0){
                    foreach(Server::getInstance()->getOnlinePlayers() as $player){
                        $player->sendMessage(Main::getInstance()->getPrefix()."Nobody has played at the lottery");
                    }
                }else{
                    $winner = array_rand(Main::getInstance()->loto->getParticipants());
                    BedrockEconomyAPI::legacy()->addToPlayerBalance($winner,($sum = Main::getInstance()->loto->getSum()));
                    foreach(Server::getInstance()->getOnlinePlayers() as $player){
                        $player->sendMessage(Main::getInstance()->getPrefix()."The player {$winner} has won the sum of {$sum} $");
                    }
                }

                Main::getInstance()->loto = null;
            }
        }
    }
}