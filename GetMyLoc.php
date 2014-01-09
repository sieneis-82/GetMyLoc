<?php

/*
__PocketMine Plugin__
name=GetMyLoc
description=
version=0.0.1
author=sieneis_82
class=GetMyLoc
apiversion=11
*/

class GetMyLoc implements Plugin{
    private $api;
	private $lang, $langFile;
	private $version = "0.0.1";
	private $prefix = "[GetMyLoc]";
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		console($this->prefix." Loading plugin version ".$this->version."...");
	}
	public function init(){
		console($this->prefix." Loading language...");
		$this->langFile = new Config($this->api->plugin->configPath($this)."lang.yml", CONFIG_YAML, array(
			"prefix" => "[GetMyLoc]",
			"help" => array(
				"command-l-description" => " :Get your location.",
				"command-help" => ">>> GetMyLoc command help <<<\n".
							   "> /l :Get your own location.\n".
							   "> /l p <player> :Get one's location.\n".
							   "> /l version :Get plugin version.\n".
							   "> /l ?|help :Get help of this plugin.\n".
							   ">>> Plugin by DreamWork <<<",
			),
			"err" => array(
				"Unknown-subcmd" => "%1 Unknown subcommand. Type '/l ?' or '/l help' for help.",
				"Player-doesnt-exist" => "%1 Player '%p' does not exist. ",
				"Console-no-loc" => "%1 Console has no location!",
			),
			"message" => array(
				"version" => "%1 Version : %v",
				"loc-me" => "%1 My location: \nw:%w, x:%x, y:%y, z:%z",
				"loc-others" => "%1 %p's location: \nw:%w, x:%x, y:%y, z:%z",
			),
		));
		$this->lang = $this->api->plugin->readYAML($this->api->plugin->configPath($this)."lang.yml");
		console($this->prefix." Loading base commands...");
		$this->api->console->register("l", $this->lang["help"]["command-l-description"], array($this, "command"));
		$this->api->ban->cmdWhitelist("l");
		console($this->prefix." Version ".$this->version." successful loaded!");
	}
	public function __destruct(){}
	public function command($cmd, $params, $issuer, $alias){
	 	switch($cmd){
			case "l":
				switch($params[0]){
					default:
						return(str_replace(array("%1"),array($this->lang["prefix"]),$this->lang["err"]["Unknown-subcmd"]));
						break;
					case "version":
						return(str_replace(array("%1","%v"),array($this->lang["prefix"],$this->version),$this->lang["message"]["version"]));
						break;
					case "?":
					case "help":
						return($this->lang["help"]["command-help"]);
						break;
					case "p":
						if($this->api->player->get($params[1]) == false){
							return(str_replace(array("%1","%p"),array($this->lang["prefix"],$params[1]),$this->lang["err"]["Player-doesnt-exist"]));
						}
						$p = $this->api->player->get($params[1]);
						return(str_replace(array("%1","%p","%w","%x","%y","%z"),array($this->lang["prefix"],$params[1],$p->level->getName(),number_format($p->entity->x,1),number_format($p->entity->y,1),number_format($p->entity->z,1)),$this->lang["message"]["loc-others"]));
						break;
					case "":
						if(!($issuer instanceof Player)){return(str_replace(array("%1"),array($this->lang["prefix"]),$this->lang["err"]["Console-no-loc"]));}
						return(str_replace(array("%1","%p","%w","%x","%y","%z"),array($this->lang["prefix"],$params[1],$issuer->level->getName(),number_format($issuer->entity->x,1),number_format($issuer->entity->y,1),number_format($issuer->entity->z,1)),$this->lang["message"]["loc-me"]));
						break;
				}
				break;
		}
	}
}
