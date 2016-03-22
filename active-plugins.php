<?php
/*
Plugin Name: Active Plugins
Description: Get number of users for each active plugin (minus network-activated). Then break down by site.  Original Plugin URI: http://trepmal.com/plugins/active-plugins-on-multisite/
Author: Cameron Macintosh, Kailey Lampert
Version: 1.6.1
Author URI: http://kaileylampert.com/
Network: true

Copyright (C) 2011-12 Kailey Lampert

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class activeplugins {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
		if ( ! is_multisite() )
			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
		else
			add_action( 'network_admin_menu', array( &$this, 'network_admin_menu' ) );
	}

	function admin_notices() {
		echo '<div class="error fade"><p>';
		_e( 'Acitve Plugins is for multisite use only.', 'active-plugins' );
		echo '</p></div>';
	}

	function network_admin_menu() {
		add_submenu_page( 'plugins.php', __( 'Active Plugins', 'active-plugins' ), __( 'Active Plugins', 'active-plugins' ), 'unfiltered_html', __FILE__, array( &$this, 'page' ) );
	}

	function page() {

		echo '<div class="wrap">';
		echo '<h2>'. __( 'Active Plugins', 'active-plugins' ) .'</h2>';
		echo '<p>'. __( 'Network-Activated plugins not listed.', 'active-plugins' ) .'</p>';

		global $wpdb;
		$query = "SELECT * FROM {$wpdb->blogs}, {$wpdb->registration_log}
							WHERE site_id = '{$wpdb->siteid}'
							AND {$wpdb->blogs}.blog_id = {$wpdb->registration_log}.blog_id";

		$blog_list = $wpdb->get_results( $query, ARRAY_A ); //get blogs
		$all_plugins = get_plugins();
		$plugins_list = array_keys( get_plugins() );

		$pi = array();

		//add main site to beginning
		$blog_list[-1] = array( 'blog_id' => 1 );
		ksort($blog_list);
		foreach( $blog_list as $k => $info ) {
			//loop through the blogs
			//store active plugins is giant array index by blog id
			$bid = $info['blog_id'];
			$pi[ $bid ] = get_blog_option( $bid, 'active_plugins' );
		}
		$pi = array_filter($pi); //remove empties

		$pi_count = array();
		foreach($pi as $k => $v_array) {
			//put all active plugins into one array, we can then count duplicate values
			$pi_count = array_merge($pi_count, $v_array);
		}

		echo '<div style="background:#f3f3f3;padding:5px;">';
		_e( 'Totals (each active plugin and how many users)', 'active-plugins' );

			$totals = $tags = array_count_values( $pi_count );
			ksort( $totals );
			echo '<ul class="ul-disc">';
			foreach( $totals as $name => $tot) {

				if ( strpos( $name, '/') !== false) {
					$dir = WP_PLUGIN_DIR . '/' . dirname( $name );
					$dottags = ( glob( $dir . '/*.tag') );
					if ( ! empty( $dottags ) )
						$tags[ $name ] = str_replace( $dir . '/', '', str_replace( '.tag', '', $dottags['0'] ) );
				}

				if ( in_array( $name, $plugins_list ) ) {
					$plugins_list = array_flip( $plugins_list );
					unset( $plugins_list[ $name ] );
					$plugins_list = array_flip( $plugins_list );
				}

				$version = isset( $all_plugins[$name]['Version'] ) ? $all_plugins[$name]['Version'] : '';
				$version = sprintf( __( 'v%s', 'active-plugins' ), $version );
				if ( isset( $all_plugins[ $name ] ) ) {
					$label = sprintf( __( '%1$s %2$s', 'active-plugins' ), $all_plugins[$name]['Name'], $version );
				} else {
					$label = sprintf( __( '%s (Uninstalled)', 'active-plugins' ), $name );
				}

				$label .= is_numeric( $tags[ $name ] ) ? '' : sprintf( __( ' (tagged: %s)', 'active-plugins' ), $tags[ $name ] );

				$fulllabel = sprintf( _n( '<strong>%s</strong> is used by %d site', '<strong>%s</strong> is used by %d sites', $tot, 'active-plugins' ), $label, $tot );
				echo "<li>$fulllabel</li>";

			}
			echo '</ul>';

			//find which are network-activated
			$network_plugins = array_flip( get_site_option('active_sitewide_plugins') );
			//remove those from our list
			$remove_network = array_diff( $plugins_list, $network_plugins );

			//show which not-network-activated plugins have 0 users
			_e( 'Plugins with zero (0) users:', 'active-plugins' );
			echo '<ul class="ul-disc">';
			foreach( $remove_network as $k => $inactive ) {
				// $realname = $all_plugins[$inactive]['Name'] . ' v' . $all_plugins[ $inactive ]['Version'];
				$version = isset( $all_plugins[$inactive]['Version'] ) ? $all_plugins[$inactive]['Version'] : '';
				$version = sprintf( __( 'v%s', 'active-plugins' ), $version );
				$realname = sprintf( __( '%1$s %2$s', 'active-plugins' ), $all_plugins[ $inactive ]['Name'], $version );
				$unused[] = "<li>{$realname}</li>";
			}
			echo empty( $unused ) ? '<li><em>'. __( 'none', 'active-plugins' ) .'</em></li>' : implode( $unused );
			echo '</ul>';

		echo '</div>';

		echo '<div style="background:#dfdfdf;padding:5px;margin-top:30px;">';
			foreach( $pi as $siteid => $list ) {

				switch_to_blog( $siteid );

				$edit = network_admin_url( "site-info.php?id=$siteid" );
				$view = home_url();
				$dash = admin_url();
				$plugins = admin_url('/plugins.php');

				$blogname = get_bloginfo('name');
				$edit_label = __( 'Edit', 'active-plugins' );
				$view_label = __( 'View', 'active-plugins' );
				$dashboard_label = __( 'Dashboard', 'active-plugins' );
				$plugins_label = __( 'Plugins', 'active-plugins' );

				echo "<h3>$blogname ($siteid) [<a href='$edit'>$edit_label</a>] [<a href='$view'>$view_label</a>] [<a href='$dash'>$dashboard_label</a>] [<a href='$plugins'>$plugins_label</a>]</h3>";
				echo '<ul class="ul-disc">';
				$tagged = array();
				$nottagged = array();
				foreach( $list as $name ) {
					$realname = isset( $all_plugins[ $name ] ) ? $all_plugins[ $name ]['Name'] : $name;
					if ( is_numeric( $tags[ $name ] ) )
						$nottagged[] .= "<li>{$realname}</li>";
					else
						$tagged["<li>({$tags[ $name ]}) $realname</li>"] = $tags[ $name ];
				}
				asort( $tagged );
				$tagged = array_keys( $tagged );
				echo implode( $tagged );

				sort( $nottagged );
				echo implode( $nottagged );
				echo '</ul><hr />';

				restore_current_blog();
			}

		echo '</div>';

		echo '</div>';

	}// end page()

}
new activeplugins();
