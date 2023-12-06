<?php
/**
 * Plugin Name: Moneyflowz Plugin
 * Description: Integrate Moneyflowz Plugin Payment Gateway with WooCommerce.
 * Version: 1.0.0
 * Author: Diego Visser
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include WooCommerce main class
if (!class_exists('WC_Payment_Gateway')) {
    return;
}

// Include the main class file
require_once(plugin_dir_path(__FILE__) . 'includes/class-moneyflowz-payment-gateway.php');
// Include additional files
require_once(plugin_dir_path(__FILE__) . 'includes/functions.php');
require_once(plugin_dir_path(__FILE__) . 'includes/api-handler.php');
require_once(plugin_dir_path(__FILE__) . 'includes/helper-functions.php');

// Initialize the plugin
function initialize_moneyflowz_payment_gateway() {
    $moneyflowz_payment_gateway = new Moneyflowz_Payment_Gateway();
}

// Initialize the plugin after WooCommerce is loaded
add_action('plugins_loaded', 'initialize_moneyflowz_payment_gateway');

// Example usage of your_plugin_general_function()
// Adjust this based on your actual functionality
function your_plugin_general_function() {
    // Your function logic
    your_plugin_helper_function(); // Call your helper function
}

// Example usage of your_plugin_helper_function()
// Adjust this based on your actual functionality
function your_plugin_helper_function() {
    // Helper function logic
    // Instantiate API_Handler
    $api_handler = new API_Handler();

    // Example: Call a method from API_Handler
    $result = $api_handler->some_api_method();

    // Add your helper function logic here

    return $result;
}
