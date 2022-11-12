<?php

namespace achedon\loto\commands;

use achedon\loto\librairies\forms\CustomForm;
use achedon\loto\librairies\forms\CustomFormResponse;
use achedon\loto\librairies\forms\elements\Button;
use achedon\loto\librairies\forms\elements\Input;
use achedon\loto\librairies\forms\elements\Label;
use achedon\loto\librairies\forms\MenuForm;
use achedon\loto\Main;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use const pocketmine\BEDROCK_DATA_PATH;

class LotoCommand extends Command{

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $main = Main::getInstance();
        if(!$sender instanceof Player){
            return;
        }

        if($main->loto === null){
            $sender->sendMessage($main->getPrefix()."No lotto in progress");
            return;
        }

        if(isset(Main::getInstance()->loto->getParticipants()[$sender->getName()])){
            $amount = Main::getInstance()->loto->getParticipants()[$sender->getName()];
            $buttons = [
                new Button("§2Yes"),
                new Button("§4No")
            ];
            $sender->sendForm(new MenuForm("§c- §bWithdraw your stake §c-","You currently have {$amount} $.",$buttons,function (Player $player,Button $button): void{
                switch ($button->getValue()){
                    case 0:
                        Main::getInstance()->loto->removeParticipant($player);
                        $player->sendMessage(Main::getInstance()->getPrefix()."You have withdrawn your money from the lottery");
                        break;
                    case 1:
                        break;
                }
            }));
        }else{
            $sender->sendForm(new CustomForm("§c- §bParticipate in the lottery §c-",[
                new Input("Deposit a certain amount of money to participate in the lottery.","1000",)
            ],function (Player $player, CustomFormResponse $response): void{
                $value = $response->getInput()->getValue();
                if(!is_numeric($value) || $value < 0 || $value > Main::getInstance()->maxDeposit){
                    $player->sendMessage(Main::getInstance()->getPrefix()."This amount is not allowed");
                    return;
                }
                $player->sendMessage(Main::getInstance()->getPrefix()."You have deposited {$value} $ in the lottery");
                Main::getInstance()->loto->addParticipant($player, $value);
            }));
        }

    }
}