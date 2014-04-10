<?php
/*
 * Simply call processCommand($user, $str) to use it.
 * $user ... user context
 * $str  ... command string(if its a command or not)
 * The return value is false, if the command was not found / not handled.
 * The return value is true, if a command driver handled the command successfully.
 */
class CommandManager {
	private $commands = array();
	private $server;
	
	/*
	 * Only needed variable is the server as an object.
	 */
	public function __construct($server) {
		$this->server = $server;
		$this->registerCommands();
	}
	
	/*
	 * Drivers are automatically registered here. Place them in the command-drivers sub folder.
	 */
	private function registerCommands() {
		$driverfiles = scandir('inc/server/chat/command-drivers');
		foreach($driverfiles as $driverfile) {
			$classname = substr($driverfile, 0, strrchr($driverfile, '.'));
			array_push($this->commands, new $classname());
		}
	}
	
	/*
	 * Its very likely, that you call this method, if you want to execute a command.
	 */
	public function processCommand($user, $str) {
		if(strlen($str) < 2)
			return false;
		else if(substr($str, 0, 1) == '/') {
			$args = explode(" ", substr($str, 1));
			return processCommandArgs($user, $args, 0);
		}else
			return false;
	}
	
	/*
	 * This method is for recursive command parsing. Most likely a driver itself will
	 * call it.
	 */
	public function processCommandArgs($user, $args, $origin) {
		foreach($this->commands as $cmd)
			if($cmd->matches($args[0]))
				return $cmd->execute($this->server, $user, $args, $origin);
		return false;
	}
}
?>