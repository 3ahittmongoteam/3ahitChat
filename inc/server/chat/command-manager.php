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
		registerCommands();
	}
	
	/*
	 * Register your new drivers here.
	 * Do not forget to include the specific files for your driver first.
	 */
	private function registerCommands() {
		//Example:
		//array_push($this->commands, new SampleCmdDriver());
	}
	
	/*
	 * Its very likely, that you call this method, if you want to execute a command.
	 */
	public function processCommand($user, $str) {
		if(strlen($str) < 2)
			return false;
		else if(substr($str, 0, 1) == '/') {
			$args = explode(" ", substr($str, 1));
			return processCommandArgs($user, $args);
		}else
			return false;
	}
	
	/*
	 * This method is for recursive command parsing. Most likely a driver itself will
	 * call it.
	 */
	public function processCommandArgs($user, $args) {
		for($this->commands as $cmd)
			if($cmd->matches($args[0]))
				return $cmd->execute($this->server, $user, $args);
		return false;
	}
}
?>