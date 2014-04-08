<?php
global $commands = array();

function registerCommands() {
	
}

function processCommand($server, $user, $str) {
	if(substr($str, 0, 1) != '/')
		return false;
	else {
		$args = explode(" ", $str);
		for($commands as $cmd) {
			if($cmd->matches($args[0])) {
				cmd->execute($server, $user, $args);
				return true;
			}
		}
		return false;
	}
}
?>