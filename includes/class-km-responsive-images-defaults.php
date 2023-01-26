<?php

class KM_Responsive_Images_Defaults {
  // Plugin defaults
  public static $plugin_default_options = [
    'min_width' => 420,
    'max_width' => 3000,
    'steps'  		=> 5,
    'min_step'  => 200,
  ];
  // User defaults (set on options page) merged with plugin defaults
  public static $default_options = [];


  public function __construct() {
    self::set_default_options(); // User defaults are set in admin and stored in wp_options table
  }


  public function set_default_options()
  {
    // If option is set in wp_options then assign otherwise use base default value
    $min_width = get_option('_km_min_width') ?: self::$plugin_default_options['min-width'];
    $max_width = get_option('_km_max_width') ?: self::$plugin_default_options['max-width'];
    $steps = get_option('_km_steps')				 ?: self::$plugin_default_options['steps'];
    $min_step = get_option('_km_min_step')	 ?: self::$plugin_default_options['min-step'];

    self::$default_options = [
      'min_width' => +$min_width,
      'max_width' => +$max_width,
      'steps' 		=> +$steps,
      'min_step' 	=> +$min_step,
    ];
  }
}