<?php

// Adding a discount field to the product card
function wcd_add_discount_field() {
    global $woocommerce, $post;

    echo '<div class="options_group">';
    woocommerce_wp_textarea_input( array(
        'id'          => '_wcd_dynamic_discounts',
        'label'       => __( 'Динамічні знижки', 'woocommerce' ),
        'placeholder' => '5:10,10:20', // An example of entering discount levels
        'description' => __( 'Введіть знижки в форматі кількість:відсоток, розділяючи комою.', 'woocommerce' ),
        'desc_tip'    => 'true'
    ) );
    echo '</div>';
}
add_action( 'woocommerce_product_options_pricing', 'wcd_add_discount_field' );

// Saving entered discounts
function wcd_save_discount_field( $post_id ) {
    $discounts = $_POST['_wcd_dynamic_discounts'];
    if ( ! empty( $discounts ) ) {
        update_post_meta( $post_id, '_wcd_dynamic_discounts', esc_attr( $discounts ) );
    }
}
add_action( 'woocommerce_process_product_meta', 'wcd_save_discount_field' );

// Discount calculation for each item in the cart
function wcd_apply_dynamic_discounts( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }

    // Checking the products in the basket
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];

        // Get product discounts
        $discounts = get_post_meta( $product_id, '_wcd_dynamic_discounts', true );

        if ( ! empty( $discounts ) ) {
            $discounts_array = wcd_parse_discounts( $discounts );
            $discount_percentage = wcd_get_discount_percentage( $quantity, $discounts_array );

            if ( $discount_percentage > 0 ) {
                $price = $cart_item['data']->get_price();
                $new_price = $price - ( $price * ( $discount_percentage / 100 ) );
                $cart_item['data']->set_price( $new_price );
            }
        }
    }
}
add_action( 'woocommerce_before_calculate_totals', 'wcd_apply_dynamic_discounts' );

// Parsing the entered discounts into an array
function wcd_parse_discounts( $discounts ) {
    $discounts_array = array();
    $discount_levels = explode( ',', $discounts );

    foreach ( $discount_levels as $level ) {
        list( $quantity, $discount ) = explode( ':', $level );
        $discounts_array[ (int) $quantity ] = (float) $discount;
    }

    return $discounts_array;
}

// Get a discount percentage based on the number of items
function wcd_get_discount_percentage( $quantity, $discounts_array ) {
    $discount_percentage = 0;

    foreach ( $discounts_array as $discount_quantity => $discount ) {
        if ( $quantity >= $discount_quantity ) {
            $discount_percentage = $discount;
        }
    }

    return $discount_percentage;
}