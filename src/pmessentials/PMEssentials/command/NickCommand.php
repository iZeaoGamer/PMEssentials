<?php

declare(strict_types=1);

namespace pmessentials\PMEssentials\command;

use pmessentials\PMEssentials\API;
use pmessentials\PMEssentials\event\PlayerNickChangeEvent;
use pmessentials\PMEssentials\Main;
use pocketmine\command\Command as pmCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class NickCommand extends SimpleExecutor {

    public function onCommand(CommandSender $sender, pmCommand $command, string $label, array $args): bool
    {
        if(isset($args[1]) && $sender->hasPermission(Main::PERMISSION_PREFIX."nick.other")){
            $match = $this->plugin->getServer()->matchPlayer($args[1]);
            if(empty($match)){
                $sender->sendMessage(TextFormat::colorize("&4Player with name &c".$args[1]."&r&4 not found!"));
                return true;
            }
            $player = $match[0];
        }elseif(isset($args[1])){
            $sender->sendMessage(TextFormat::colorize("&4You don't have permission to change someone else's nickname!"));
        }else{
            $player = $sender;
        }

        if(!$player instanceof Player){
            $sender->sendMessage(TextFormat::colorize("&4Target needs to be a player."));
            return true;
        }

        if(!isset($args[0])){
            $player->setDisplayName($player->getName());
            if($player === $sender){
                $sender->sendMessage(TextFormat::colorize("&6Your nick has been cleared."));
            }else{
                $sender->sendMessage(TextFormat::colorize("&6Cleared ".$player->getName()."&6's nick"));
                $player->sendMessage(TextFormat::colorize("&6Your nick has been cleared."));
            }
            return true;
        }

        $str = str_replace("+", " ", $args[0]);
        $str = TextFormat::colorize($str);
        if(!$sender->hasPermission(Main::PERMISSION_PREFIX."nick.color")){
            $str = TextFormat::clean($str);
        }
        if(!$sender->hasPermission(Main::PERMISSION_PREFIX."nick.custom") && strtolower($player->getName()) != strtolower(TextFormat::clean($str))){
            $sender->sendMessage(TextFormat::colorize("&4You're not allowed to set custom nicknames"));
            return true;
        }

        $ev = new PlayerNickChangeEvent($player, $sender, $args[0], $str);
        $ev->call();
        if($ev->isCancelled()){
            return true;
        }
        $player->setDisplayName($ev->getCheckedNick().TextFormat::RESET);
        if($player === $sender){
            $nick = new Config($this->plugin->getDataFolder() . "Players/" . $sender->getName() . ".yml", Config::YAML);
            if($nick->get("nick") === "none"){
                    $nick->set("nick", $ev->getCheckedNick());
                    $nick->save();
            $sender->sendMessage(TextFormat::colorize("&6Your nick has been set to &c".$ev->getCheckedNick()."&r&6."));
        }else{
            $sender->sendMessage(TextFormat::colorize("&6Set ".$player->getName()."&6's nick to &r&c".$ev->getCheckedNick()."&r&6."));
                if($nick->get("nick") === "none"){
                    $nick->set("nick", $ev->getCheckedNick());
                    $nick->save();
            $player->sendMessage(TextFormat::colorize("&6Your nick has been set to &c".$ev->getCheckedNick()."&r&6."));
        }
        return true;
    }
}
