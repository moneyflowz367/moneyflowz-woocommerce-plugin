<?php
// File: helper-functions.php

// Include the API_Handler class
require_once 'api-handler.php';

// Helper function to interact with the API
function your_plugin_helper_function() {
    // Instantiate API_Handler
    $api_handler = new API_Handler();

    // Example: Call a method from API_Handler
    $result = $api_handler->some_api_method();

    // Add your helper function logic here

    return $result;
}
