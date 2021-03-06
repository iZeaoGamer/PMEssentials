<?php

declare(strict_types=1);

namespace pmessentials\PMEssentials\command;

use pmessentials\PMEssentials\API;
use pmessentials\PMEssentials\Main;
use pocketmine\command\Command as pmCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class SizeCommand extends Command {

    public function __construct(Main $plugin, API $api){
        parent::__construct($plugin, $api);
    }

    public function onCommand(CommandSender $sender, pmCommand $command, string $label, array $args): bool
    {
        if(isset($args[1]) && $sender->hasPermission("pmessentials.size.other")){
            $match = $this->plugin->getServer()->matchPlayer($args[1]);
            if(empty($match)){
                $sender->sendMessage(TextFormat::colorize("&4Player with name &c".$args[1]."&r&4 not found!"));
                return true;
            }
            $player = $match[0];
        }else{
            $player = $sender;
        }

        if(!$player instanceof Player){
            $sender->sendMessage(TextFormat::colorize("&4Target needs to be a player"));
            return true;
        }

        if(!isset($args[0])){
            $size = 1;
        }elseif(is_numeric($args[0])){
            $size = abs($args[0]);
        }else{
            $sender->sendMessage(TextFormat::colorize("&4Please enter a valid size!"));
            return true;
        }

        $player->setScale($size);
        if($player === $sender){
            $sender->sendMessage(TextFormat::colorize("&6You have been resized to &c".$size."&6."));
        }else{
            $sender->sendMessage(TextFormat::colorize("&6Resized &c".$player->getName()."&r&6 to &c".$size."&6."));
            $player->sendMessage(TextFormat::colorize("&6You have been resized to &c".$size."&6."));
        }
        return true;
    }
}