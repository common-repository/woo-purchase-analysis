<?php
/*Plugin Name:WooCommerce Purchase Analysis
	Plugin URI: https://acespritech.com/services/wordpress-extensions/
	Description: Purchase analysis for customers
	Author: Acespritech Solutions Pvt. Ltd.
	Author URI: https://acespritech.com/
	Version: 1.1.0
	Domain Path: /languages/
	Requires WooCommerce: 4.9
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) 
{
  add_action('init', 'acemp_mypurchase_endpoint');
  add_action('wp_enqueue_scripts', 'acemp_script');
  add_action('admin_enqueue_scripts', 'acemp_script_admin');
  add_filter('woocommerce_account_menu_items', 'acemp_account_menu_items', 10, 1);
}
else{ 
    deactivate_plugins(plugin_basename(__FILE__));
    add_action( 'admin_notices', 'acemp_woocommerce_not_installed_mypurchase' );
}

 if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
// Ignores notices and reports all other kinds... and warnings
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
// error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}

function acemp_woocommerce_not_installed_mypurchase()
{
    ?>
    <div class="error notice">
      <p><?php _e( 'You need to install and activate WooCommerce to use WooCommerce Mypurchase!', 'WooCommerce-Mypurchase' ); ?></p>
    </div>
    <?php
}
function acemp_mypurchase_endpoint()
{
  add_rewrite_endpoint('mypurchase', EP_PAGES);
  add_rewrite_endpoint('mypurchase_filter', EP_PAGES);
}
function acemp_script_admin(){
	//wp_enqueue_style('myPurchase_admin_boostrap', plugins_url('/css/bootstrap.min.css', __FILE__))	;
	//wp_enqueue_style('myPurchase_admin_style', plugins_url('/css/dashboard.css', __FILE__))	;
}
function acemp_script()
{
    global $post;
    global $wp;
    $current_url = home_url( $wp->request );
    $mypurchase_url = get_permalink( get_option('woocommerce_myaccount_page_id')).'mypurchase';
    $mypurchase_filter = get_permalink( get_option('woocommerce_myaccount_page_id')).'mypurchase_filter';
    if($current_url == $mypurchase_url || $current_url == $mypurchase_filter)
    {
      wp_enqueue_script('myPurchase_script', plugins_url('/js/myPurchase.js', __FILE__), array('jquery'));
      wp_enqueue_script('myPurchase_script2', plugins_url('/js/jquery.easyPaginate.js', __FILE__), array('jquery'));
      wp_enqueue_style('mypurchase_style', plugins_url('/css/myPurchase.css', __FILE__));
      wp_enqueue_script('myPurchase_script1', plugins_url('/js/bootstrap.min.js', __FILE__), array('jquery')); 
      wp_enqueue_script('myPurchase_chart', plugins_url('/js/chart.js', __FILE__), array('jquery')); 
    }
    if($current_url == $mypurchase_filter){
      wp_enqueue_style('mypurchase_filter_style', plugins_url('/css/mypurchase-filter.css', __FILE__));
    }
}

function acemp_account_menu_items($items)
{
    $my_items = array('mypurchase' => __('My Purchase', 'purchase'),);
    $my_items = array_slice($items, 1, true) + $my_items + array_slice($items, 1, count($items), true);
    return $my_items;
}
add_action('woocommerce_account_mypurchase_endpoint', 'acemp_frontend_content');
include('include/mypurchase.php');
add_action('woocommerce_account_mypurchase_filter_endpoint', 'acemp_filter_frontend_content');
include('include/mypurchase-filter.php');

add_action('wp_ajax_acemp_select_nothing', 'acemp_select_nothing');
add_action('wp_ajax_nopriv_acemp_select_nothing', 'acemp_select_nothing');

add_action('wp_ajax_acemp_select_category_based_year', 'acemp_select_category_based_year');
add_action('wp_ajax_nopriv_acemp_select_category_based_year', 'acemp_select_category_based_year');

add_action('wp_ajax_acemp_select_year_only', 'acemp_select_year_only');
add_action('wp_ajax_nopriv_acemp_select_year_only', 'acemp_select_year_only');

add_action('wp_ajax_acemp_product_content', 'acemp_product_content');
add_action('wp_ajax_nopriv_acemp_product_content', 'acemp_product_content');

add_action('wp_ajax_acemp_all_category', 'acemp_all_category');
add_action('wp_ajax_nopriv_acemp_all_category', 'acemp_all_category');

include('include/filter-function.php');

add_filter('the_title', 'acemp_mypurchase_endpoint_title');
function acemp_mypurchase_endpoint_title($title)
{
    global $wp_query;
    $is_endpoint = isset($wp_query->query_vars['giftproduct']);
    if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) 
    {
        $title = __('My Purcase', 'woocommerce');
    }
    return $title;
}

function acemp_mypurchase_plugin_path() {
  return untrailingslashit( plugin_dir_path( __FILE__ ) );
}






