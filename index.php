<?php
/**
 * Plugin Name: Admin Menu Slide
 * Plugin URI:  https://wordpress.org/plugins/admin-menu-slide
 * Description: Adds a menu slide feature to the admin menu.
 * Version: 1.0
 * Author: Maciej Krawczyk
 * Author URI: https://profiles.wordpress.org/helium-3
 * License:GPLv2
=====================================================================================
Copyright (C) 2015 Maciej Krawczyk
All Rights Reserved

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WordPress; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
======================================================================================
*/

	defined('ABSPATH') or die();
	
	define('ADMINMENUSLIDE_VERSION','1.0');

	function AdminMenuSlideInit() {
				
		//WindowPress has its own menu slide 
		if (!( basename($_SERVER["PHP_SELF"])==='admin.php' and isset($_GET['page']) and $_GET['page']=='windowpress' ))
			require(dirname(__FILE__).'/admin-menu-slide.php');
			
		require(dirname(__FILE__).'/settings.php');

	}

	if (is_admin()) {
		add_action('plugins_loaded','AdminMenuSlideInit');
		//activation hook
		require(dirname(__FILE__).'/activate.php');
	}

?>
