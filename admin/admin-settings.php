<?php

class LSB_Admin_Settings {

    /**
     * @var LSB_Diagnostics
     */
    private $diagnostics;

    function __construct( $diagnostics ) {
        $this->diagnostics = $diagnostics;
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

                <div style="font-family: Consolas, 'Courier New'; background: #ffffe0; border-radius: 5px;">
                    <?php $this->diagnostics->render(); ?>
                </div>
            </div>

        </div>

    <?php
    }
}