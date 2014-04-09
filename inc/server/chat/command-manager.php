<?php
/**
 * Simply call processCommand($user, $str) to use it.
 * $user ... user context
 * $str  ... command string(if its a command or not)
 * The return value is false, if the command was not found / not handled.
 * The return value is true, if a command driver handled the command successfully.
 **/
class CommandManager {
	private $commands = array();
	private $server;
	
	public function __construct($server) {
		$this->server = $server;
		registerCommands();
	}

	private function registerCommands() {
		//Example:
		//array_push($this->commands, new SampleCmdDriver());
	}
	
	public function processCommand($user, $str) {
		if(substr($str, 0, 1) == '/') {
			$args = explode(" ", $str);
			return processCommandArgs($user, $args);
		}else
			return false;
	}
	
	public function processCommandArgs($user, $args) {
		for($this->commands as $cmd) {
			if($cmd->matches($args[0]))
				return $cmd->execute($this->server, $user, $args);
		}
		return false;
	}
}
?>