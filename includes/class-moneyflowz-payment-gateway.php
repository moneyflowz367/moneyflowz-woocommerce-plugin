<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WC_Payment_Gateway')) {
    return;
}

class Moneyflowz_Payment_Gateway extends WC_Payment_Gateway {
    const NONCE_NAME = 'moneyflowz_checkout_nonce';
    const TEXT_DOMAIN = 'moneyflowz-payment-gateway';

    public function __construct() {
        $this->id = 'moneyflowz_payment_gateway';
        $this->icon = ''; // URL to the gateway icon
        $this->has_fields = false;
        $this->method_title = __('My Payment Gateway', self::TEXT_DOMAIN);
        $this->method_description = __('Accept payments through My Payment Gateway', self::TEXT_DOMAIN);

        // Initialize settings
        $this->init_form_fields();
        $this->init_settings();

        // Define settings
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');

        // Hooks
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        add_action('woocommerce_receipt_' . $this->id, [$this, 'receipt_page']);
        add_action('woocommerce_api_moneyflowz_payment_gateway', [$this, 'handle_webhook']);
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => __('Enable/Disable', self::TEXT_DOMAIN),
                'type'    => 'checkbox',
                'label'   => __('Enable My Payment Gateway', self::TEXT_DOMAIN),
                'default' => 'yes',
            ),
            'title' => array(
                'title'       => __('Title', self::TEXT_DOMAIN),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.', self::TEXT_DOMAIN),
                'default'     => __('My Payment Gateway', self::TEXT_DOMAIN),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __('Description', self::TEXT_DOMAIN),
                'type'        => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.', self::TEXT_DOMAIN),
                'default'     => __('Pay securely with My Payment Gateway.', self::TEXT_DOMAIN),
            ),
        );
    }

    public function process_payment($order_id) {
        $order = wc_get_order($order_id);

        // Security: Verify nonce
        $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
        if (!wp_verify_nonce($nonce, self::NONCE_NAME)) {
            wc_add_notice(__('Security check failed. Please try again.', self::TEXT_DOMAIN), 'error');
            return;
        }

        // Your payment processing logic goes here
        $payment_result = $this->process_payment_request($order);

        // Check if the payment was successful
        if ($payment_result === true) {
            // Mark the order as complete
            $order->payment_complete();

            // Reduce stock levels
            $order->reduce_order_stock();

            // Handle IPN (Instant Payment Notification) if applicable
            $this->handle_ipn($order_id);

            // Empty the cart
            WC()->cart->empty_cart();

            // Return thank you page redirect
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order),
            );
        } else {
            // Payment failed, display an error message
            wc_add_notice(__('Payment failed: ', self::TEXT_DOMAIN) . $payment_result, 'error');
            return;
        }
    }

    private function process_payment_request($order) {
        // Simulate logic based on other payment plugins
        $order_total = $order->get_total();
        $threshold_amount = 50; // Set your threshold amount

        if ($order_total > $threshold_amount) {
            // Simulating a successful payment for orders above the threshold
            return true;
        } else {
            // Simulating a failed payment for orders below the threshold
            return __('Payment failed. Order total must be above the threshold amount.', self::TEXT_DOMAIN);
        }
    }

    public function receipt_page($order_id) {
        echo '<p>' . __('Thank you for your order. Please click the button below to pay with My Payment Gateway.', self::TEXT_DOMAIN) . '</p>';
        echo $this->generate_moneyflowz_payment_gateway_form($order_id);
    }

    public function generate_moneyflowz_payment_gateway_form($order_id) {
        // Retrieve the order
        $order = wc_get_order($order_id);

        // Get the order total
        $order_total = $order->get_total();

        // Prepare form data (replace these with actual data from your gateway)
        $gateway_url = 'https://api-moneyflowz.com'; // Replace with your payment gateway API endpoint
        $api_key = 'your-api-key'; // Replace with your actual API key

        // Form HTML
        $form_html = '<form id="moneyflowz_payment_form" action="' . esc_url($gateway_url) . '" method="post">
            <input type="hidden" name="api_key" value="' . esc_attr($api_key) . '">
            <input type="hidden" name="order_id" value="' . esc_attr($order_id) . '">
            <input type="hidden" name="amount" value="' . esc_attr($order_total) . '">
            <input type="submit" class="button-alt" id="submit_moneyflowz_payment_form" value="' . __('Proceed to Payment', self::TEXT_DOMAIN) . '">
        </form>';

        // Output the form
        echo $form_html;
    }

    public function handle_webhook() {
        // Retrieve the raw request body
        $request_body = file_get_contents('php://input');

        // Log the raw request for debugging purposes (optional)
        error_log('Webhook Request: ' . $request_body);

        // Parse the request body as JSON (assuming your webhook sends data in JSON format)
        $data = json_decode($request_body, true);

        // Check if the JSON decoding was successful
        if (json_last_error() === JSON_ERROR_NONE) {
            // Handle specific webhook events based on your payment gateway's documentation
            $event_type = isset($data['event_type']) ? sanitize_text_field($data['event_type']) : '';

            switch ($event_type) {
                case 'payment_success':
                    $order_id = isset($data['order_id']) ? absint($data['order_id']) : 0;
                    $this->handle_payment_success($order_id, $data);
                    break;

                case 'payment_failure':
                    $order_id = isset($data['order_id']) ? absint($data['order_id']) : 0;
                    $this->handle_payment_failure($order_id, $data);
                    break;

                // Add more cases for other webhook events if applicable
                case 'additional_event':
                    $this->handle_additional_event($data);
                    break;

                default:
                    // Handle unknown or unhandled webhook events
                    break;
            }
        } else {
            // Log an error if JSON decoding fails
            error_log('Webhook JSON Decoding Error: ' . json_last_error_msg());
        }

        // Send a response to the gateway (if required)
        http_response_code(200); // Acknowledge the receipt of the webhook
        exit; // Terminate the script
    }

    private function handle_payment_success($order_id, $data) {
        // Implement logic for a successful payment webhook event
        // Update order status, send notifications, etc.

        // Example: Mark the order as completed
        $order = wc_get_order($order_id);
        $order->payment_complete();
        $order->add_order_note('Payment successful via webhook.');

        // Example: Send a confirmation email to the customer
        $customer_email = $order->get_billing_email();
        $subject = 'Payment Confirmation';
        $message = 'Thank you for your payment.';
        wp_mail($customer_email, $subject, $message);
    }

    private function handle_payment_failure($order_id, $data) {
        // Implement logic for a failed payment webhook event
        // Update order status, send notifications, etc.

        // Example: Mark the order as failed
        $order = wc_get_order($order_id);
        $order->update_status('failed', __('Payment failed via webhook.', 'text_domain'));

        // Example: Send a notification to the store admin
        $admin_email = get_option('admin_email');
        $subject = 'Payment Failure Notification';
        $message = 'Payment failed for order ID: ' . $order_id;
        wp_mail($admin_email, $subject, $message);
    }

    private function handle_additional_event($data) {
        // Implement logic for handling additional webhook events
        // Update order status, send notifications, etc.

        // Example: Log the event for debugging
        error_log('Additional webhook event received: ' . print_r($data, true));
    }

    private function handle_ipn($order_id) {
        // Retrieve the order
        $order = wc_get_order($order_id);
    
        // Get the order total
        $order_total = $order->get_total();
    
        // Prepare form data (replace these with actual data from your gateway)
        $gateway_url = 'https://api-moneyflowz.com'; // Replace with your payment gateway API endpoint
        $api_key = 'your-api-key'; // Replace with your actual API key
    
        // Prepare IPN data
        $ipn_data = array(
            'api_key'   => $api_key,
            'order_id'  => $order_id,
            'amount'    => $order_total,
            'event_type' => 'ipn_notification', // Adjust based on your gateway's documentation
        );
    
        // Use wp_remote_post to send the IPN data to your payment gateway
        $response = wp_remote_post($gateway_url, array(
            'body'    => json_encode($ipn_data),
            'headers' => array('Content-Type' => 'application/json'),
        ));
    
        // Check if the request was successful
        if (is_wp_error($response)) {
            error_log('IPN Request Error: ' . $response->get_error_message());
        } else {
            // Log the IPN response for debugging purposes
            error_log('IPN Response: ' . wp_json_encode($response));
    
            // Update order status based on the IPN response
            $body = wp_remote_retrieve_body($response);
            $ipn_response = json_decode($body, true);
    
            if ($ipn_response && isset($ipn_response['status']) && $ipn_response['status'] === 'success') {
                // Update order status and add a note
                $order->update_status('completed');
                $order->add_order_note('Payment marked as completed via IPN.');
            } else {
                // Log an error and handle accordingly
                error_log('IPN Processing Error: ' . $body);
            }
        }
    }
}
// Register the gateway
function add_moneyflowz_payment_gateway($methods) {
    $methods[] = 'Moneyflowz_Payment_Gateway';
    return $methods;
}
add_filter('woocommerce_payment_gateways', 'add_moneyflowz_payment_gateway');
