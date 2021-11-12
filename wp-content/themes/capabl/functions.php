<?php

require_once 'theme-options/menus.php';

/**
 * Figure out where the base wordpress install is located
 */
function get_wp_config_path() {
	$base = dirname( __FILE__ );
	$path = false;

	if ( @file_exists( dirname( dirname( $base ) ) . "/wp-config.php" ) ) {
		$path = dirname( dirname( $base ) );
	} elseif ( @file_exists( dirname( dirname( dirname( $base ) ) ) . "/wp-config.php" ) ) {
		$path = dirname( dirname( dirname( $base ) ) );
	} else {
		$path = false;
	}

	if ( $path != false ) {
		$path = str_replace( "\\", "/", $path );
	}

	return $path;
}

/**
 * If we have access to /vendor/autoload.php then initialize it.
 */
if ( file_exists( get_wp_config_path() . '/vendor/autoload.php' ) ) {
	require_once( get_wp_config_path() . '/vendor/autoload.php' );

	Symfony\Component\Debug\ErrorHandler::register();
	Symfony\Component\Debug\ExceptionHandler::register();
}

// Clean up wordpress <head>
remove_action( 'wp_head', 'rsd_link' ); // remove really simple discovery link
remove_action( 'wp_head', 'wp_generator' ); // remove wordpress version
remove_action( 'wp_head', 'feed_links',
	2 ); // remove rss feed links (make sure you add them in yourself if youre using feedblitz or an rss service)
remove_action( 'wp_head', 'feed_links_extra', 3 ); // removes all extra rss feed links
remove_action( 'wp_head', 'index_rel_link' ); // remove link to index page
remove_action( 'wp_head', 'wlwmanifest_link' ); // remove wlwmanifest.xml (needed to support windows live writer)
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // remove random post link
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // remove parent post link
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // remove the next and previous post links
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

/**
 * Theme assets
 */
add_action( 'wp_enqueue_scripts', function () {
	$manifest = json_decode( file_get_contents( 'dist/mix-manifest.json', true ), true );
	wp_enqueue_style( 'theme-css', get_template_directory_uri() . "/dist/" . $manifest['/app.css'], false, null );
	wp_enqueue_script( 'theme-js', get_template_directory_uri() . "/dist/" . $manifest['/app.js'], [ 'jquery' ], null, true );
  if( is_singular( 'product' ) || is_page('instant-quote')) {
    wp_enqueue_script( 'polyfill', 'https://polyfill.io/v3/polyfill.min.js?features=default', array(), '1.0.0', false );
    wp_enqueue_script( 'instantQuoteModule', get_template_directory_uri() . '/js/' . '/instant-quote.js', array(), '', false );
  }
}, 100 );


/**
 * Theme setup
 */
add_action( 'after_setup_theme', function () {
	/**
	 * Enable features from Soil when plugin is activated
	 * @link https://roots.io/plugins/soil/
	 */
	add_theme_support( 'soil-clean-up' );
	add_theme_support( 'soil-jquery-cdn' );
	add_theme_support( 'soil-nav-walker' );
	add_theme_support( 'soil-nice-search' );
	add_theme_support( 'soil-relative-urls' );
	/**
	 * Enable plugins to manage the document title
	 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
	 */
	add_theme_support( 'title-tag' );
	/**
	 * Enable post thumbnails
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );
	/**
	 * Enable HTML5 markup support
	 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
	 */
	add_theme_support( 'html5', [ 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ] );
	/**
	 * Enable selective refresh for widgets in customizer
	 * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
	 */
	// add_theme_support('customize-selective-refresh-widgets');

}, 20 );


add_action( 'rest_api_init', function () {
	$namespace = 'cpl/v1';
	register_rest_route( $namespace, '/path/(?P<url>.*?)', array(
		'methods'  => 'GET',
		'callback' => 'get_post_for_url',
	) );
} );

/**
 * This fixes the wordpress rest-api so we can just lookup pages by their full
 * path (not just their name). This allows us to use React Router.
 *
 * @return WP_Error|WP_REST_Response
 */
function get_post_for_url( $data ) {
	$postId     = url_to_postid( $data['url'] );
	$postType   = get_post_type( $postId );
	$controller = new WP_REST_Posts_Controller( $postType );
	$request    = new WP_REST_Request( 'GET', "/wp/v2/{$postType}s/{$postId}" );
	$request->set_url_params( array( 'id' => $postId ) );

	return $controller->get_item( $request );
}

add_filter( 'body_class', 'add_slug_to_body_class' ); // Add slug to body class

function add_slug_to_body_class( $classes ) {
	global $post;
	if ( is_home() ) {
		$key = array_search( 'blog', $classes );
		if ( $key > - 1 ) {
			unset( $classes[ $key ] );
		}
	} elseif ( is_page() ) {
		$classes[] = sanitize_html_class( $post->post_name );
	} elseif ( is_singular() ) {
		$classes[] = sanitize_html_class( $post->post_name );
	}

	return $classes;
}

function myTruncate( $string, $limit, $break = ".", $pad = "..." ) {
	// return with no change if string is shorter than $limit
	if ( strlen( $string ) <= $limit ) {
		return $string;
	}

	// is $break present between $limit and the end of the string?
	if ( false !== ( $breakpoint = strpos( $string, $break, $limit ) ) ) {
		if ( $breakpoint < strlen( $string ) - 1 ) {
			$string = substr( $string, 0, $breakpoint ) . $pad;
		}
	}

	return $string;
}

function bootstrap_pagination( $wp_query ) {

	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}

	$big = 999999999; // need an unlikely integer

	$pages = paginate_links( array(
		'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format'  => '?paged=%#%',
		'current' => max( 1, get_query_var( 'paged' ) ),
		'total'   => $wp_query->max_num_pages,
		'type'    => 'array',
	) );
	if ( is_array( $pages ) ) {
		$paged = ( get_query_var( 'paged' ) == 0 ) ? 1 : get_query_var( 'paged' );
		echo '<div class="col-12 my-4"><nav class="Page navigation"><ul class="pagination">';
		foreach ( $pages as $page ) {
			echo '<li class="page-item">' . $page . '</li>';
		}
		echo '</ul></nav></div>';
	}
}

// Notification  error if Elementor isn't installed
if ( did_action( 'elementor/loaded' ) ) {
  require_once 'elementor-extensions/elementor-widgets.php';
} else {

  $message = sprintf(
  /* translators: 1: Plugin Name 2: Elementor */
    esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'text-domain' ),
    '<strong>' . esc_html__( 'Capabl Theme', 'text-domain' ) . '</strong>',
    '<strong>' . esc_html__( 'Elementor', 'text-domain' ) . '</strong>'
  );

  printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
}

/**
 * Add Woocommerce Support
 */
add_action( 'after_setup_theme', 'woocommerce_support' );

function woocommerce_support() {
  add_theme_support( 'woocommerce' );
}

/**
 * Remove the order comments field from the cart
 */

add_filter( 'woocommerce_checkout_fields' , 'remove_fields' );

function remove_fields( $fields ) {
  unset($fields['order']['order_comments']);

  return $fields;
}

/**
 * Redirect to checkout page after adding product to cart
 */

add_filter( 'woocommerce_add_to_cart_redirect', 'skip_woo_cart' );

function skip_woo_cart() {
  return wc_get_checkout_url();
}

/**
 * Changes the default text on the 'Add To Cart' buttons
 */

add_filter( 'woocommerce_product_single_add_to_cart_text', 'cw_btntext_cart' );
add_filter( 'woocommerce_product_add_to_cart_text', 'cw_btntext_cart' );
function cw_btntext_cart() {
  return __( 'Continue To Request Transport', 'woocommerce' );
}

/**
 * Removes the breadcrumbs on the product page
 */

remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

/**
 * Add fields to the product
 */

// 1. Show custom input field above Add to Cart

add_action( 'woocommerce_before_add_to_cart_button', 'product_add_ons', 9 );

function product_add_ons() {

  // shipping info
  $origin_zip_code = isset( $_POST['origin_zip_code'] ) ? sanitize_text_field( $_POST['origin_zip_code'] ) : '';
  $destination_zip_code = isset( $_POST['destination_zip_code'] ) ? sanitize_text_field( $_POST['destination_zip_code'] ) : '';
  $desired_shipping_date = isset( $_POST['desired_shipping_date'] ) ? sanitize_text_field( $_POST['desired_shipping_date'] ) : '';

  // vehicle details
  $make = isset( $_POST['make'] ) ? sanitize_text_field( $_POST['make'] ) : '';
  $model = isset( $_POST['model'] ) ? sanitize_text_field( $_POST['model'] ) : '';
  $year = isset( $_POST['year'] ) ? sanitize_text_field( $_POST['year'] ) : '';

  // shipping speed option
  $cost = isset( $_POST['shippingSpeed'] );
  $trailerSelection = isset( $_POST['trailerSelection'] ) ? sanitize_text_field( $_POST['trailerSelection'] ) : '';
  $speedSelection = isset( $_POST['speedSelection'] ) ? sanitize_text_field( $_POST['speedSelection'] ) : '';

  //TODO: add hidden fields to for the shipping speed and trailer option - this will give us the info we need to change the product name

  echo '
  <section id="instant-quote-container">
  
    <div id="shippingInfo">
      <div class="iq-row">
        <div class="input-group-container">
          <label for="origin">Origin Zip Code</label>
          <input id="origin" type="number" name="origin" min="0" value="' . $origin_zip_code . '">
        </div>
        <div class="input-group-container">
          <label for="destination">Destination Zip Code</label>
          <input id="destination" type="number" name="destination" min="0" value="' . $destination_zip_code . '">
        </div>
        <div class="input-group-container">
          <label for="desiredDate">Desired Shipping Date</label>
          <input id="desiredDate" type="date" name="desiredDate" value="' . $desired_shipping_date . '" min="' . date('Y-m-d') . '">
        </div>
      </div>
      <div class="iq-next-container">
        <button type="button" onclick="instantQuoteModule.calculateDistance()" class="capabl-btn__round">Next</button>
      </div>
    </div> <!-- #shippingInfo -->

    <div id="vehicleDetails" style="visibility: hidden;">
      <div class="iq-row">
        <div class="input-group-container">
          <label for="make">Make</label>
          <input id="make" type="text" name="make" placeholder="Make" value="' . $make . '">
        </div>
        <div class="input-group-container">
          <label for="model">Model</label>
          <input id="model" type="text" name="model" placeholder="Model" value="' . $model . '">
        </div>
        <div class="input-group-container">
          <label for="year">Year</label>
          <input id="year" type="text" name="year" placeholder="Year" value="' . $year . '">
        </div>
      </div>
      <div class="iq-row py-3">
          <div class="input-group-container">
              <label for="vehicleType">Vehicle Type</label>
              <select id="vehicleType" name="vehicleType">
                  <option value="" disabled selected>Select vehicle type</option>
                  <option value="car">Car</option>
                  <option value="truck">Truck</option>
                  <option value="van">Van</option>
                  <option value="suv">SUV</option>
              </select>
          </div>
          <div class="input-group-container">
              <label for="operationalStatus">Operational Status</label>
              <select id="operationalStatus" name="operationalStatus">
                  <option value="" disabled selected>Select status</option>
                  <option value="running">Operational</option>
                  <option value="not-running">Non-Operational</option>
              </select>
          </div>
      </div>
      <div class="iq-helper-text py-3">
        <div class="iq-helper-text__inner">
          <p>
            <span class="font-weight-bold d-none">Can\'t find your vehicle?</span> Our instant quote tool is not
              yet configured for certain vehicles including those that would be considered oversized or
              non-operational.
          </p>
          <p>
              Rest assured we can still help! <a href="https://offsettransport.com/contact-us/" 
              target="_self">Simply request a custom quote here.</a>
          </p>
        </div>
        <div class="iq-next-container">
            <button type="button" onclick="instantQuoteModule.captureVehicleDetails()" class="capabl-btn__round">Next</button>
        </div>
      </div>
    </div> <!-- #vehicleDetails -->
    
    <div id="output" style="visibility: hidden;">
      <div class="output-row">
        <p>SHIPPING SPEED - <a href="https://offsettransport.com/faqs/#how-fast-can-you-ship-my-car" target="_blank">LEARN MORE</a></p>
        <h3>Shipping Options</h3>
        <p>Select your preferred shipping option.</p>
        <p class="color-gray-900">We are pleased to offer you the following rates for your vehicle transport. Simply select the tile with your desired options and click continue to request transport. To learn more about the various shipping speed options please <a href="/faqs/#how-fast-can-you-ship-my-car" target="_blank" class="theme-cyan-process">click here</a>.</p>
        <ul class="shipping-speed-options">
            <li>
                <input type="radio" id="openStandard" class="speedOption" name="shippingSpeed" value="" data-speed="Standard" data-type="Open">
                <label for="openStandard" id="openStandardLabel">
                  <div>Open Trailer</div>
                  Standard -
                </label>
            </li>
            <li>
                <input type="radio" id="openFast" class="speedOption" name="shippingSpeed" value="" data-speed="Faster" data-type="Open">
                <label for="openFast" id="openFastLabel">
                  <div>Open Trailer</div>
                  Faster -
                </label>
            </li>
            <li>
                <input type="radio" id="openNitro" class="speedOption" name="shippingSpeed" value="" data-speed="Fastest" data-type="Open">
                <label for="openNitro" id="openNitroLabel">
                  <div>Open Trailer</div>
                  Fastest -
                </label>
            </li>
          </ul>
          <ul class="shipping-speed-options">
            <li>
                <input type="radio" id="closedStandard" class="speedOption" name="shippingSpeed" value="" data-speed="Standard" data-type="Enclosed">
                <label for="closedStandard" id="closedStandardLabel">
                  <div>Enclosed Trailer</div>
                  Standard -
                </label>
            </li>
            <li>
                <input type="radio" id="closedFast" class="speedOption" name="shippingSpeed" value="" data-speed="Faster" data-type="Enclosed">
                <label for="closedFast" id="closedFastLabel">
                  <div>Enclosed Trailer</div>
                  Faster -
                </label>
            </li>
            <li>
                <input type="radio" id="closedNitro" class="speedOption" name="shippingSpeed" value="" data-speed="Fastest" data-type="Enclosed">
                <label for="closedNitro" id="closedNitroLabel">
                  <div>Enclosed Trailer</div>
                  Fastest -
                </label>
            </li>
          </ul>
          <div class="hidden-fields">
            <input id="trailerSelection" type="hidden" name="trailerSelection" value="' . $trailerSelection . '">
            <input id="speedSelection" type="hidden" name="speedSelection" value="' . $speedSelection . '">
          </div>
        <!-- <button type="submit" onclick="instantQuoteModule.submitQuote()" class="capabl-btn__round">Continue to Deposit</button> -->
      </div>
    </div> <!-- #output -->

  </section>';

}

// 2. Throw error if custom input field empty - not necessary because the error handling is handled with js right now

// 3. Save custom input field value into cart item data

add_filter( 'woocommerce_add_cart_item_data', 'product_add_ons_cart_item_data', 10, 2 );

function product_add_ons_cart_item_data( $cart_item, $product_id ) {

  if( isset( $_POST['origin'] ) ) {
    $cart_item['origin'] = sanitize_text_field( $_POST['origin'] );
  }
  if( isset( $_POST['destination'] ) ) {
    $cart_item['destination'] = sanitize_text_field( $_POST['destination'] );
  }
  if( isset( $_POST['desiredDate'] ) ) {
    $cart_item['desiredDate'] = sanitize_text_field( $_POST['desiredDate'] );
  }
  if( isset( $_POST['make'] ) ) {
    $cart_item['make'] = sanitize_text_field( $_POST['make'] );
  }
  if( isset( $_POST['model'] ) ) {
    $cart_item['model'] = sanitize_text_field( $_POST['model'] );
  }
  if( isset( $_POST['year'] ) ) {
    $cart_item['year'] = sanitize_text_field( $_POST['year'] );
  }
  if( isset( $_POST['shippingSpeed'] ) ) {
    $cart_item['cost'] = $_POST['shippingSpeed'];
  }

  if( isset( $_POST['trailerSelection'] ) ) {
    $cart_item['trailerSelection'] = sanitize_text_field( $_POST['trailerSelection'] );
  }

  if( isset( $_POST['speedSelection'] ) ) {
    $cart_item['speedSelection'] = sanitize_text_field( $_POST['speedSelection'] );
  }

  return $cart_item;

}

// 4. Display custom input field value @ Cart

add_filter( 'woocommerce_get_item_data', 'product_add_ons_display_cart', 10, 2 );

function product_add_ons_display_cart( $data, $cart_item ) {

  if ( isset( $cart_item['origin'] ) ) {
    $data[] = array(
      'name' => 'Origin Zip Code',
      'value' => sanitize_text_field( $cart_item['origin'] )
    );
  }

  if ( isset( $cart_item['destination'] ) ) {
    $data[] = array(
      'name' => 'Destination Zip Code',
      'value' => sanitize_text_field( $cart_item['destination'] )
    );
  }

  if ( isset( $cart_item['desiredDate'] ) ) {
    $data[] = array(
      'name' => 'Desired Shipping Date',
      'value' => sanitize_text_field( $cart_item['desiredDate'] )
    );
  }

  if ( isset( $cart_item['make'] ) ) {
    $data[] = array(
      'name' => 'Vehicle Make',
      'value' => sanitize_text_field( $cart_item['make'] )
    );
  }

  if ( isset( $cart_item['model'] ) ) {
    $data[] = array(
      'name' => 'Vehicle Model',
      'value' => sanitize_text_field( $cart_item['model'] )
    );
  }

  if ( isset( $cart_item['year'] ) ) {
    $data[] = array(
      'name' => 'Vehicle Year',
      'value' => sanitize_text_field( $cart_item['year'] )
    );
  }

  return $data;

}

// 5. Save custom input field value into order item meta

add_action( 'woocommerce_add_order_item_meta', 'product_add_ons_order_item_meta', 10, 2 );

function product_add_ons_order_item_meta( $item_id, $values ) {

  if ( ! empty( $values['origin'] ) ) {
    wc_add_order_item_meta( $item_id, 'Origin Zip Code', $values['origin'], true );
  }
  if ( ! empty( $values['destination'] ) ) {
    wc_add_order_item_meta( $item_id, 'Destination Zip Code', $values['destination'], true );
  }
  if ( ! empty( $values['desiredDate'] ) ) {
    wc_add_order_item_meta( $item_id, 'Desired Shipping Date', $values['desiredDate'], true );
  }
  if ( ! empty( $values['make'] ) ) {
    wc_add_order_item_meta( $item_id, 'Vehicle Make', $values['make'], true );
  }
  if ( ! empty( $values['model'] ) ) {
    wc_add_order_item_meta( $item_id, 'Vehicle Model', $values['model'], true );
  }
  if ( ! empty( $values['year'] ) ) {
    wc_add_order_item_meta( $item_id, 'Vehicle Year', $values['year'], true );
  }
  if ( ! empty( $values['cost'] ) ) {
    wc_add_order_item_meta( $item_id, 'Your Quote', $values['cost'], true );
  }

}

// 6. Display custom input field value into order table

add_filter( 'woocommerce_order_item_product', 'product_add_ons_display_order', 10, 2 );

function product_add_ons_display_order( $cart_item, $order_item ) {

  if( isset( $order_item['origin'] ) ){
    $cart_item['origin'] = $order_item['origin'];
  }
  if( isset( $order_item['destination'] ) ){
    $cart_item['destination'] = $order_item['destination'];
  }
  if( isset( $order_item['desiredDate'] ) ){
    $cart_item['desiredDate'] = $order_item['desiredDate'];
  }
  if( isset( $order_item['make'] ) ){
    $cart_item['make'] = $order_item['make'];
  }
  if( isset( $order_item['model'] ) ){
    $cart_item['model'] = $order_item['model'];
  }
  if( isset( $order_item['year'] ) ){
    $cart_item['year'] = $order_item['year'];
  }

  return $cart_item;

}

// 7. Display custom input field value into order emails

add_filter( 'woocommerce_email_order_meta_fields', 'product_add_ons_display_emails' );

function product_add_ons_display_emails( $fields ) {

  $fields['origin'] = 'Origin Zip Code';
  $fields['destination'] = 'Destination Zip Code';
  $fields['desiredDate'] = 'Desired Shipping Date';
  $fields['make'] = 'Vehicle Make';
  $fields['model'] = 'Vehicle Model';
  $fields['year'] = 'Vehicle Year';

  return $fields;

}

// updates the cart price to the calculated quote price
add_action( 'woocommerce_before_calculate_totals', 'calculate_quote', 99, 1 );
function calculate_quote( $cart ) {
  if ( is_admin() && ! defined( 'DOING_AJAX' ) )
    return;

  if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
    return;

  foreach ( $cart->get_cart() as $cart_item ) {
    if( isset( $cart_item['cost'] ) ) {
      $new_price = (float) $cart_item['cost'];
      $cart_item['data']->set_price( $new_price );
      $cart_item['data']->set_name( $cart_item['trailerSelection'].' Trailer '.$cart_item['speedSelection'] );
    }
  }
}

/**
 *  Custom Added To Your Cart Message
 */

add_filter( 'wc_add_to_cart_message_html', 'instant_quote_added_to_cart_message' );

function instant_quote_added_to_cart_message() {

  $message = 'The quote you have selected is an estimated cost of transportation based on the information you have provided to us. While we pride ourselves on the accuracy of our quotes, moving forward does not constitute a guarantee to transport your vehicle. As such, the form below serves only to “authorize” your credit card for the quoted amount. Funds will not be “captured” until transportation has been secured. Rest assured that we will be in communication with you throughout this process.' ;

  return $message;

}

/**
 * Custom Thank You Message
 */

add_filter( 'woocommerce_thankyou_order_received_text', 'instant_quote_thank_you_message' );

function instant_quote_thank_you_message() {

  $thank_you_msg =  'Thank you. Your request has been received and we will be touch shortly as we begin the process of securing transport!';

  return $thank_you_msg;
}