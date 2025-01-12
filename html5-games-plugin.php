<?php
/*
Plugin Name: HTML5 Game Integrator
Description: Add multiple HTML5 games easily to your website with auto-update feature.
Version: 1.0
Author: Your Name
Text Domain: html5-game-integrator
*/

// Enqueue Styles and Scripts for Plugin
function html5_game_plugin_enqueue_scripts() {
    wp_enqueue_style('html5-games-plugin-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('html5-games-plugin-js', plugin_dir_url(__FILE__) . 'js/plugin.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'html5_game_plugin_enqueue_scripts');

// Shortcode to Display a Game
function display_html5_game($atts) {
    $atts = shortcode_atts(array(
        'game_id' => '',
        'width' => '800',
        'height' => '600',
    ), $atts, 'html5_game');

    $game_path = plugin_dir_url(__FILE__) . 'games/' . $atts['game_id'] . '/' . $atts['game_id'] . '.html';
    if (!file_exists(plugin_dir_path(__FILE__) . 'games/' . $atts['game_id'] . '/')) {
        return "<p>Game not found!</p>";
    }

    $output = "<div class='game-container'>";
    $output .= "<iframe src='{$game_path}' width='{$atts['width']}' height='{$atts['height']}' frameborder='0' allowfullscreen></iframe>";
    $output .= "</div>";

    return $output;
}
add_shortcode('html5_game', 'display_html5_game');

// Admin Settings Page to Upload Games
function html5_game_plugin_menu() {
    add_menu_page('HTML5 Games', 'HTML5 Games', 'manage_options', 'html5-games', 'html5_game_plugin_settings_page');
}
add_action('admin_menu', 'html5_game_plugin_menu');

function html5_game_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Upload and Manage HTML5 Games</h1>
        <form method="post" enctype="multipart/form-data">
            <h3>Upload New Game</h3>
            <label for="game_name">Game Name:</label>
            <input type="text" name="game_name" required><br><br>
            <label for="game_zip">Upload Game (ZIP format):</label>
            <input type="file" name="game_zip" accept=".zip" required><br><br>
            <input type="submit" name="upload_game" value="Upload Game">
        </form>
        
        <?php
        if (isset($_POST['upload_game'])) {
            $game_name = sanitize_text_field($_POST['game_name']);
            $game_zip = $_FILES['game_zip'];

            // Check if the file is a zip archive
            if ($game_zip['type'] == 'application/zip') {
                // Create game directory
                $upload_dir = plugin_dir_path(__FILE__) . 'games/' . $game_name;
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Unzip the game
                $zip = new ZipArchive();
                if ($zip->open($game_zip['tmp_name']) === TRUE) {
                    $zip->extractTo($upload_dir);
                    $zip->close();
                    echo '<p>Game uploaded successfully!</p>';
                } else {
                    echo '<p>Failed to unzip the game.</p>';
                }
            } else {
                echo '<p>Please upload a valid ZIP file.</p>';
            }
        }
        ?>
    </div>
    <?php
}

// Auto-update feature
require_once plugin_dir_path(__FILE__) . 'includes/updater.php';

// Initialize Updater
function html5_game_plugin_init_updater() {
    $updater = new HTML5_Game_Plugin_Updater(__FILE__);
    $updater->set_plugin_slug('html5-games-plugin');
    $updater->set_repo_url('https://github.com/yourusername/html5-games-plugin');
    $updater->set_author('Your Name');
    $updater->set_version('1.0');
    $updater->init();
}
add_action('admin_init', 'html5_game_plugin_init_updater');
