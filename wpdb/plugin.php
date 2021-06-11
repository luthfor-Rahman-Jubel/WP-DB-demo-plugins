<?php
/*
Plugin Name: WPDB Demo
Plugin URI: https://jubel-ahemd.xyz
Description: Demonstration of WPDB Methods
Version: 1.0.0
Author: Jubel Ahmed
Author URI: https://jubel-ahmed.xyz
License: GPLv2 or later
Text Domain: wpdb
Domain Path: /languages/
 */

function wpdb_init()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'peoples';
    $sql = "CREATE TABLE {$table_name} (
        id INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(250),
        email VARCHAR(250),
        `password` VARCHAR(255),
        age INT,
        PRIMARY KEY (id)
    );";
    require_once ABSPATH . "wp-admin/includes/upgrade.php";
    dbDelta($sql);
}
register_activation_hook(__FILE__, "wpdb_init");


add_action('admin_enqueue_scripts', function ($hook) {
    if ('toplevel_page_wpdb-demo' == $hook) {
        wp_enqueue_style('pure-grid-css', '//unpkg.com/purecss@1.0.1/build/grids-min.css');
        wp_enqueue_style('wpdb-demo-css', plugin_dir_url(__FILE__) . "assets/admin/css/style.css", null, time());
        wp_enqueue_script('wpdb-demo-js', plugin_dir_url(__FILE__) . "assets/admin/js/main.js", array('jquery'), time(), true);
        $nonce = wp_create_nonce('display_result');
        wp_localize_script(
            'wpdb-demo-js',
            'plugin_data',
            array('ajax_url' => ('admin-ajax.php'), 'nonce' => $nonce)
        );
    }
});


add_action('admin_menu', function () {
    add_menu_page('WPDB Demo', 'WPDB Demo', 'manage_options', 'wpdb-demo', 'wpdb_admin_menu_page');
});

add_action('wp_ajax_display_result', function () {
    global $wpdb;
    $table_name = $wpdb->prefix . 'peoples';
    if (wp_verify_nonce($_POST['nonce'], 'display_result')) {
        $task = $_POST['task'];
        if ($task == 'add-new-record') {
            $peoples = array(
                'name' => 'Jubel Ahmed',
                'email' => 'studentslerder44@gmail.com',
                'password' => 'fjdr03955t4o',
                'age' => 24
            );
            $wpdb->insert($table_name, $peoples, array('%s', '%s', '%s', '%d'));
            echo "New Record Added <br>";
            echo "ID:{$wpdb->insert_id} <br>";
        } elseif ('replace-or-insert' == $task) {
            $peoples = array(
                'id' => 5,
                'name' => 'Luthfor Rahman',
                'email' => 'lutfor@gmail.com',
                'password' => '2943fedrokfj',
                'age' => 23
            );
            $wpdb->replace($table_name, $peoples);
            echo "Operation Done <br>";
            echo "ID: {$wpdb->insert_id} <br>";
        } elseif ('update-data' == $task) {
            $persons = array('age' => 25);
            $result =  $wpdb->update($table_name, $persons, array('id' => 5));
            echo "Operation Done. Result = {$result} <br>";
        } elseif ('load-single-row' == $task) {
            $data = $wpdb->get_row("select * from {$table_name} where id=1");
            print_r($data);

            $data = $wpdb->get_row("select * from {$table_name} where id=1", ARRAY_A);
            print_r($data);

            $data = $wpdb->get_row("select * from {$table_name} where id=1", ARRAY_N);
            print_r($data);
        } elseif ('load-multiple-row' == $task) {
            $data = $wpdb->get_results("select * from {$table_name}", ARRAY_A);
            print_r($data);

            $data = $wpdb->get_results("select email, id, name, password, age from {$table_name}", OBJECT_K);
            print_r($data);
        } elseif ('add-multiple' == $task) {
            $persons = array(
                array(
                    'name' => 'Raju Khan',
                    'email' => 'rajulaku@gmail.com',
                    'password' => '4trjdjlk4t',
                    'age' => 45
                ),
                array(
                    'name' => 'Siam Ahmed ',
                    'email' => 'siam23@gmail.com',
                    'password' => '4trjdjlk4t',
                    'age' => 33
                ),
                array(
                    'name' => 'Muhit Ahmed',
                    'email' => 'muhit21@gmail.com',
                    'password' => 'jkvrij545',
                    'age' => 55
                )
            );

            foreach ($persons as $person) {
                $wpdb->insert($table_name, $person);
            }
            $data = $wpdb->get_results("select id, name, email, password, age from {$table_name}", OBJECT_K);
            print_r($data);
        } elseif ('prepared-statement' == $task) {
            $id = 3;
            $email = 'studentslerder44@gmail.com';
            $perpared_statement = $wpdb->prepare("select * from {$table_name} where id = %d", $id);
            // $perpared_statement = $wpdb->prepare("select * from {$table_name} where email = %s",$email );
            $data = $wpdb->get_results($perpared_statement, ARRAY_A);
            print_r($data);
        } elseif ('single-column' == $task) {
            $query = "SELECT email FROM {$table_name}";
            $result = $wpdb->get_col($query);
            print_r($result);
        } elseif ('single-var' == $task) {
            $result = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} ");
            echo "Total User: {$result} <br>";
            $result = $wpdb->get_var("SELECT name, email FROM {$table_name}", 0, 0);
            echo "Name of 1st User: {$result}<br>";

            $result = $wpdb->get_var("SELECT name, email FROM {$table_name}", 1, 0);
            echo "Email of 1st User: {$result}<br>";

            $result = $wpdb->get_var("SELECT password, age FROM {$table_name}", 0, 0);
            echo "Password of 1st User: {$result}<br>";

            $result = $wpdb->get_var("SELECT password, age FROM {$table_name}", 1, 0);
            echo "Age of 1st User: {$result}<br>";
        } elseif ('delete-data' == $task) {
            $result = $wpdb->delete($table_name, array('email' => 'studentslerder44@gmail.com'));
            echo "Delete Result = {$result} <br>";
        }
    }
    die(0);
});


function wpdb_admin_menu_page()
{
?>
    <div class="container" style="padding-top: 20px ">
        <h2>WPDB DEMO</h2>
        <div class="pure-g">
            <div class="pure-u-1-4" style="height:100vh;">
                <div class="plugin-side-options">
                    <button class="action-button" data-task="add-new-record">Add New Record </button>
                    <button class="action-button" data-task="replace-or-insert">Replace Of Insert Record</button>
                    <button class="action-button" data-task="update-data">Update Data</button>
                    <button class="action-button" data-task="load-single-row">Load Single Row </button>
                    <button class="action-button" data-task="load-multiple-row">Load Multiple Row </button>
                    <button class="action-button" data-task="add-multiple">Add Multiple Row</button>
                    <button class="action-button" data-task="prepared-statement">Prepared Statement</button>
                    <button class="action-button" data-task="single-column">Display Single Column</button>
                    <button class="action-button" data-task="single-var">Display Single Var</button>
                    <button class="action-button" data-task="delete-data">Delete Data</button>
                </div>

            </div>
            <div class="pure-u-3-4">
                <div class="plugin-demo-content">
                    <h2 class="plugin-result-title">Title</h2>
                    <div id="plugin-demo-result" class="plugin-result"></div>
                </div>
            </div>
        </div>

    </div>
<?php

}
