<?php

include_once LSB_PLUGIN_BASE . 'apis/class-api-core.php';
include_once LSB_PLUGIN_BASE . 'domain/class-stream.php';
include_once LSB_PLUGIN_BASE . 'domain/class-stream-summary.php';
include_once LSB_PLUGIN_BASE . 'domain/class-stream-sorter.php';
include_once LSB_PLUGIN_BASE . 'store/class-stream-storage.php';
include_once LSB_PLUGIN_BASE . 'functions.php';

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
		$sorting_strategy    = isset ( $instance['sorting_strategy'] ) ? $instance['sorting_strategy'] : 'by_watching_now';

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

		$stream_sorter = new LSB_Stream_Sorter( $links );
		if ( $sorting_strategy == 'by_status' ) {
			usort( $streams, array( $stream_sorter, 'sort_by_status' ) );
		}
		else if ( $sorting_strategy == 'by_watching_now' ) {
			usort( $streams, array( $stream_sorter, 'sort_by_watching_now' ) );
		}
		else {
			usort( $streams, array( $stream_sorter, 'sort_by_menu_order' ) );
		}

        // Display format templates

		$lsb_status_widget_format = 
			'<div class="lsb-status-widget-holder"><ul>%%items%%</ul></div>';
		$lsb_status_widget_format = apply_filters( 'lsb_status_widget_format', $lsb_status_widget_format );

		$lsb_status_widget_item_format = 
			'<li class="lsb-status-widget-list-item %%status_class%%">'.
			'  <span class="lsb-status-widget-title">'.
			'    <a href="%%url%%" target="_blank">%%title%%</a>'.
			'  </span>'.
			'  <span class="lsb-status-widget-indicator %%status_class%%">%%status_indicator%%</span>'.
			'</li>';
		$lsb_status_widget_item_format = apply_filters( 'lsb_status_widget_item_format', $lsb_status_widget_item_format );

		$lsb_status_widget_item_with_image_format = 
			'<li class="lsb-status-widget-list-item %%status_class%%">'.
			'  <span class="lsb-status-widget-title">'.
			'    <a href="%%url%%" target="_blank">%%title%%</a>'.
			'  </span>'.
			'  <span class="lsb-status-widget-indicator %%status_class%%">%%status_indicator%%</span>'.
			'  <span class="lsb-status-widget-image">'.
			'    <a href="%%url%%" target="_blank">'.
			'      <img src="%%image_src%%">'.
			'    </a>'.
			'  </span>'.
			'</li>';
		$lsb_status_widget_item_with_image_format = apply_filters( 'lsb_status_widget_item_with_image_format', $lsb_status_widget_item_with_image_format );

		$lsb_status_widget_no_content_format =
			'<div class="lsb-status-widget-holder"><span class="lsb-status-widget-info">%%message%%</span></div>';
		$lsb_status_widget_no_content_format = apply_filters( 'lsb_status_widget_no_content_format', $lsb_status_widget_no_content_format );

		$container = '';
		$items = '';

		foreach ( $streams as $stream ) {
			/** @var $stream LSB_Stream */
			$stream_id = $stream->summary->get_id();
			$menu_item = isset( $links[$stream_id] ) ? $links[$stream_id] : NULL;
			if ( empty( $menu_item ) )
				continue;

			$is_on = ( $stream->watching_now != -1 );
			if ( !$is_on && $hide_offline )
				continue;

			$var_image_src = '';
			if ( $is_on || !$hide_offline_images ) {
				if ( $display_type == 'screen_cap' && !empty( $stream->screen_cap_url ) ) {
					$var_image_src = $stream->screen_cap_url;
				} else if ( $display_type == 'image' && !empty ( $stream->image_url ) ) {
					$var_image_src = $stream->image_url;
				}
			}
			$show_image = !empty( $var_image_src );

			$var_status_class = $is_on ? 'lsb-on' : 'lsb-off';
			$var_url = $menu_item->url;
			$var_title = apply_filters( 'lsb_stream_status_widget_text', $menu_item->title );
			$var_status_indicator = $is_on ? $stream->watching_now : 'Offline';

			$item = '';
			if ($show_image === TRUE) {
				$item = lsb_template_sprintf( $lsb_status_widget_item_with_image_format,
					array(
						'%%status_class%%'     => $var_status_class,
						'%%url%%'              => $var_url,
						'%%title%%'            => $var_title,
						'%%status_indicator%%' => $var_status_indicator,
						'%%image_src%%'        => $var_image_src
					)
				);
			} else {
				$item = lsb_template_sprintf( $lsb_status_widget_item_format,
					array(
						'%%status_class%%'     => $var_status_class,
						'%%url%%'              => $var_url,
						'%%title%%'            => $var_title,
						'%%status_indicator%%' => $var_status_indicator
					)
				);
			}

			$items .= $item;
		}

		if ( !empty( $items ) ) {
			$container = lsb_template_sprintf( $lsb_status_widget_format,
				array(
					'%%items%%' => $items
				)
			);
		} else {
			$container = lsb_template_sprintf( $lsb_status_widget_no_content_format, 
				array(
					'%%message%%' => __( 'No streams available' )
				)
			);
		}

		echo $container;

		echo $args['after_widget'];
	} // widget()

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']   = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['menu_id'] = (int) $new_instance['menu_id'];

		$instance['display_type'] = $new_instance['display_type'];

		$instance['hide_offline']        = $new_instance['hide_offline'];
		$instance['hide_offline_images'] = $new_instance['hide_offline_images'];

		$instance['sorting_strategy'] = $new_instance['sorting_strategy'];

		return $instance;
	}

	function form( $instance ) {
		$title               = isset ( $instance['title'] ) ? $instance['title'] : '';
		$menu_id             = isset ( $instance['menu_id'] ) ? $instance['menu_id'] : '';
		$display_type        = isset ( $instance['display_type'] ) ? $instance['display_type'] : 'text';
		$hide_offline        = isset ( $instance['hide_offline'] ) ? $instance['hide_offline'] : FALSE;
		$hide_offline_images = isset ( $instance['hide_offline_images'] ) ? $instance['hide_offline_images'] : FALSE;
		$sorting_strategy    = isset( $instance['sorting_strategy'] ) ? $instance['sorting_strategy'] : 'by_watching_now';

		$menus = get_terms( 'nav_menu', array( 'hide_empty' => FALSE ) );

		// No menus available
		if ( !$menus ) {
			echo '<p>' . sprintf( __( 'No menus have been created yet. <a href="%s">Create some</a>.' ), admin_url( 'nav-menus.php' ) ) . '</p>';
			return;
		}

		// Output options (title, menu select)
		?>
		<p>
			<label>
				<?php _e( 'Title' ); ?>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>"
				       value="<?php echo $title; ?>"/>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Select menu containing stream links:' ); ?>
				<select class="widefat" name="<?php echo $this->get_field_name( 'menu_id' ); ?>" id="<?php echo $this->get_field_id( 'menu_id' ); ?>">
					<?php
					foreach ( $menus as $menu ) {
						echo '<option value="' . $menu->term_id . '"' . selected( $menu_id, $menu->term_id, FALSE ) . '>' . $menu->name . '</option>';
					}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Display type:' ); ?>
				<select name="<?php echo $this->get_field_name( 'display_type' ); ?>" id="<?php echo $this->get_field_id( 'display_type' ); ?>">
					<option value="text" <?php selected( $display_type, 'text' ) ?>>Text</option>
					<option value="screen_cap" <?php selected( $display_type, 'screen_cap' ) ?>>Screen Capture</option>
					<option value="image" <?php selected( $display_type, 'image' ) ?>>Channel's image</option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_offline' ); ?>" name="<?php echo $this->get_field_name( 'hide_offline' ); ?>"
					<?php checked( $hide_offline ) ?> value="1"/>
				<?php _e( 'Hide offline?' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_offline_images' ); ?>" name="<?php echo $this->get_field_name( 'hide_offline_images' ); ?>"
					<?php checked( $hide_offline_images ) ?> value="1"/>
				<?php _e( 'Hide offline images?' ); ?>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Sort streams by:' ); ?>
				<select name="<?php echo $this->get_field_name( 'sorting_strategy' ); ?>" id="<?php echo $this->get_field_id( 'sorting_strategy' ); ?>">
					<option value="by_watching_now" <?php selected( $sorting_strategy, 'by_watching_now' ) ?>>Watching Now</option>
					<option value="by_status" <?php selected( $sorting_strategy, 'by_status' ) ?>>Status</option>
					<option value="no_sort" <?php selected( $sorting_strategy, 'no_sort' ) ?>>No sort</option>
				</select>
			</label>
		</p>
	<?php
	} // form()
}

//eof