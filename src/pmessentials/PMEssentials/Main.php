<?php

declare(strict_types=1);

namespace pmessentials\PMEssentials;

use pmessentials\PMEssentials\API;
use pmessentials\PMEssentials\listener\BackListener;
use pmessentials\PMEssentials\listener\GodmodeListener;
use pmessentials\PMEssentials\listener\ListenerBase;
use pmessentials\PMEssentials\listener\PlayerEventListener;
use pmessentials\PMEssentials\listener\PowertoolListener;
use pmessentials\PMEssentials\listener\VanishListener;
use pmessentials\PMEssentials\module\ModuleManager;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Main extends PluginBase{

    public const PERMISSION_PREFIX = "pmessentials.";

    /** @var API */
    public $api;
    /** @var ModuleManager */
    public $moduleManager;

    /** @var array|ListenerBase */
    public $listeners = [];

    /** @var EssentialsCommandMap */
    public $commandMap;

    /** @var UserMap */
    public $userMap;

    private static $instance;

    /** @var Config */
    public $config;

    public function onLoad(){
        self::$instance = $this;
	$this->saveResource("commands.yml");
        $this->api = API::getAPI();
        $this->config = $this->getConfig();
	 $this->commands = new Config($this->getDataFolder() . "commands.yml");
        $this->moduleManager = new ModuleManager($this);
        $this->userMap = new UserMap();
    }

    public function onEnable() : void{
        $this->listeners[VanishListener::class] = new VanishListener();
        $this->listeners[PowertoolListener::class] = new PowertoolListener();
        $this->listeners[GodmodeListener::class] = new GodmodeListener();
        $this->listeners[BackListener::class] = new BackListener();
	    $this->commandMap = EssentialsCommandMap::getInstance();

	    $this->listeners[PlayerEventListener::class] = new PlayerEventListener();
	}


	public function onDisable() : void{
	}

	public static function getInstance() : Main{
        return self::$instance;
    }

    public function getModuleManager() : ModuleManager{
        return $this->moduleManager;
    }

    public function getUserMap() : UserMap{
        return $this->userMap;
    }
}
