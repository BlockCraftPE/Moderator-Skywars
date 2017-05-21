<?php

  namespace Driesboy;

  use pocketmine\plugin\PluginBase;
  use pocketmine\event\Listener;
  use pocketmine\utils\TextFormat as TF;
  use pocketmine\Player;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;
  use pocketmine\command\ConsoleCommandSender;
  use pocketmine\event\player\PlayerInteractEvent;
  use pocketmine\utils\Config;
  use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;
  use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
  use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
  use pocketmine\level\Position;

  class Main extends PluginBase implements Listener {
	  
    public $spectator;
	  
    public function dataPath() {
      return $this->getDataFolder();
    }

    public function onEnable() {
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
      $this->spectator = new Config($this->dataPath() . "spectator.txt", Config::ENUM);
      if(!(file_exists($this->dataPath()))) {
        @mkdir($this->dataPath());
        chdir($this->dataPath());
        @mkdir("Players/", 0777, true);
      }
    }

    public function Warns(Player $player, Player $sender , $reason){

      $player_name = $player->getName();
      $file = file_get_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt");
      file_put_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt", $file + 1);

      if(!(file_exists($this->dataPath() . "Players/" . strtolower($player_name) . ".txt"))) {
        touch($this->dataPath() . "Players/" . strtolower($player_name) . ".txt");
        file_put_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt", "0");
      }

      if($file === "2") {
        $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"nban " . $player_name . " 10min " . $reason);
	      
        $sender->sendMessage(TF::GREEN . "" . $player_name . " has been BANNED for 10 min!");
      }

      if($file === "3") {
        $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"nban " . $player_name . " 1hour " . $reason);

        $sender->sendMessage(TF::GREEN . "" . $player_name . " has been BANNED for 1 hour!");
      }

      if($file === "4") {
        $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"nban " . $player_name . " 1day " . $reason);
	      
        $sender->sendMessage(TF::GREEN . "" . $player_name . " has been BANNED for 1 day!");
      }

      if($file >= "5") {
        $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"nban " . $player_name . " 1week " . $reason);
	      
        $sender->sendMessage(TF::GREEN . "" . $player_name . " has been BANNED for 1 week!");
      }else{
        $player->kick(TF::YELLOW . "You are warned for " . $reason . " by a Moderator", false);
        $sender->sendMessage(TF::GREEN . $player_name . " has been warned!");
        return true;
      }
    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
      if(strtolower($cmd->getName()) === "report") {
        if(!(isset($args[0]))) {
          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /report <player>");
          return true;
        }else{
          $sender_name = $sender->getName();
          $sender_display_name = $sender->getDisplayName();
          $name = $args[0];
          $player = $this->getServer()->getPlayer($name);
	  $pn = $player->getName();
          if($player === null) {
            $sender->sendMessage(TF::RED . "Player " . $pn . " could not be found.");
            return true;
          }else{
            foreach($this->getServer()->getOnlinePlayers() as $p) {
              if($p->hasPermission("rank.moderator")) {
                $p->sendMessage(TF::YELLOW . $sender_name . " reported " . $pn . " for teaming / hacking!");
              }
            }
            $player->sendMessage(TF::YELLOW . "You are reported for teaming / hacking!");
            $sender->sendMessage(TF::GREEN . $pn . " has been reported!");
            return true;
          }
        }
      }
      if(strtolower($cmd->getName()) === "spectate") {
          if(!(isset($args[0]))) {
            $sender->sendMessage(TF::RED . "/spectate on <player>");
	    $sender->sendMessage(TF::RED . "/spectate off");
            return true;
          }else{
	    switch (strtolower($args[0])):
		  case 'on':
		  	if($sender->getLevel()->getFolderName() === "Lobby"){
				if(!(isset($args[1]))) {
					$sender->sendMessage(TF::RED . "/spectate on <player>");
				}else{	
					$sender_name = $sender->getName();
					$name = $args[1];
					$player = $this->getServer()->getPlayer($name);
					$player_name = $player->getName();
					if($player === null) {
						$sender->sendMessage(TF::RED . "Player could not be found.");
						return true;
					}else{
						$this->spectator->set(strtolower($sender_name));
						$this->spectator->save();
						$this->getServer()->dispatchCommand(new ConsoleCommandSender(),"tp " . $sender_name . " " . $player_name);
						$sender->gamemode = Player::SPECTATOR;
						$pk = new SetPlayerGameTypePacket();
						$pk->gamemode = Player::CREATIVE;
						$sender->dataPacket($pk);
						$pk = new AdventureSettingsPacket();
						$pk->flags = 207;
						$pk->userPermission = 2;
						$pk->globalPermission = 2;
						$sender->dataPacket($pk);
						$pk = new ContainerSetContentPacket();
						$pk->windowid = ContainerSetContentPacket::SPECIAL_CREATIVE;
						$sender->dataPacket($pk);
						$level = $player->getLevel()->getName();
						foreach ($this->getServer()->getOnlinePlayers()  as $d) {
							$d->hidePlayer($sender);
						}
						return true;
					}
				}	
			}else{
				$sender->sendMessage(TF::RED . "You are not in the Lobby!");
			}	
		  break;
		  
		  case 'off':;
		  	if ($this->spectator->exists($sender->getName())){
				$this->spectator->remove(strtolower($sender->getName()));
                                $this->spectator->save();
				$sender->gamemode = 4;//Just to make sure setGamemode() won't return false if the gm is the same
				if ($sender->hasPermission("rank.diamond")){
				       	$sender->setGamemode("1");
				       	$pk = new ContainerSetContentPacket();
				       	$pk->windowid = ContainerSetContentPacket::SPECIAL_CREATIVE;
				       	$sender->dataPacket($pk);
				}else{
					$sender->setGamemode($sender->getServer()->getDefaultGamemode());
				} 
				foreach ($this->getServer()->getOnlinePlayers()  as $d) {
					$d->showPlayer($sender);
				}
				$sender->teleport(new Position("-0.491200", "77.000000", "9.780400", $this->getServer()->getLevelByName("Lobby")), "179", "-3");
		  	}else{
				$sender->sendMessage(TF::RED . "You are not in spectator mode!");
			} 
		  break;
		  
	    endswitch;
	    return true;
	  } 
      }      
      if(strtolower($cmd->getName()) === "warn") {
        if(!(isset($args[0]) and isset($args[1]))) {
          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /warn <player> <reason>");
          return true;
        } else {
          $sender_name = $sender->getName();
          $name = $args[0];
          $player = $this->getServer()->getPlayer($name);
          if($player === null) {
            $sender->sendMessage(TF::RED . "Player " . $name . " could not be found.");
            return true;
          }else{
            unset($args[0]);
            $reason = implode(" ", $args);
            $this->Warns($player, $sender, $reason);
          }
        }
      }
    }
    public function onInteract(PlayerInteractEvent $ev){
	    if ($this->spectator->exists($ev->getPlayer()->getName())){
	    	$ev->setCancelled();
	    }    
    }
}	  
	  
