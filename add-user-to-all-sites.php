<?php
/**
 * Author URI:        https://github.com/barryceelen/
 * Author:            Barry Ceelen
 * Description:       A CLI command that helps you add an existing user to all sites in a multisite network.
 * Domain Path:       /languages
 * License:           GPLv3+
 * Plugin Name:       Add User to All Sites
 * Plugin URI:        https://github.com/barryceelen/add-user-to-all-sites/
 * Text Domain:       add-user-to-all-sites
 * Version:           1.0.0
 * Requires PHP:      5.3.0
 * Requires at least: 3.1.0
 * GitHub Plugin URI: barryceelen/add-user-to-all-sites
 *
 * @package AddUserToAllSites
 */

if ( ! defined( 'WP_CLI' ) ) {
	return;
}

/**
 * CLI command class.
 */
class Add_User_To_All_Sites_Command {

	/**
	 * User fetcher.
	 *
	 * @var \WP_CLI\Fetchers\User
	 */
	private $fetcher;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->fetcher = new \WP_CLI\Fetchers\User();
	}

	/**
	 * Adds an existing user to all sites in a multisite network.
	 *
	 * ## OPTIONS
	 *
	 * <user>
	 * : The user login, user email, or user ID of the user to add.
	 *
	 * [--role=<role>]
	 * : A string used to set the role of newly added users on each site.
	 * Defaults to `subscriber` if not set. If the user already exists on
	 * a site they will keep their existing role.
	 *
	 * ## EXAMPLES
	 *
	 *     wp add-user-to-all-sites 123 --role=administrator
	 *     wp add-user-to-all-sites bob --role=editor
	 *     wp add-user-to-all-sites bob@example.com
	 *
	 * @param array $args       The array of positional arguments.
	 * @param array $assoc_args The array of associative arguments.
	 * @return void
	 */
	public function __invoke( $args, $assoc_args ) {

		$user = $this->fetcher->get_check( $args[0] );

		$error_count = 0;
		$role        = empty( $assoc_args['role'] ) ? 'subscriber' : trim( $assoc_args['role'] );
		$sites       = get_sites();

		foreach ( $sites as $site ) {

			$url = untrailingslashit( $site->domain . $site->path );

			/*
			 * If the user already belongs to this site, skip it to
			 * preserve their existing role.
			 */
			if ( is_user_member_of_blog( $user->ID, $site->blog_id ) ) {
				WP_CLI::log( "User already exists on {$url}, skipping." );
				continue;
			}

			$result = add_user_to_blog( $site->blog_id, $user->ID, $role );

			if ( is_wp_error( $result ) ) {

				$message = $result->get_error_message();
				++$error_count;

				WP_CLI::log( "An error occurred adding user to {$url}: {$message}" );

			} else {
				WP_CLI::log( "User added to {$url}" );
			}
		}

		WP_CLI::log(
			sprintf(
				'Adding user %s to all sites completed%s.',
				$user->user_login,
				0 === $error_count ? '' : " with {$error_count} errors."
			)
		);
	}
}

WP_CLI::add_command(
	'add-user-to-all-sites',
	'Add_User_To_All_Sites_Command',
	array(
		'before_invoke' => function () {
			if ( ! is_multisite() ) {
				WP_CLI::error( 'This is not a multisite installation.' );
			}
		},
	)
);
