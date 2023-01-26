<?php

function kmri_image($id, $width, $height, $class = null, $options = [])
{
  $img = new KM_Responsive_Images_Image($id, $width, $height, $class, $options);
  echo $img->get_img_tag();
}

function kmri_image_url($id, $width, $height, $class = null, $options = [])
{
  $img = new KM_Responsive_Images_Image($id, $width, $height, $class, $options);
  [$crop_width, $crop_height] = $img->get_crop_dimensions([$img->wp_image_width, $img->options['max_width'], $width], [$img->wp_image_height], $height); // Stop image from upscaling - if target width/height is too big return cropped dimensions that fit the original wp image
  echo $img->get_img_url($crop_width, $crop_height);
}
