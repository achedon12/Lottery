<?php

namespace achedon\loto\class;

use achedon\loto\Main;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use pocketmine\player\Player;

class Loto{

    private int $timer;

    private int $sum;

    private array $participants = [];

    /**
     * @param int $timer
     * @param int $sum
     */
    public function __construct(int $timer, int $sum)
    {
        $this->timer = $timer;
        $this->sum = $sum;
    }

    /**
     * @return int
     */
    public function getTimer(): int
    {
        return $this->timer;
    }

    /**
     * @return int
     */
    public function getSum(): int
    {
        return $this->sum;
    }

    /**
     * @param int $timer
     */
    public function setTimer(int $timer): void
    {
        $this->timer = $timer;
    }

    /**
     * @param int $sum
     */
    private function addSum(int $sum): void
    {
        $this->sum += $sum;
    }

    public function getParticipants(): array
    {
        return $this->participants;
    }

    /**
     * @param Player $player
     * @param int $amount
     */
    public function addParticipant(Player $player, int $amount): void{
        $this->participants[$player->getName()] = $amount;
        $this->addSum($amount);
        BedrockEconomyAPI::legacy()->subtractFromPlayerBalance($player->getName(),$amount);
    }

    /**
     * @param Player $player
     */
    public function removeParticipant(Player $player): void{
        BedrockEconomyAPI::legacy()->addToPlayerBalance($player->getName(),Main::getInstance()->loto->getParticipants()[$player->getName()]);
        unset(Main::getInstance()->loto->getParticipants()[$player->getName()]);
    }
}