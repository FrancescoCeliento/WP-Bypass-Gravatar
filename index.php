<?php
/**
 * Plugin Name: WP Bypass Gravatar
 * Description: Bypassa completamente Gravatar e usa avatar locali con set configurabile. Gravatar completamente bypassato, Cache via transient (performance), Fallback configurabile, Set avatar espandibile, Privacy & GDPR OK
 * Version: 1.1.0
 * Author: Francesco Celiento
 * Author URI: https://www.francescoceliento.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* =====================================================
 * COSTANTI
 * ===================================================== */
define( 'BG_AVATAR_DIR', plugin_dir_path( __FILE__ ) . 'avatar/' );
define( 'BG_AVATAR_URL', plugin_dir_url( __FILE__ ) . 'avatar/' );
define( 'BG_OPTION_KEY', 'bg_avatar_settings' );

/* =====================================================
 * DEFAULT OPTIONS
 * ===================================================== */
function bg_default_options() {
    return [
        'enabled'        => 1,
        'range_min'      => 1,
        'range_max'      => 100,
        'default_avatar' => '001.png',
    ];
}

/* =====================================================
 * GET OPTIONS
 * ===================================================== */
function bg_get_options() {
    return wp_parse_args(
        get_option( BG_OPTION_KEY, [] ),
        bg_default_options()
    );
}

/* =====================================================
 * AVATAR CORE (cache + hashing)
 * ===================================================== */
add_filter( 'pre_get_avatar_data', 'bg_avatar_override', 10, 2 );

function bg_avatar_override( $args, $id_or_email ) {

    $opt = bg_get_options();

    if ( ! $opt['enabled'] ) {
        return $args;
    }

    $email = bg_extract_email( $id_or_email );
    $cache_key = 'bg_avatar_' . md5( $email );

    // 1️⃣ Cache transient
    $cached = get_transient( $cache_key );
    if ( $cached ) {
        return bg_apply_avatar( $args, $cached );
    }

    // 2️⃣ Calcolo avatar
    $file = bg_calculate_avatar( $email, $opt );

    // 3️⃣ Fallback se file mancante
    if ( ! file_exists( BG_AVATAR_DIR . $file ) ) {
        $file = $opt['default_avatar'];
    }

    // 4️⃣ Cache (24h)
    set_transient( $cache_key, $file, DAY_IN_SECONDS );

    return bg_apply_avatar( $args, $file );
}

/* =====================================================
 * HELPERS
 * ===================================================== */
function bg_extract_email( $id_or_email ) {

    if ( is_object( $id_or_email ) && ! empty( $id_or_email->comment_author_email ) ) {
        return strtolower( trim( $id_or_email->comment_author_email ) );
    }

    if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
        return strtolower( trim( $id_or_email ) );
    }

    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id', $id_or_email );
        if ( $user ) {
            return strtolower( trim( $user->user_email ) );
        }
    }

    return 'default@local.avatar';
}

function bg_calculate_avatar( $email, $opt ) {

    $range = max( 1, (int) $opt['range_max'] - (int) $opt['range_min'] + 1 );
    $hash  = abs( crc32( $email ) );
    $num   = ( $hash % $range ) + (int) $opt['range_min'];

    return str_pad( $num, 3, '0', STR_PAD_LEFT ) . '.png';
}

function bg_apply_avatar( $args, $file ) {

    $args['url']           = BG_AVATAR_URL . $file;
    $args['force_default'] = true;
    $args['found_avatar']  = true;

    return $args;
}

/* =====================================================
 * ADMIN PANEL
 * ===================================================== */
add_action( 'admin_menu', 'bg_admin_menu' );
function bg_admin_menu() {
    add_options_page(
        'WP Bypass Gravatar',
        'WP Bypass Gravatar',
        'manage_options',
        'bypass-gravatar',
        'bg_admin_page'
    );
}

add_action( 'admin_init', 'bg_register_settings' );
function bg_register_settings() {
    register_setting( 'bg_settings', BG_OPTION_KEY );
}

function bg_admin_page() {

    $opt = bg_get_options();
    ?>
    <div class="wrap">
        <h1>WP Bypass Gravatar</h1>

        <form method="post" action="options.php">
            <?php settings_fields( 'bg_settings' ); ?>

            <table class="form-table">

                <tr>
                    <th>Abilita avatar</th>
                    <td>
                        <input type="checkbox" name="<?php echo BG_OPTION_KEY; ?>[enabled]" value="1" <?php checked( $opt['enabled'], 1 ); ?>>
                    </td>
                </tr>

                <tr>
                    <th>Avatar di default</th>
                    <td>
                        <input type="text" name="<?php echo BG_OPTION_KEY; ?>[default_avatar]" value="<?php echo esc_attr( $opt['default_avatar'] ); ?>">
                        <p class="description">Es: 001.png</p>
                    </td>
                </tr>

                <tr>
                    <th>Range avatar</th>
                    <td>
                        Da <input type="number" name="<?php echo BG_OPTION_KEY; ?>[range_min]" value="<?php echo esc_attr( $opt['range_min'] ); ?>" style="width:80px;">
                        a <input type="number" name="<?php echo BG_OPTION_KEY; ?>[range_max]" value="<?php echo esc_attr( $opt['range_max'] ); ?>" style="width:80px;">
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

?>
