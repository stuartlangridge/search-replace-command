<?php

class Server_Command extends WP_CLI_Command {

	/**
	 * Start a development server.
	 *
	 * ## OPTIONS
	 *
	 * --host=<host>
	 * : The hostname to bind the server to. Default: localhost
	 *
	 * --port=<port>
	 * : The port number to bind the server to. Default: 8080
	 *
	 * ## EXAMPLES
	 *
	 *     # Make the instance available on any address (with port 8080)
	 *     wp server --host=0.0.0.0
	 *
	 *     # Run on port 80 (for multisite)
	 *     sudo wp server --host=localhost.localdomain --port=80
	 *
	 * @when before_wp_load
	 * @synopsis [--host=<host>] [--port=<port>]
	 */
	function __invoke( $_, $assoc_args ) {
		$min_version = '5.4';
		if ( version_compare( PHP_VERSION, $min_version, '<' ) ) {
			WP_CLI::error( "The `wp server` command requires PHP $min_version or newer." );
		}

		$defaults = array(
			'host' => 'localhost',
			'port' => 8080
		);
		$assoc_args = array_merge( $defaults, $assoc_args );

		$config_path = WP_CLI::get_runner()->project_config_path;

		if ( !$config_path ) {
			$docroot = ABSPATH;
		} else {
			$docroot = dirname( $config_path );
		}

		$cmd = \WP_CLI\Utils\esc_cmd( PHP_BINARY . ' -S %s -t %s %s',
			$assoc_args['host'] . ':' . $assoc_args['port'],
			$docroot,
			WP_CLI_ROOT . '/php/router.php'
		);

		$descriptors = array( STDIN, STDOUT, STDERR );

		exit( proc_close( proc_open( $cmd, $descriptors, $pipes ) ) );
	}
}

WP_CLI::add_command( 'server', 'Server_Command' );

