# Wordpress Images on Demand Plugin

Images on Demand is a WordPress plugin that provides an alternative way of serving scaled images by integrating with an image CDN. It replaces both the native WordPress thumbnail and responsive images functionality. This helps to save server space and improve page load speed compared to the default WordPress approach.

### Responsive Images

- Generates an image `srcset` based on the dimensions of the original image, using a customisable algorithm to determine the dimensions for each image in the `srcset`.
- Generates a `sizes` attribute (using [lazysizes](https://github.com/aFarkas/lazysizes)) to help the browser select an appropriate image size to download.
- Integrates with your image CDN to generate and serve the scaled images.

### Thumbnails

- Prevents the generation of default Wordpress thumbnails.
- Replaces WordPress thumbnails with scaled image from CDN.

Demo site

## Todo

- [ ]  Prevent the generation of default Wordpress thumbnails.
- [ ]  Use `post_thumbnail_html` filter to replace WP thumbnails with scaled image from CDN.
- [ ]  Documentation