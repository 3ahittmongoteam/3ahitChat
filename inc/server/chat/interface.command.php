<?php
interface ICommand {
	/*
	 * Implement this, to determine, if your driver is the right one for the given command.
	 */
	public function matches($command);
	
	/*
	 * Execute your command.
	 * You get a args array beginning with the command, and followed by all your arguments.
	 * The first valid args index to read, is the indexer origin. Do not read elements
	 * before it.
	 * The user is the user, which executed the command.
	 */
	public function execute($server, $user, $args, $origin);
}
?>