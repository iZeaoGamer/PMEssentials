<?php

declare(strict_types=1);

namespace pmessentials\PMEssentials\command;

use pmessentials\PMEssentials\API;
use pmessentials\PMEssentials\event\PlayerFeedEvent;
use pmessentials\PMEssentials\event\TeleportRespondEvent;
use pmessentials\PMEssentials\Main;
use pmessentials\PMEssentials\TeleportRequest;
use pocketmine\command\Command as pmCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TpacceptCommand extends SimpleExecutor {

    public function onCommand(CommandSender $sender, pmCommand $command, string $label, array $args): bool{
        switch ($label) {
            case "tpaccept":
            case "tpyes":
                $ev = new TeleportRespondEvent($sender, true);
                $ev->call();
                if($ev->isCancelled()){
                    return true;
                }

                $user = $this->api->getUserMap()->fromPlayer($sender);
                if ($user->acceptRequest()) {
                    $sender->sendMessage(TextFormat::colorize("&6Teleporting..."));
                } else {
                    $sender->sendMessage(TextFormat::colorize("&4You have no pending requests!"));
                }
                return true;
                break;
            case "tpdeny":
            case "tpno":
                $ev = new TeleportRespondEvent($sender, true);
                $ev->call();
                if($ev->isCancelled()){
                    return true;
                }

                $user = $this->api->getUserMap()->fromPlayer($sender);
                if ($user->denyRequest()) {
                    $sender->sendMessage(TextFormat::colorize("&6Teleport request denied."));
                } else {
                    $sender->sendMessage(TextFormat::colorize("&4You have no pending requests!"));
                }
                return true;
                break;
            default:
                break;
        }
        return true;
    }
}
