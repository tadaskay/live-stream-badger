<?php
namespace livestreambadger;

class LSB_Admin_Settings {

    function __construct( ) {
        add_action( 'admin_menu', array( $this, 'register' ) );
    }

    function register() {
        add_options_page( 'Live Stream Badger', 'Live Stream Badger', 'manage_options', 'live-stream-badger', array( $this, 'render' ) );
    }

    function render() {
        ?>

        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php esc_html_e( 'Live Stream Badger - Settings', 'live-stream-badger' ) ?></h2>

            <div>
                <h3>Diagnostics</h3>
            </div>

        </div>

    <?php
    }
}