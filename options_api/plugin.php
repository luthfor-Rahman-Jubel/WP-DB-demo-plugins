<?php
/*
Plugin Name: Options Demo
Plugin URI: https://jubel-ahemd.xyz
Description: Demonstration of Option API
Version: 1.0.0
Author: Jubel Ahmed
Author URI: https://jubel-ahmed.xyz
License: GPLv2 or later
Text Domain: wpdb
Domain Path: /languages/
 */


 add_action('admin_enqueue_scripts', function($hook){
     if($hook == 'toplevel_page_options-demo'){
        wp_enqueue_style('pure-grid-css', '//unpkg.com/purecss@1.0.1/build/grids-min.css');
        wp_enqueue_style('option-demo-style', plugin_dir_url(__FILE__) . "assets/admin/css/style.css", time() );
        wp_enqueue_script('options-demo-js', plugin_dir_url(__FILE__) . "assets/admin/js/main.js", array('jquery'), time(), true);
       $nonce = wp_create_nonce('options_display_result');
       wp_localize_script(
           'options-demo-js', 
           'plugin_data', 
            array('ajax_url' => admin_url('admin-ajax.php'), 'nonce'=>$nonce )
       );
     }
    });


     add_action('wp_ajax_options_display_result', function(){
        global $options;
        $table_name = $options->prefix . 'peoples';
        if(wp_verify_nonce($_POST['nonce'], 'options_display_result') ){
            $task = $_POST['task'];
            if('add-option' == $task){
                $key = 'od-country';
                $value = 'Bangladesh';
                echo "Result = ". add_option($key, $value) . "<br>";

                $key = 'od-country';
                $value = 'Bangladesh';
                echo "Result = ". add_option($key, $value) . "<br>";

            }elseif ('add-array-option' == $task) {
               $key = 'odd-array';
               $value = array('country'=>'Bangladesh','capital'=>'Dhaka');
               echo "Result = ". add_option($key, $value). "<br>";

               $key = 'odd-array-json';
               $value = json_encode(array('country'=>'Bangladesh','capital'=>'Dhaka') );
               echo "Result = ". add_option($key, $value). "<br>";


            }elseif ('get-option' == $task) {
                $key = 'od-country';
                $result = get_option($key);
                echo "Result = ". $result ."<br>";
            }elseif ('get-array-option' == $task) {
               $key = 'odd-array';
               $result = get_option($key);
               print_r($result);

               $key = 'odd-array-json';
               $result = json_decode(get_option($key) );
               print_r($result);
            }elseif ('option-filter-hook' == $task) {
                $key = 'od-country';

                $result = get_option($key);
                echo "Result = ". $result . "<br>";

                $json_key = 'odd-array-json';
                $result = get_option($json_key);
                print_r($result);
            }elseif ('update-option' == $task) {
                $key = 'od-capital';
                $value = 'Dhaka';
                echo "Result = ". update_option($key, $value) . "<br>";
            }elseif ('update-array-option' == $task) {
                $key = 'od-new-array';
                $value = array('country'=>'pakistan','capital'=>'Islamabad');
                echo "Result = ". update_option($key, $value) . "<br>";

                $new_value = array('Country'=>'Afghanistan','capital'=>'Kabul');
                echo "Result = ". update_option($key, $new_value) . "<br>";
            }elseif ('delete-option' == $task) {
                $key = 'test-key';
                echo "Result = ". delete_option($key) . "<br>";
            }elseif ('export-option' == $task) {
                $key_normal = array('od-country','od-capital');
                $key_array = array('odd-array','od-new-array');
                $key_json = array('odd-array-json');

                $exported_data = array();

                foreach ($key_normal as $key) {
                    $value = get_option($key);
                    $exported_data[$key] = $value;
                }
                foreach ($key_array as $key ) {
                   $value = get_option($key);
                   $exported_data[$key] = $value;
                }

                foreach ($key_json as $key ) {
                $value = json_decode(get_option($key), true);
                $exported_data[$key] = $value;
                }
                echo json_encode($exported_data);
            }elseif ('import-option' == $task) {
                $import_data = '{"od-country":"Bangladesh","od-capital":"Dhaka","odd-array":{"country":"Bangladesh","capital":"Dhaka"},"od-new-array":{"Country":"Afghanistan","capital":"Kabul"},"odd-array-json":{"country":"Bangladesh","capital":"Dhaka"}}';
                $array_data = json_decode($import_data, true);
                print_r($array_data);
                foreach ($array_data as $key => $value) {
                    update_option($key, $value );
                }
            }

        }
        die(0);
     } );

     add_filter('option_od-country', function($value){
        return strtoupper($value)." MY LOVE";
     });
     
     add_action('admin_menu', function(){
         add_menu_page( 'Options Demo', 'Options Demo', 'manage_options', 'options-demo', 'options_demo_admin_page');
     } );

     function options_demo_admin_page(){
         ?>
      <div class="container" style="padding-top:20px;">
            <h1>Options Demo</h1>
            <div class="pure-g">
                <div class="pure-u-1-4" style='height:100vh;'>
                    <div class="plugin-side-options">
                        <button class="action-button" data-task='add-option'>Add New Option</button>
                        <button class="action-button" data-task='add-array-option'>Add Array Option</button>
                        <button class="action-button" data-task='get-option'>Display Saved Option</button>
                        <button class="action-button" data-task='get-array-option'>Display Option Array</button>
                        <button class="action-button" data-task='option-filter-hook'>Option Filter Hook</button>
                        <button class="action-button" data-task='update-option'>Update Option</button>
                        <button class="action-button" data-task='update-array-option'>Update Array Option</button>
                        <button class="action-button" data-task='delete-option'>Delete Option</button>
                        <button class="action-button" data-task='export-option'>Export Options</button>
                        <button class="action-button" data-task='import-option'>Import Options</button>
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
