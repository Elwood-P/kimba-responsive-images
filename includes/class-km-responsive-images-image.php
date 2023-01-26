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
  private $wp_image_alt;


  function __construct($wp_id, $width, $height, $class = null, $arg_options = [])
  {
    $this->width            = $width;
    $this->height           = $height;
    $this->a_ratio          = $width / $height;
    $this->class            = $class;
    $this->options          = array_replace(KM_Responsive_Images_Defaults::$default_options, $arg_options); // Merge default options with argument options

    // Original wp image attributes
    $this->wp_id            = $wp_id;
    $wp_image_array         = wp_get_attachment_image_src($this->wp_id, 'full');
    $this->wp_image_url     = wp_make_link_relative($wp_image_array[0]);
    $this->wp_image_width   = $wp_image_array[1];
    $this->wp_image_height  = $wp_image_array[2];
    $this->wp_image_alt     = get_post_meta($this->wp_id, '_wp_attachment_image_alt', true);
  }


  public function get_img_tag()
  {
    $img_tag =
      '<img ' .
      'class="'       . $this->class . ' lazyload" ' .
      'width="'       . $this->width . '" ' .
      'height="'      . $this->height . '" ' .
      'src="'         . $this->get_img_url($this->width, $this->height) . '" ' .
      'srcset="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="' .
      'data-sizes="'       . 'auto' . '" ' .
      'data-srcset="'      . $this->get_img_srcset() . '" ' .
      'alt="'         . $this->wp_image_alt . '" ' .
      '/>';

    return $img_tag;
  }


  public function get_img_url($width, $height)
  {
    return 'https://' . get_option('_km_imgix_domain') . $this->wp_image_url . '?fit=min&w=' . round($width) . '&h=' . round($height) . '&auto=format&q=75';
  }


  private function get_img_srcset()
  {
    // TODO
    // [ ] What if original image size is smaller than min-size -> then return? srcset won't do anything right?
    // [x] Lazy sizes
    // [ ] Thumbnail option
    // [ ] Wordpress scaled images
    // [ ] Lazysizes plugins
    // [ ] Option for no js
    // [ ] img[data-sizes="auto"] { display: block; width: 100%; }

    $srcset = '';
    [$max_width, $max_height] = $this->get_crop_dimensions([$this->wp_image_width, $this->options['max_width']], [$this->wp_image_height]); // Get dimensions of maximum sized image in srcset (restricted by original image dimensions and max_width option at target aspect ratio).
    $steps = min($this->options['steps'], round(($max_width - $this->options['min_width']) / $this->options['min_step']) + 1); // Set number of steps. Use less steps if step will be less than min_step set in options.
    $step = ($max_width - $this->options['min_width']) / ($steps - 1); // $step is the pixel difference between srcset images

    // Build $srcset
    for ($i = 0; $i < $steps; $i++) {
      $step_width = round($this->options['min_width'] + ($i * $step));
      $step_height = $step_width / $this->a_ratio;
      $srcset .= $this->get_img_url($step_width, $step_height) . ' ' . $step_width . 'w, ';
      $srcset .= ($i < $steps - 1) ? '' : $this->get_img_url($this->width, $this->height) . ' ' . $this->width . 'w'; // Add original src dimensions to end of the srcset
    }

    return $srcset;
  }


  /**
   * Chooses smallest width and smallest height to create crop box. Then return dimensions of the biggest image of target aspect ratio that fits into that box.
   * 
   * @param array $widths  All widths that should be taken into account to restrict final width.
   * @param array $heights All heights that should be taken into account to restrict final height.
   * @return array Dimensions of final crop.
   */
  public function get_crop_dimensions($widths, $heights)
  {
    $crop_width = min($widths);
    $crop_height = min($heights);
    $crop_a_ratio = $crop_width / $crop_height;

    if ($crop_a_ratio > $this->a_ratio) {
      $cropped_width = $crop_height * $this->a_ratio;
      $cropped_height = $crop_height;
    } else {
      $cropped_width = $crop_width;
      $cropped_height = $crop_width / $this->a_ratio;
    }

    return [round($cropped_width), round($cropped_height)];
  }
}
