<?php

include_once LSB_PLUGIN_BASE . 'apis/class-api-core.php';
include_once LSB_PLUGIN_BASE . 'domain/class-stream.php';
include_once LSB_PLUGIN_BASE . 'domain/class-stream-summary.php';
include_once LSB_PLUGIN_BASE . 'store/class-stream-storage.php';

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
		$display_type        = isset ( $instance['display_type'] ) ? $instance['display_type'] : 'text';
		$hide_offline        = isset ( $instance['hide_offline'] ) ? $instance['hide_offline'] : FALSE;
		$hide_offline_images = isset ( $instance['hide_offline_images'] ) ? $instance['hide_offline_images'] : FALSE;

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

		$any_content_rendered = FALSE;

		$core = new LSB_API_Core();

		// Get only those with links
		$links = array();
		foreach ( $menu_items as $m ) {
			if ( empty( $m->url ) || empty( $m->title ) )
				continue;
			$stream_summaries = $core->validate_urls( array( $m->url ) );
			/** @var $stream_summary LSB_Stream_Summary */
			$stream_summary = isset( $stream_summaries[0] ) ? $stream_summaries[0] : NULL;
			if ( empty( $stream_summary ) )
				continue;

			$links[$stream_summary->get_id()] = $m;
		}

		$store   = new LSB_Stream_Storage();
		$streams = $store->load();

		usort( $streams, array( 'LSB_Stream', 'sort_by_watching_now' ) );
		?>
		<div class="lsb-status-widget-holder">
			<ul>
				<?php
				foreach ( $streams as $stream ) {
					/** @var $stream LSB_Stream */
					$stream_id = $stream->summary->get_id();
					$menu_item = isset( $links[$stream_id] ) ? $links[$stream_id] : NULL;
					if ( empty( $menu_item ) )
						continue;

					$is_on = ( $stream->watching_now != -1 );
					if ( !$is_on && $hide_offline )
						continue;

					$any_content_rendered = TRUE;

					$status_class = $is_on ? 'lsb-on' : 'lsb-off';
					?>
					<li class="lsb-status-widget-list-item <?php echo $status_class; ?>">
						<span class="lsb-status-widget-title">
							<a href="<?php echo $menu_item->url; ?>"
							   target="_blank"><?php echo apply_filters( 'lsb_stream_status_widget_text', $menu_item->title ); ?></a>
						</span>
						<span class="lsb-status-widget-indicator <?php echo $status_class; ?>"><?php echo $is_on ? $stream->watching_now : 'Offline'; ?></span>
						<?php
						if ( $is_on || !$hide_offline_images ) {
							if ( $display_type == 'screen_cap' && !empty( $stream->screen_cap_url ) ) {
								?>
								<span class="lsb-status-widget-image">
								<a href="<?php echo $menu_item->url; ?>" target="_blank">
									<img src="<?php echo $stream->screen_cap_url; ?>">
								</a>
							</span>
							<?php
							}
							else if ( $display_type == 'image' && !empty ( $stream->image_url ) ) {
								?>
								<span class="lsb-status-widget-image">
								<a href="<?php echo $menu_item->url; ?>" target="_blank">
									<img src="<?php echo $stream->image_url; ?>">
								</a>
							</span>
							<?php
							}
						}
						?>
					</li>
				<?php
				}
				?>
			</ul>
			<?php
			if ( !$any_content_rendered ) {
				?>
				<span class="lsb-status-widget-info"><?php _e( 'No streams available' ); ?></span>
			<?php
			}
			?>
		</div>

		<?php
		echo $args['after_widget'];
	} // widget()

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']   = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['menu_id'] = (int) $new_instance['menu_id'];

		$instance['display_type'] = $new_instance['display_type'];

		$instance['hide_offline']        = $new_instance['hide_offline'];
		$instance['hide_offline_images'] = $new_instance['hide_offline_images'];

		return $instance;
	}

	function form( $instance ) {
		$title               = isset ( $instance['title'] ) ? $instance['title'] : '';
		$menu_id             = isset ( $instance['menu_id'] ) ? $instance['menu_id'] : '';
		$display_type        = isset ( $instance['display_type'] ) ? $instance['display_type'] : 'text';
		$hide_offline        = isset ( $instance['hide_offline'] ) ? $instance['hide_offline'] : FALSE;
		$hide_offline_images = isset ( $instance['hide_offline_images'] ) ? $instance['hide_offline_images'] : FALSE;

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
			<select name="<?php echo $this->get_field_name( 'menu_id' ); ?>"
			        id="<?php echo $this->get_field_id( 'menu_id' ); ?>">
				<?php
				foreach ( $menus as $menu ) {
					echo '<option value="' . $menu->term_id . '"' . selected( $menu_id, $menu->term_id, FALSE ) . '>' . $menu->name . '</option>';
				}
				?>
			</select>
		</p>
		<p>
			<label name="<?php echo $this->get_field_id( 'display_type' ); ?>"><?php _e( 'Display type:' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'display_type' ); ?>""
			id="<?php echo $this->get_field_id( 'display_type' ); ?>">
			<option value="text" <?php selected( $display_type, 'text' ) ?>>Text</option>
			<option value="screen_cap" <?php selected( $display_type, 'screen_cap' ) ?>>Screen Capture</option>
			<option value="image" <?php selected( $display_type, 'image' ) ?>>Channel's image</option>
			</select>
		</p>
		<p>
			<label name="<?php echo $this->get_field_id( 'hide_offline' ); ?>"><?php _e( 'Hide offline?' ); ?></label>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_offline' ); ?>"
			       name="<?php echo $this->get_field_name( 'hide_offline' ); ?>" <?php checked( $hide_offline ) ?>
			       value="1"/>
		</p>
		<p>
			<label name="<?php echo $this->get_field_id( 'hide_offline_images' ); ?>"><?php _e( 'Hide offline images?' ); ?></label>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_offline_images' ); ?>"
			       name="<?php echo $this->get_field_name( 'hide_offline_images' ); ?>" <?php checked( $hide_offline_images ) ?>
			       value="1"/>
		</p>
	<?php
	} // form()
}

//eof