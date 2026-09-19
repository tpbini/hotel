<?php
/**
 * Fullscreen / Standalone Table Booking Application Template.
 * Rendered at /book-table/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$restaurant_name = get_option( 'rb_restaurant_name', 'The Crown & Thistle Bistro' );
$restaurant_phone = get_option( 'rb_restaurant_phone', '020 7946 0912' );
$restaurant_addr = get_option( 'rb_restaurant_address', 'London, UK' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo esc_html( sprintf( 'Reserve a Table - %s', $restaurant_name ) ); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo esc_url( RB_PLUGIN_URL . 'assets/css/booking.css?ver=' . RB_VERSION ); ?>">
    <script src="<?php echo esc_url( includes_url( '/js/jquery/jquery.min.js' ) ); ?>"></script>
    <style>
        body {
            margin: 0;
            padding: 20px 10px;
            background: #0f172a;
            background: linear-gradient(135deg, #090d16 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        .rb-standalone-brand {
            text-align: center;
            margin-bottom: 16px;
            color: #ffffff;
        }
        .rb-standalone-brand h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            margin: 0;
            color: #ffffff;
            font-weight: 700;
        }
        .rb-standalone-brand p {
            margin: 4px 0 0 0;
            color: #94a3b8;
            font-size: 14px;
        }
        .rb-footer-contact {
            margin-top: 20px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }
        .rb-footer-contact a {
            color: #38bdf8;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="rb-standalone-brand">
        <h1><?php echo esc_html( $restaurant_name ); ?></h1>
        <p>UK Table Reservation System</p>
    </div>

    <!-- Booking Root Element -->
    <div id="rb-booking-root" style="width: 100%; max-width: 680px;">
        <div class="rb-spinner-wrap"><div class="rb-spinner"></div><p style="color:#ffffff;">Loading reservation system...</p></div>
    </div>

    <div class="rb-footer-contact">
        <p>Telephone: <a href="tel:<?php echo esc_attr( $restaurant_phone ); ?>"><?php echo esc_html( $restaurant_phone ); ?></a> | <?php echo esc_html( $restaurant_addr ); ?></p>
    </div>

    <script>
        const rbData = {
            restUrl: '<?php echo esc_url_raw( rest_url( 'rb/v1/' ) ); ?>',
            nonce: '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>',
            currencySymbol: '£',
            dateFormat: 'd/m/Y',
            allergensNotice: <?php echo wp_json_encode( get_option( 'rb_allergen_notice', 'If you or any member of your party have a severe food allergy, please inform us directly by phone.' ) ); ?>,
            phone: <?php echo wp_json_encode( $restaurant_phone ); ?>,
            manageUrl: '<?php echo esc_url_raw( home_url( '/booking/manage/' ) ); ?>'
        };
    </script>
    <script src="<?php echo esc_url( RB_PLUGIN_URL . 'assets/js/booking.js?ver=' . RB_VERSION ); ?>"></script>
</body>
</html>
