# Moneyflowz Plugin

Integrate Moneyflowz Plugin Payment Gateway with WooCommerce.

## Description

This WordPress plugin enables seamless integration of the Moneyflowz Payment Gateway with WooCommerce, allowing users to make payments using the Moneyflowz service.

## Installation

1. Upload the `moneyflowz-plugin` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Configuration

1. Navigate to the WooCommerce settings.
2. Select the 'Payments' tab.
3. Enable 'Moneyflowz Payment Gateway'.
4. Configure the required settings, such as API keys and other gateway-specific details.

## Usage

Once the plugin is activated and configured, customers can select "My Payment Gateway" during the checkout process, and payments will be processed through Moneyflowz.

## Additional Files

### `api-handler.php`

Contains the `API_Handler` class responsible for handling API-related functionalities.

### `functions.php`

Includes general functions related to your plugin, such as `your_plugin_general_function()`.

### `helper-functions.php`

Includes helper functions used throughout the plugin, including `your_plugin_helper_function()`.

## Version

1.0.0

## Author

Diego Visser

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

