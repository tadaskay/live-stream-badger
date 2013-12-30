<?php

namespace livestreambadger;

class Templates {
    
    function printt( $template_string, $args ) {
        return str_replace(array_keys($args), array_values($args), $template_string);
    }

    function status_widget() {
        $template = '<div class="lsb-status-widget-holder"><ul>%%items%%</ul></div>';
        return apply_filters( 'lsb_status_widget_format', $template );
    }
    
    function status_widget_item() {
        $template = 
            '<li class="lsb-status-widget-list-item %%status_class%%">'.
			'  <span class="lsb-status-widget-title">'.
			'    <a href="%%url%%" target="_blank">%%title%%</a>'.
			'  </span>'.
			'  <span class="lsb-status-widget-indicator %%status_class%%">%%status_indicator%%</span>'.
			'</li>';
	    return apply_filters( 'lsb_status_widget_item_format', $template );
    }
    
    function status_widget_item_with_image() {
        $template = 
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
		return apply_filters( 'lsb_status_widget_item_with_image_format', $template );
    }
    
    function status_widget_no_content() {
        $template = '<div class="lsb-status-widget-holder"><span class="lsb-status-widget-info">%%message%%</span></div>';
        return apply_filters( 'lsb_status_widget_no_content_format', $template );
        
    }

}