<?php
/**
 * Class to load the framework and modules
 *
 * @package Dip Framework
 * @subpackage Core
 * @since Dip Framework 1.0
 */

class DP_Bootstrap
{
  public $theme;
  public $config;
  public $modules;
  public $ui;

  function __construct()
  {
    global $loader;

    $this->config  = $loader['config'];
    $this->modules = array_replace(array('foundation'=>true), $loader['modules']); 

    /** get all theme info */
    $this->theme = wp_get_theme();
    $this->theme->stylesheet_uri           = get_stylesheet_uri();
    $this->theme->stylesheet_directory_uri = get_stylesheet_directory_uri();
    $this->theme->template_directory_uri   = get_template_directory_uri();

    $this->_include_libs();

    if(!is_admin()) return;
    $this->ui = new DP_UserInterface();
    set_error_handler(array(&$this->ui, 'admin_alert_errors'), E_ERROR ^ E_CORE_ERROR ^ E_COMPILE_ERROR ^ E_USER_ERROR ^ E_RECOVERABLE_ERROR ^ E_CORE_WARNING ^ E_COMPILE_WARNING ^ E_USER_WARNING ^ E_USER_NOTICE ^ E_DEPRECATED ^ E_USER_DEPRECATED ^ E_PARSE);
  }
  
  public function start()
  {
    $this->_init_supports();
    $this->_load_modules();
    $this->_call_hooks();
    $this->_load_scripts();
  }

  protected function _include_libs()
  {
    // Libraries
    require_once('vendor/simplehtmldom.php');

    // Module classes
    require_once('post-type.php');
    require_once('taxonomy.php');

    // 
    if(is_admin()) {
      require_once('form.php');
      require_once('panel.php');
      require_once('ui.php');
    } else {
      require_once('helpers/template-tags.php');
    } 
  }

  protected function _call_hooks()
  {
    /** wp-admin hooks */
    if(!is_admin()) return;
    add_action('admin_print_styles', array(&$this->ui, 'apply'));
  }

  protected function _init_supports()
  {
    // Remove admin bar
    if($this->config['adminbar'] == false)
      add_filter('show_admin_bar', '__return_false');  
    
    // Register menus
    if(is_array($this->config['menus']))
      register_nav_menus($this->config['menus']);
    
    // Register sidebars
    if(is_array($this->config['sidebars'])) {
      foreach ($this->config['sidebars'] as $id=>$args) {
        if(is_int($id)) $id = $args;
        $defaults = array(
          'name'          => ucwords(str_replace('-', ' ', $id)),
          'id'            => $id
        );
        
        $args = array_merge($defaults, (array)$args);
        register_sidebar($args);
      }  
    }  
  }

  protected function _load_modules()
  {
    if(!is_array($this->modules)) return;
    foreach($this->modules as $module=>$args)
    {
      if($args === true)
      {
        $filename = stream_resolve_include_path("modules/{$module}.php") ? "modules/{$module}.php" : "modules/{$module}/init.php";
        include_once($filename);
      }
    }
  }

  protected function _load_scripts()
  {
    if(is_admin() || is_login_page()) return;
    wp_register_style('dip', $this->theme->stylesheet_uri, false, $this->theme->version);

    if(is_child_theme() && file_exists($this->theme->stylesheet_directory_uri.'/script.js'))
      wp_register_script('dip', $this->theme->stylesheet_directory_uri.'/script.js', false, $this->theme->version, true);
    else
      wp_register_script('dip', $this->theme->template_directory_uri.'/script.js', false, $this->theme->parent()->version, true);

    wp_enqueue_style('dip');
    wp_enqueue_script('dip');
  }
}