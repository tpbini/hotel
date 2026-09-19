<?php
/**
 * Standalone Guest Manage & Cancellation Portal Template.
 * Rendered at /booking/manage/{token}
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$token = get_query_var( 'rb_token' );
if ( empty( $token ) && isset( $_GET['token'] ) ) {
    $token = sanitize_text_field( wp_unslash( $_GET['token'] ) );
}

$restaurant_name = get_option( 'rb_restaurant_name', 'The Crown & Thistle Bistro' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo esc_html( sprintf( 'Manage Reservation - %s', $restaurant_name ) ); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo esc_url( RB_PLUGIN_URL . 'assets/css/booking.css?ver=' . RB_VERSION ); ?>">
    <script src="<?php echo esc_url( includes_url( '/js/jquery/jquery.min.js' ) ); ?>"></script>
    <style>
        body {
            margin: 0;
            padding: 30px 10px;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body>

    <div id="rb-manage-root" data-token="<?php echo esc_attr( $token ); ?>" style="width: 100%; max-width: 580px;">
        <div class="rb-spinner-wrap"><div class="rb-spinner"></div><p style="color:#ffffff;">Loading reservation...</p></div>
    </div>

    <script>
        const rbData = {
            restUrl: '<?php echo esc_url_raw( rest_url( 'rb/v1/' ) ); ?>',
            token: <?php echo wp_json_encode( $token ); ?>,
            currencySymbol: '£',
            dateFormat: 'd/m/Y'
        };
    </script>
    <script src="<?php echo esc_url( RB_PLUGIN_URL . 'assets/js/booking.js?ver=' . RB_VERSION ); ?>"></script>
</body>
</html>
