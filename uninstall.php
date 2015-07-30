<?php
/**
* 
* Plugin uninstallation
* 
* @package Admin Menu Slide
* @author Maciej Krawczyk
*/
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
	
	
	function AMSUninstall() {
		
		$option_name = 'admin-menu-slide-settings';
		delete_option( $option_name );
		$info_name='admin-menu-slide-info';
		delete_option($info_name);
		
	}
	
	global $wpdb;
				
	if (is_multisite()) { 
			
		$current_blog = $wpdb->blogid;
			
		$blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		
		foreach ($blogs as $blog) {
			switch_to_blog($blog);
			if (get_option('admin-menu-slide-settings')) AMSUninstall();
		}
		
		switch_to_blog($current_blog);
	}
		
	elseif (get_option('admin-menu-slide-settings')) AMSUninstall();
	
	
?>
