<?php
/*
Plugin Name: Transient Demo
Plugin URI: https://jubel-ahemd.xyz
Description: Demonstration of Transient API
Version: 1.0.0
Author: Jubel Ahmed
Author URI: https://jubel-ahmed.xyz
License: GPLv2 or later
Text Domain: transient
Domain Path: /languages/
 */

add_action('admin_enqueue_scripts', function ($hook) {
    if('toplevel_page_transient-demo' == $hook){
        wp_enqueue_style('pure-grid-css', '//unpkg.com/purecss@1.0.1/build/grids-min.css');
        wp_enqueue_style('transient-demo-css', plugin_dir_url(__FILE__) . "assets/admin/css/style.css", null, time() );
        wp_enqueue_script('transient-demo-js', plugin_dir_url(__FILE__) . "assets/admin/js/main.js", array('jquery'), time(), true);
        $nonce = wp_create_nonce('transient_display_result');
        wp_localize_script(
            'transient-demo-js',
            'plugin_data',
            array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => $nonce)
        );
    }
    
});

add_action('wp_ajax_transient_display_result', function(){
    global $transient;
    $table_name = $transient->prefix . 'peoples';
    if(wp_verify_nonce($_POST['nonce'], 'transient_display_result') ){
        $task = $_POST['task'];

        if ('add-transient'== $task) {
            
          $key = 'tr-country';
          $value = 'Bangladesh';
         // echo "Result = {$result} <br> ";
         echo "Result = ". set_transient($key, $value ) . "<br>";

         $key2 = 'tr-capital';
         $value2 = 'tr-Dhaka';
         echo "Result = ". set_transient($key2, $value2) . "<br>";
        }elseif ('set-expiry' == $task) {
        $key = 'tr-continent';
        $value = 'Asia';
        $expiry = 60*60;
        echo "Result". set_transient($key, $value, $expiry);
        }elseif ('get-transient'== $task) {
            $key1 = 'tr-country';
            $key2 = 'tr-continent';
            echo "Result 1 = ". get_transient($key1). "<br>";
            echo "Result 2 = " . get_transient($key2)."<br>";
        }elseif ('importance'==$task) {
            $key = 'tr-tempareture-sylhet';
            $value = 0;
            $expiry = 12*60;
            set_transient($key, $value, $expiry);
            $result = get_transient($key);
            if($result === false){
                echo "Sylhet's data was not found";
            }else {
                echo "Today's Tempareture in Sylhet is {$result} Degree";
            }
        }elseif ('add-complex-transient' == $task) {
           global $wpdb;
            $result = $wpdb->get_results("SELECT post_title FROM wp_posts ORDER BY id DESC LIMIT 10", ARRAY_A);
            $key = "tr-latest-post";
            $expiry = 60*60;
            set_transient($key, $result, $expiry);
            $output = get_transient($key);
            print_r($output);
        }elseif ('transient-filter-hook' == $task) {
            
            $key = 'tr-country';
            $result = get_transient($key);
            echo "Result = {$result} <br> ";
        }elseif ('delete-transient' == $task) {
            $key = 'tr-capital';
            echo "Before Delete = ". get_transient($key) ."<br>";
            delete_transient($key);
            echo "After Delete = ". get_transient($key) . "<br>";

        }

    }
    die(0);
    
});

add_filter('pre_transient_tr-country', function($result){
    //return strtoupper($result);
   // return false;
    $country = "bangladesh my love";
    $modified =  strtoupper($country);
    return $modified;
});

add_action('admin_menu', function () {
    add_menu_page('Transient Demo', 'Transient Demo', 'manage_options', 'transient-demo', 'transient_admin_page');
});

function transient_admin_page()
{
?>
    <div class="container" style="padding-top:20px;">
        <h1>Transient Demo</h1>
        <div class="pure-g">
            <div class="pure-u-1-4" style='height:100vh;'>
                <div class="plugin-side-options">
                    <button class="action-button" data-task='add-transient'>Add New transient</button>
                    <button class="action-button" data-task='set-expiry'>Set Expiry</button>
                    <button class="action-button" data-task='get-transient'>Display Transient</button>
                    <button class="action-button" data-task='importance'>Importance of ===</button>
                    <button class="action-button" data-task='add-complex-transient'>Add Complex Transient</button>
                    <button class="action-button" data-task='transient-filter-hook'>Transient Filter Hook</button>
                    <button class="action-button" data-task='delete-transient'>Delete Transient</button>
                </div>
            </div>
            <div class="pure-u-3-4">
                <div class="plugin-demo-content">
                    <h3 class="plugin-result-title">Result</h3>
                    <div id="plugin-demo-result" class="plugin-result"></div>
                </div>
            </div>
        </div>
    </div>
<?php
}

