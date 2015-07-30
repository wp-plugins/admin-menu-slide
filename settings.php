<?php
/**
* 
* Add settings page
* Ajax Receiver for toggling menuslide
* 
* @package Admin Menu Slide
* @author Maciej Krawczyk
*/


class AdminMenuSlide_Settings {

	public function __construct() {
			
		$this->plugin_url=plugins_url().'/'.basename(dirname(__FILE__));
		$this->plugin_path=dirname(__FILE__);
		$this->options=get_option($this->option_name);
		$this->info=get_option($this->info_name);

		add_action( 'wp_ajax_adminmenuslide_toggle', array($this,'adminmenuslide_toggle_callback') );

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'add_settings_fields' ) );
        
        if (basename($_SERVER['PHP_SELF'])==='options-general.php' && isset($_GET['page']) && $_GET['page']===$this->settings_page_id)
			add_action('admin_enqueue_scripts',array($this,'enqueue_settings_scripts'));

	}
	
	function adminmenuslide_toggle_callback() {
		
				
		if (!isset($_GET['value'])) { wp_die(); return; }
		
		$user_id=wp_get_current_user()->ID;
		
		$value=intval($_GET['value']);
		
		if (!empty($user_id) || ($value===1 || $value===0) ) {
			if (get_user_meta($user_id, 'windowpress-menu-slide',true)!=='') //if usermeta exists
				update_user_meta( $user_id, 'windowpress-menu-slide', $value);
		}
	
		wp_die();
	}

	public function enqueue_settings_scripts() {
		wp_enqueue_style('admin-menu-slide-settings', $this->plugin_url.'/includes/css/settings.css');
	}

	public function add_plugin_page() {
		add_options_page( 'Admin Menu Slide', 'Admin Menu Slide', 'manage_options', $this->page_id, array($this,'plugin_page') );
	}
	
	public function plugin_page() {
		
		?>
		<div class="wrap">
			
		<h2>Admin Menu Slide</h2>
		
		<div id="pluginpage-about">
						
			<div id="pluginpage-windowpress">
			
				<div class="about-section">
					<p>If you like Admin Menu Slide, you will love WindowPress. Click on the thumbnail for more information.</p>
					<a href="https://wordpress.org/plugins/windowpress/" target="_blank"><img src="<?php echo $this->plugin_url.'/includes/images/windowpress.png'; ?>" /></a>
				</div>
			</div>
			
			<div class="about-row-2">

				<div class="about-section">
				<p>If you like this plugin and find it useful, please consider donating and/or giving it a 5-star review.</p>
				<a class="button button-blue mobile-button-noborder-right" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=P5UC9VG3Q687N" target="_blank">Donate</a>
				</div>
			
				<div class="about-section">
				<a class="button button-red mobile-button-noborder-left mobile-button-noborder-right" href="https://wordpress.org/support/view/plugin-reviews/admin-menu-slide?rate=5#postform" target="_blank">Review</a>
				</div>
			
				<div class="about-section">
				<p>Need help, found a bug? Check the Support Forum.</p>
				<a class="button button-green mobile-button-noborder-left" href="https://wordpress.org/support/plugin/admin-menu-slide" target="_blank">Forum</a>
				</div>
			</div>
		</div>
		
		<div id="pluginpage-options">
			<form method="post" action="options.php"><?php
			settings_fields($this->option_group);
			do_settings_sections($this->page_id);
			submit_button();
			?></form>
		</div>
		
		</div>
		<?php
	}
    
   	public function add_settings_fields() {
		
		register_setting( $this->option_group, $this->option_name, array( $this, 'sanitize_input'));
		
		#settings secion
		add_settings_section($this->section_general,__('Settings',$this->text_domain), array($this,'empty_func') ,$this->settings_page_id);

		add_settings_field('sidebar_slide_duration',__('Menu Slide Duration',$this->text_domain),array( $this, 'text_callback' ),$this->settings_page_id,$this->section_general,
		array('sidebar_slide_duration','Set the duration of slide animation.','ms'));
		
	}
    	
    public function text_callback($args) { $option_id=$args[0]; $description=$args[1]; $units=$args[2];
		$value='';
		if (isset($this->options[$option_id])) $value=esc_attr($this->options[$option_id]);
		if (empty($units)) { $width='500px'; $units=null; }
		else $width='50px';
		echo "<input class=\"text-input\" type=\"text\" id=\"$option_id\" name=\"".$this->option_name."[$option_id]\" style=\"width:$width;\" value=\"$value\" /><p class=\"units\">$units</p><div class=\"clear\"></div>";
		if(!empty($description)) echo "<p class='description'>$description</p>";
	}
    

	
	
	public function sanitize_input($input) {
		
		$new_input = array();
		
		#error messages
		$int_incorrect_value_error=__('allowing values between %d and %d',$this->text_domain);
		$notnum_error=__('input is not a number',$this->text_domain);
		$incorrect_url_error=__('input is not a valid URL',$this->text_domain);
		
		
		#validate integer inputs
		$int_inputs=array(
			'sidebar_slide_duration'=>__('Menu Slide Duration',$this->text_domain),
		);
		foreach ($int_inputs as $field=>$name) { if (isset($input[$field])) {
			$num=$input[$field];
			$error_message='';
			if (!is_numeric($num)) $error_message=$name.': '.$notnum_error;
			else {
				#set limits
				$lower_limit=0;
				$upper_limit=2000;
				if ($field==='sidebar_slide_duration') $upper_limit=1000;

				$num=intval($num);
				if ($num>=$lower_limit && $num<=$upper_limit) $new_input[$field]=$num;
				else $error_message=$name.': '.sprintf($int_incorrect_value_error,$lower_limit,$upper_limit);
			}	
			if(!empty($error_message)) {
				$new_input[$field]=$this->options[$field];
				add_settings_error($field,esc_attr('settings_error'),$error_message,'error');
			}
						
		} }
		
		return $new_input;
	} //endof sanitize_input

	public function empty_func() { }

	private $options;
	private $option_group='admin-menu-slide';
	private $page_id='admin-menu-slide';
	private $section_general='admin-menu-slide-settings';

	private $option_name='admin-menu-slide-settings';
	private $info_name='admin-menu-slide-info';

	private $plugin_url;
	
	private $text_domain='admin-menu-slide';

	private $settings_page_id='admin-menu-slide';




}

$admin_menu_slide_settings= new AdminMenuSlide_Settings();

?>

