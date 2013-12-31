<?php
namespace livestreambadger;

class LSB_Admin_Settings {
    
    const MENU_SLUG = 'live-stream-badger';
    private $storage;
    
    function __construct( $storage ) {
        $this->storage = $storage;

        add_action( 'admin_menu', array( $this, 'register' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        $plugin_base_name = LSB_PLUGIN_BASENAME;
        add_filter("plugin_action_links_$plugin_base_name", array( &$this, 'filter_plugins_page' ) );
    }
    
    function filter_plugins_page( $links ) {
        $settings_link = '<a href="options-general.php?page=live-stream-badger">Settings</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
    
    function register() {
        add_options_page( 'Live Stream Badger', 'Live Stream Badger', 'manage_options', self::MENU_SLUG, array( $this, 'render_page' ) );
    }
    
    function register_settings() {
        $default_settings = Settings::default_settings();
        foreach ( $default_settings as $key => $value ) {
            switch ( $value['type'] ) {

                case 'section':
                    add_settings_section( $key, $value['title'], '__return_false', $value['group'] );
                    break;

                case 'text':
                    $section = $value['section'];
                    add_settings_field( $key, $value['title'], array( &$this, 'text_element_cb'), $default_settings[$section]['group'], $section, 
                        // Arguments to pass to Callback
                        array(
                            'id' => $key,
                            'group' => $default_settings[$section]['group'],
                            'description' => $value['description']
                        )
                    );
                    break;

                case 'select':
                    $section = $value['section'];
                    add_settings_field( $key, $value['title'], array( &$this, 'select_element_cb'), $default_settings[$section]['group'], $section, 
                        array(
                            'id' => $key,
                            'group' => $default_settings[$section]['group'],
                            'description' => $value['description'],
                            'options' => $value['options'],
                            'default' => $value['default']
                        )
                    );
                    break;
                    
                case 'checkbox':
                    $section = $value['section'];
                    add_settings_field( $key, $value['title'], array( &$this, 'checkbox_element_cb'), $default_settings[$section]['group'], $section, 
                        array(
                            'id' => $key,
                            'group' => $default_settings[$section]['group'],
                            'description' => $value['description'],
                            'default' => $value['default']
                        )
                    );
                    break;
                    
            }
        }

        register_setting( WP_Options::OPTIONS_GROUP, WP_Options::OPTIONS_GROUP, array( $this, 'options_validate_cb' ) );
    }
    
    function text_element_cb( $args ) {
        $id = $args['id'];
        $group = $args['group'];
        $value = Settings::read_settings( $id );

        $html = sprintf('<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s">', $id, $group, $value);
        if ( isset( $args['description'] ) ) {
            $html.= sprintf('<p class="description">%s</p>', $args['description']);
        }

        echo $html;
    }
    
    function select_element_cb( $args ) {
        $id = $args['id'];
        $group = $args['group'];
        $value = Settings::read_settings( $id );
        
        ?>
        <select id="<?php echo $id; ?>" name="<?php echo $group . '[' . $id . ']'; ?>" value="<?php echo $value; ?>">
        <?php
            foreach ( $args['options'] as $option_name => $option_value ) :
            ?>
            <option value="<?php echo $option_value; ?>" <?php echo ($option_value == $value ? 'selected' : ''); ?>><?php echo $option_name; ?></option>
            <?php
            endforeach;
        ?>
        </select>
        
        <?php
        if ( isset( $args['description'] ) ) :
            ?>
            <p class="description"><?php echo $args['description']; ?></p>
            <?php
        endif;
    }
    
    function checkbox_element_cb( $args ) {
        $id = $args['id'];
        $group = $args['group'];
        $value = Settings::read_settings( $id );

        ?>
        <input name="<?php echo $group . '[' . $id . ']'; ?>" type="checkbox" id="<?php echo $id; ?>" value="true" <?php echo $value ? 'checked="checked"' : ''; ?>>
        <?php
        if ( isset( $args['description'] ) ) :
            ?>
            <p class="description"><?php echo $args['description']; ?></p>
            <?php
        endif;
    }
    
    function options_validate_cb( $input ) {
        $this->storage->reset();
        return $input;
    }

    function render_page() {
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php esc_html_e( 'Live Stream Badger - Settings', 'live-stream-badger' ) ?></h2>
            <form action="options.php" method="post">
                <?php settings_fields( WP_Options::OPTIONS_GROUP ); ?>
                <?php do_settings_sections( WP_Options::OPTIONS_GROUP ); ?>
                <?php submit_button(); ?>
            </form>

        </div>
    <?php
    }
}