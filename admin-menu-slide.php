<?php
/**
* 
* The plugin itself
* 
* @package Admin Menu Slide
* @author Maciej Krawczyk
*/


defined('ABSPATH') or die();

class AdminMenuSlide {

	public function __construct() {

		#configuration
		$this->options=get_option($this->option_name);
		$this->plugin_url=plugins_url().'/'.basename(dirname(__FILE__));
		$this->plugin_path=dirname(__FILE__);
		$this->wp_admin_url=get_site_url(null,'wp-admin/');
		
		#javascript, css and html
		add_action( 'admin_enqueue_scripts', array($this, 'include_scripts') );
		add_action('admin_head', array($this,'inline_css'));
		
		$user_id=wp_get_current_user()->ID;

		if (get_user_meta($user_id, 'windowpress-menu-slide',true)==1)
			add_filter( 'admin_body_class',array($this,'add_body_class') );
		elseif (get_user_meta($user_id, 'windowpress-menu-slide',true)==='' ) 
			add_user_meta($user_id, 'windowpress-menu-slide', 0, true);
	
	}

	function add_body_class($classes) {
		
		return $classes.'admin-menu-slide'; 
		 
	}

	public function include_scripts() {
		
		//CSS
		wp_enqueue_style( 'admin-menu-slide', $this->plugin_url.'/includes/css/admin-menu-slide.css', false, ADMINMENUSLIDE_VERSION); 
		
		//main script
		wp_enqueue_script( $this->main_script, $this->plugin_url.'/includes/js/admin-menu-slide.js', array('jquery'), ADMINMENUSLIDE_VERSION, true ); 

		//get icons to include as inline svg
		$icons=array("menu_slide_enable"=>"","menu_slide_disable"=>"");

		foreach ($icons as $key=>&$val) $val=file_get_contents($this->plugin_path.'/includes/icons/'.$key.'.svg');
		unset($val);
		
		$user_id=wp_get_current_user()->ID;

		wp_localize_script( $this->main_script, 'AMS_PHP',
		array( 
			//configuration
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'plugin_url' => $this->plugin_url,
			'svgIcons' => $icons,
			'sidebar_slide' => get_user_meta($user_id, 'windowpress-menu-slide'),
			'sidebar_slide_duration' => $this->options['sidebar_slide_duration'],

			
			//locale
			'text_disable_menuslide' => __('Disable sliding',$this->text_domain),
			'text_enable_menuslide' => __('Enable sliding', $this->text_domain)
		));

	}

	public function inline_css() {

        global $_wp_admin_css_colors;
		$scheme=get_user_option('admin_color');
		
		$icon_colors= $_wp_admin_css_colors[$scheme]->icon_colors;
		
		echo "<style>			
			#adminmenuslide-toggle span { color: $icon_colors[base]; }
			#adminmenuslide-toggle path { fill: $icon_colors[base]; }
			#adminmenuslide-toggle:hover span { color: $icon_colors[focus]; }
			#adminmenuslide-toggle:hover path { fill: $icon_colors[focus]; }
		</style>";

    }

	private $options;
	private $wp_admin_url;
	private $plugin_url;
	private $plugin_path;
	private $option_name='admin-menu-slide-settings';
	private $text_domain='admin-menu-slide';
	private $main_script='admin-menu-slide';

}

$admin_menu_slide=new AdminMenuSlide();


?>
