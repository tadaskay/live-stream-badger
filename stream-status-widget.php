<?php

include_once LSB_PLUGIN_BASE . 'apis/class-api-core.php';

/**
 * Class Live Stream Widget.
 *
 * Displays Live Streams list.
 * Uses a menu configured in Widget Options and nested Custom Links.
 */
class LSB_Stream_Status_Widget extends WP_Widget {

	function LSB_Stream_Status_Widget() {
		parent::WP_Widget( FALSE, $name = 'LSB Stream Status' );
	}

	function widget( $args, $instance ) {

		// Get menu items for configured menu
		$menu_items = !empty( $instance['menu_id'] ) ? wp_get_nav_menu_items( $instance['menu_id'] ) : FALSE;

		// No menu selected
		if ( !$menu_items )
			return;

		$instance['title'] = apply_filters( 'widget_title', !empty( $instance['title'] ) ? $instance['title'] : '' );

		echo $args['before_widget'];

		if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		$core = new LSB_API_Core();

		// Get only those with links
		$links = array();
		foreach ( $menu_items as $m ) {
			if ( empty( $m->url ) || empty( $m->title ) )
				continue;
			$validated_urls = $core->validate_urls(array($m->url));
			$validated_url = isset($validated_urls[0]) ? $validated_urls[0] : NULL;
			$stream_id = LSB_Stream_Info::make_stream_id($validated_url->api_id, $validated_url->channel_name);
			$links[$stream_id] = $m;
		}

		$store = new LSB_Widget_Stream_Store();
		$stream_infos = $store->load();

		usort( $stream_infos, array( 'LSB_Stream_Info', 'sort_by_watching_now' ) );
		?>
		<div class="lsb-status-widget-holder">
			<ul>
				<?php
				foreach ($stream_infos as $stream_info) {
					/** @var $stream_info LSB_Stream_Info */
					$menu_item = $links[LSB_Stream_Info::make_stream_id($stream_info->api_id, $stream_info->channel_name)];
					if (empty($menu_item))
						continue;

					$is_on        = ( $stream_info->watching_now != -1 );
					$status_class = $is_on ? 'lsb-on' : 'lsb-off';
					?>
					<li class="lsb-status-widget-list-item <?php echo $status_class; ?>">
						<a href="<?php echo $menu_item->url; ?>"
						   target="_blank"><?php echo apply_filters( 'lsb_stream_status_widget_text', $menu_item->title ); ?></a>
						<span
							class="lsb-status-widget-indicator <?php echo $status_class; ?>"><?php echo $is_on ? $stream_info->watching_now : 'Offline'; ?></span>
					</li>
				<?php
				}
				?>
			</ul>
		</div>

		<?php
		echo $args['after_widget'];
	} // widget()

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']   = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['menu_id'] = (int) $new_instance['menu_id'];

		return $instance;
	}

	function form( $instance ) {
		$title   = isset( $instance['title'] ) ? $instance['title'] : '';
		$menu_id = isset( $instance['menu_id'] ) ? $instance['menu_id'] : '';

		$menus = get_terms( 'nav_menu', array( 'hide_empty' => FALSE ) );

		// No menus available
		if ( !$menus ) {
			echo '<p>' . sprintf( __( 'No menus have been created yet. <a href="%s">Create some</a>.' ), admin_url( 'nav-menus.php' ) ) . '</p>';
			return;
		}

		// Output options (title, menu select)
		?>
		<p>
			<label name="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>"/>
		</p>
		<p>
			<label name="<?php echo $this->get_field_id( 'menu_id' ); ?>"><?php _e( 'Select menu:' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'menu_id' ); ?>"
			        id="<?php echo $this->get_field_id( 'menu_id' ); ?>">
				<?php
				foreach ( $menus as $menu ) {
					echo '<option value="' . $menu->term_id . '"' . selected( $menu_id, $menu->term_id, FALSE ) . '>' . $menu->name . '</option>';
				}
				?>
			</select>
		</p>
	<?php
	} // form()
}

//eof