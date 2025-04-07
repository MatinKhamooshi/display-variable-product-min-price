<?php
/**
 * Plugin Name: Display Variable Product Minimum Price
 * Description: Displays the minimum price of variable products as the default price on all front pages. Ensures the price is greater than 0.
 * Version: 1.0
 * Author: Matin Khamooshi
 */

// Hook into WooCommerce to modify the variable product price display
add_filter('woocommerce_variable_price_html', 'custom_variable_price', 10, 2);

function custom_variable_price($price, $product) {
    // Get all available variations
    $available_variations = $product->get_available_variations();
    
    // Initialize minimum price
    $min_price = PHP_INT_MAX;

    // Loop through each variation to find the minimum price
    foreach ($available_variations as $variation) {
        $variation_price = floatval($variation['display_price']);

        // Only consider prices greater than 0
        if ($variation_price > 0 && $variation_price < $min_price) {
            $min_price = $variation_price;
        }
    }

    // If no valid minimum price found, retain the default price display
    if ($min_price == PHP_INT_MAX) {
        return $price;
    }

    // Format the price according to WooCommerce settings
    $price = wc_price($min_price);

    return $price;
}

// Hook into WooCommerce to modify the product price display on the front pages
add_filter('woocommerce_get_price_html', 'custom_variable_product_price', 10, 2);

function custom_variable_product_price($price, $product) {
    if ($product->is_type('variable')) {
        return custom_variable_price($price, $product);
    }
    return $price;
}
