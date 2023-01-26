<?php

class KM_Responsive_Images_Image
{
  private $width;
  private $height;
  private $a_ratio;
  private $class;
  private $options;
  private $wp_id;
  private $wp_image_url;
  private $wp_image_width;
  private $wp_image_height;
  private $wp_image_a_ratio;
  private $wp_image_alt;


  function __construct($wp_id, $width, $height, $class = null, $arg_options = [])
  {
    $this->width            = $width;
    $this->height           = $height;
    $this->a_ratio          = $width / $height;
    $this->class            = $class;

    // Merge default options with argument options
    $this->options          = array_replace(KM_Responsive_Images_Defaults::$default_options, $arg_options);

    // Get original image attributes
    $this->wp_id               = $wp_id;
    $wp_image_array         = wp_get_attachment_image_src($this->wp_id, 'full');
    $this->wp_image_url     = wp_make_link_relative($wp_image_array[0]);
    $this->wp_image_width   = $wp_image_array[1];
    $this->wp_image_height  = $wp_image_array[2];
    $this->wp_image_a_ratio = $this->wp_image_width / $this->wp_image_height;
    $this->wp_image_alt     = get_post_meta($this->wp_id, '_wp_attachment_image_alt', true);
  }

  public function get_img_tag()
  {
    echo $this->get_img_srcset();
  }

  public function get_img_url($width, $height)
  {
    return 'https://' . get_option('_km_imgix_domain') . $this->wp_image_url . '?fit=min&w=' . round($width) . '&h=' . round($height) . '&auto=format&q=75';
  }

  /**
   * Generate sourceset with correct number of steps.
   * 
   * @return  string srcset formatted for srcset attribute in <img> tag
   */
  public function get_img_srcset()
  {
    $srcset = '';

    // Get dimensions of maximum sized image in srcset (restricted by original image dimensions and max_width option at target aspect ratio).
    [$max_width, $max_height] = $this->get_crop_dimensions([$this->wp_image_width, $this->options['max_width']], [$this->wp_image_height]);

    // Set number of steps. Use less steps if not required. 
    $steps = min($this->options['steps'], round(($max_width - $this->options['min_width']) / $this->options['min_step']) + 1);
    if ($steps < 2) {
      return ''; // If steps is less than 2 srcset isn't needed
    }
    
    $step = ($max_width - $this->options['min_width']) / ($steps - 1); // Pixel difference between generated images

    // Build $srcset
    for ($i = 0; $i < $steps; $i++) {
      $step_width = round($this->options['min_width'] + ($i * $step));
      $step_height = $step_width / $this->a_ratio;
      $srcset .= $this->get_img_url($step_width, $step_height) . ' ' . $step_width . 'w';
      $srcset .= ($i < $steps - 1) ? ', ' : '';
    }
    
    return $srcset;
  }

  /**
   * Chooses smallest width and smallest height to create crop box. Then return dimensions of the biggest image of target aspect ratio that fits into that box.
   * 
   * @param   array $widths  All widths that should be taken into account to restrict final width
   * @param   array $heights All heights that should be taken into account to restrict final height
   * @return  array Dimensions of final crop.
   */
  public function get_crop_dimensions($widths, $heights)
  {
    $crop_width = min($widths);
    $crop_height = min($heights);
    $crop_aratio = $crop_width / $crop_height;

    if ($crop_aratio > $this->a_ratio) {
      $cropped_width = $crop_height * $this->a_ratio;
      $cropped_height = $crop_height;
    } else {
      $cropped_width = $crop_width;
      $cropped_height = $crop_width / $this->a_ratio;
    }

    return [round($cropped_width), round($cropped_height)];
  }
}
