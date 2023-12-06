<?php
// File: functions.php

// Include the API_Handler class
require_once 'api-handler.php';

// Function to handle API requests
function your_plugin_general_function() {
    // Instantiate API_Handler
    $api_handler = new API_Handler();

    // Check the request method (GET, POST, PUT, DELETE)
    $request_method = $_SERVER['REQUEST_METHOD'];

    // Process the API request based on the method
    switch ($request_method) {
        case 'GET':
            $params = $_GET;
            $response = $api_handler->handle_get_request($params);
            break;

        case 'POST':
            // Assuming JSON payload for simplicity. Adjust as needed.
            $data = json_decode(file_get_contents('php://input'), true);
            $response = $api_handler->handle_post_request($data);
            break;

        case 'PUT':
            // Assuming JSON payload for simplicity. Adjust as needed.
            $data = json_decode(file_get_contents('php://input'), true);
            $response = $api_handler->handle_put_request($data);
            break;

        case 'DELETE':
            $params = $_GET;
            $response = $api_handler->handle_delete_request($params);
            break;

        default:
            $response = array(
                'status' => 'error',
                'message' => 'Unsupported request method',
            );
            break;
    }

    // Output the API response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    // Exit to prevent additional content in the response
    exit;
}
