<?php
/**
 * Abstract class to help create new Admin Panels
 *
 * @package Dip Framework
 * @subpackage Module Helpers
 * @since 1.0.0
 */

abstract class DP_Panel
{
  public $name;
  public $namespace;
  public $module;
  public $settings;

  public $parent      = 'themes.php';
  public $capability  = 'edit_theme_options';
  public $icon        = 'options';
  public $position    = '100';

  public $tabs        = false;
  public $current_tab;

  /**
   * Call init method and create new Panel
   * @return DP_Panel self-object
   */
  public function __construct()
  {
    /** don't run outside of wp-admin */
    if(!is_admin()) return;

    /** call extend class init */
    $this->init();
    
    /** fill necessary attributes if is null */
    if(is_null($this->module)) $this->module = $this->namespace;
    if($this->tabs && is_null($this->current_tab)) $this->current_tab = isset($_GET['tab']) ? $_GET['tab'] : current(array_keys($this->tabs));
    
    /** customize wp-admin icons for the new panel */
    global $dip;
    $dip->ui->set_icon32($this->namespace, $this->icon);
    if($this->parent==false) $dip->ui->set_menu_icon('toplevel_page_'.$this->module, $this->icon);

    /** only if is a form submit */
    if (!empty($_POST) && $_POST['option_page'] == $this->namespace)
    {
      /** call abstract method to save */
      $this->save_settings();

      /* redirect to original tab, if was setted */
      wp_redirect($this->get_panel_uri($this->current_tab, '&updated=true'));
      exit;
    }

    /** load saved settings */
    $this->settings = get_option($this->namespace);

    /** call WordPress hooks */
    add_action('admin_menu', array(&$this, 'register_panel'));
    add_action('admin_enqueue_scripts', array(&$this, 'enqueue_scripts'));
  }

  /**
   * Abstract method to configure the new Panel
   * @return void
   */
  abstract public function init();

  /**
   * Hooked method to register the new Panel
   * @return void
   */
  public function register_panel()
  {
   // add_menu_page( 'custom menu title', 'custom menu', 'manage_options', 'myplugin/myplugin-admin.php', '', , 6 );
    /** register panel in admin menu */
    if($this->parent == false) {
      add_menu_page($this->name, $this->name, $this->capability, $this->namespace, array($this, 'panel_view'), '', $this->position);
    } else {
      add_submenu_page($this->parent, $this->name, $this->name, $this->capability, $this->namespace, array(&$this, 'panel_view'));
    }

    add_action('admin_init', array(&$this, 'register_setting'));
  }

  /**
   * Hooked method to register the Panel settings
   * @return void
   */
  public function register_setting()
  {
    register_setting($this->namespace, $this->settings);
  }

  /**
   * Empty methods to save registered settings and register scripts
   * @return void
   */
  public function save_settings() {}
  public function enqueue_scripts() {}

  /**
   * Hooked method to render the panel view
   * @param string $tab
   * @return string
   */
  public function get_panel_uri($tab = null, $sufix = null)
  {
    $separator = strpos($this->parent, '?') ? '&' : '?';
    $tab_query = !is_null($tab) ? "&tab={$tab}" : '';
    return admin_url("{$this->parent}{$separator}page={$this->namespace}{$tab_query}{$sufix}");
  }
  
  /**
   * Hooked method to render the panel view
   * @return void
   */
  public function panel_view()
  {
    /** open container */
    echo '<div class="wrap">';

    /** has tabs or sigle page */
    $this->panel_header();

    /** include file view or show the error */
    $filename = $this->get_panel_view_filename();
    if((include $filename) === false) 
    {
      printf('<p>'.__('You need create the panel view!'.'</p>', 'dip'));
      return;
    }
    
    /** close container */
    echo '</div>';
  }

  /**
   * Get a valid filname view
   * @return string $filename
   */
  public function get_panel_view_filename()
  {
    /** set template view order */
    if($this->tabs)
    {
      $paths = array(
        "panels/{$this->module}/{$this->current_tab}.phtml",
        "panels/{$this->module}/{$this->namespace}/{$this->current_tab}.phtml",
        "modules/{$this->module}/{$this->current_tab}.phtml",
        "modules/{$this->module}/panels/{$this->current_tab}.phtml",
        "modules/{$this->module}/panels/{$this->namespace}/{$this->current_tab}.phtml"
      );
    }
    else
    {
      $paths = array(
        "panels/{$this->namespace}.phtml",
        "panels/{$this->module}/{$this->namespace}.phtml",
        "modules/{$this->module}/{$this->namespace}.phtml",
        "modules/{$this->module}/panels/{$this->namespace}.phtml"        
      );
    }
    
    /** search a valid panel view */
    foreach($paths as $filename) {
      if(stream_resolve_include_path($filename))
        return $filename;
    }
  }

  /**
   * Print panel header: tabs or single title
   * @return void
   */
  public function panel_header()
  {    
    echo '<div id="icon-'.$this->namespace.'" class="icon32 icon-generic"><br /></div>';
    if($this->tabs) {
      echo '<h2 class="nav-tab-wrapper">';
      foreach ($this->tabs as $key => $label)
      {
        $class = ($key == $this->current_tab) ? ' nav-tab-active' : '';
        printf('<a class="nav-tab%s" href="%s">%s</a>', $class, $this->get_panel_uri($key), $label);
      }
      echo '</h2>';
    } else {
      echo '<h2>'.$this->name.'</h2>';
    }
  }  
}