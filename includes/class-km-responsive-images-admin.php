<?

class KM_Responsive_Images_Admin
{
  function __construct()
  {
    add_action('carbon_fields_register_fields', array($this, 'plugin_options_page'));
    add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));
  }


  public function register_admin_scripts($post)
  {
    wp_enqueue_style('km-responsive-image-admin', KM_RESPONSIVE_IMAGES_URL . 'assets/css/admin.css', array('carbon-fields-metaboxes'), KM_RESPONSIVE_IMAGES_VERSION);
  }

  
  public function plugin_options_page()
  {
    $defaults = KM_Responsive_Images_defaults::$plugin_default_options;

    Carbon_Fields\Container::make('theme_options', 'Kimba Responsive Images')
      ->set_page_parent('options-general.php') // Place under settings menu
      ->add_fields(array(
        // Section Heading - Image CDN Settings
        Carbon_Fields\Field::make('html', 'km_section_heading_1')
          ->set_html('<h3>Image CDN Settings</h3>'),
        // imgIX Domain
        Carbon_Fields\Field::make('text', 'km_imgix_domain', 'imgIX Domain')
          ->set_attribute('type', 'text')
          ->set_help_text('As found in imgIX dashboard, e.g. "myproject.imgix.net".'),

        // Section Heading - Image CDN Settings
        Carbon_Fields\Field::make('html', 'km_section_heading_2')
          ->set_html('<h3>Defaults</h3>'),
        // Min Width
        Carbon_Fields\Field::make('text', 'km_min_width', 'Min Width (px)')
          ->set_default_value($defaults['min_width'])
          ->set_attribute('type', 'number')
          ->set_help_text('The  width of the smallest image in your srcset (default: ' . $defaults['min_width'] . 'px). If the original source image is less than the default that width will be used instead.'),
        // Max Width
        Carbon_Fields\Field::make('text', 'km_max_width', 'Max Width (px)')
          ->set_default_value($defaults['max_width'])
          ->set_attribute('type', 'number')
          ->set_help_text('The  width of the largest image in your srcset (default: ' . $defaults['max_width'] . 'px). If the original source image is less than the default that width will be used instead.'),
        // Steps
        Carbon_Fields\Field::make('text', 'km_steps', 'Steps')
          ->set_default_value($defaults['steps'])
          ->set_attribute('type', 'number')
          ->set_help_text('Number of images to be generated in the srcset (default: ' . $defaults['steps'] . '). The number of images could also be limited by Min Steps setting.'),
        // Min Step
        Carbon_Fields\Field::make('text', 'km_min_step', 'Min Step (px)')
          ->set_default_value($defaults['min_step'])
          ->set_attribute('type', 'number')
          ->set_help_text('Minimum number of pixels between consecutive images in srcset (default: ' . $defaults['min_step'] . 'px). Used to limit unnecessary images being generated in the case where Min Width and Max Width are close.'),

        // Submmit button replacement
        Carbon_Fields\Field::make('html', 'km_submit_btn')
          ->set_html('<input type="submit" value="Save Changes" name="publish" id="publish" class="button button-primary button-large">')
      ));
  }
}
