<?php

  namespace WarnPlayer;

  use pocketmine\plugin\PluginBase;
  use pocketmine\event\Listener;
  use pocketmine\utils\TextFormat as TF;
  use pocketmine\Player;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;
  use pocketmine\command\ConsoleCommandSender;

  class Main extends PluginBase implements Listener {
    public function dataPath() {
      return $this->getDataFolder();
    }

    public function onEnable() {
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
      if(strtolower($cmd->getName()) === "warn") {
        if(!(isset($args[0]) and isset($args[1]))) {
          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /warn <player> < reason >");
          return true;
        } else {
          $sender_name = $sender->getName();
          $name = $args[0];
          $player = $this->getServer()->getPlayer($name);
          if($player === null) {
            $sender->sendMessage(TF::RED . "Player " . $name . " could not be found.");
            return true;
          } else {
            unset($args[0]);
            $player_name = $player->getName();
            if(!(file_exists($this->dataPath() . "Players/" . strtolower($player_name) . ".txt"))) {
              touch($this->dataPath() . "Players/" . strtolower($player_name) . ".txt");
              file_put_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt", "0");
            }
            $reason = implode(" ", $args);
            $file = file_get_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt");
            if($file >= "3") {
              $string = "action_after_three_warns: ";
              $action = substr(strstr(file_get_contents($this->dataPath() . "config.yml"), $string), strlen($string));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "1 1440 warned 3+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "2 1440 warned 3+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "3 1440 warned 3+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "4 1440 warned 3+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "5 1440 warned 3+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "6 1440 warned 3+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "7 1440 warned 3+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "8 1440 warned 3+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "9 1440 warned 3+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "10 1440 warned 3+ times"));
            }
            if($file >= "2") {
              $string = "action_after_three_warns: ";
              $action = substr(strstr(file_get_contents($this->dataPath() . "config.yml"), $string), strlen($string));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "1 5 warned 2+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "2 5 warned 2+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "3 5 warned 2+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "4 5 warned 2+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "5 5 warned 2+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "6 5 warned 2+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "7 5 warned 2+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "8 5 warned 2+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "9 5 warned 2+ times"));
              $this->getServer()->dispatchCommand(new ConsoleCommandSender("jail" . $player_name . "10 5 warned 2+ times"));
            }else{
              $player->sendMessage(TF::YELLOW . "You have been warned by " . $sender_name . " for " . $reason);
              $this->getServer()->broadcastMessage(TF::YELLOW . $player_name . " was warned by " . $sender_name . " for " . $reason);
              $file = file_get_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt");
              file_put_contents($this->dataPath() . "Players/" . strtolower($player_name) . ".txt", $file + 1);
              $sender->sendMessage(TF::GREEN . "Warned " . $player_name . ", and added +1 warns to their file.");
              return true;
            }
          }
        }
      } 
    }
  }
