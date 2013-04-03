<?php
/**
 * Widget template. This template can be overriden using the "sp_template_image-widget_widget.php" filter.
 * See the readme.txt file for more info.
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

echo $before_widget;
if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
if ( !empty( $imageurl ) ) {
	if ( $link ) {
		echo '<a class="'.$this->widget_options['classname'].'-image-link" href="'.$link.'" target="'.$linktarget.'">';
	}
	if ( $imageurl ) {
		echo '<img src="'.$imageurl.'" style="';
		if ( !empty( $width ) && is_numeric( $width ) ) {
			echo "max-width: {$width}px;";
		}
		if ( !empty( $height ) && is_numeric( $height ) ) {
			echo "max-height: {$height}px;";
		}
		echo "\"";
		if ( !empty( $align ) && $align != 'none' ) {
			echo " class=\"align{$align}\"";
		}
		if ( !empty( $alt ) ) {
			echo " alt=\"{$alt}\"";
		} else {
			echo " alt=\"{$title}\"";
		}
		echo " />";
	}

	if ( $link ) { echo '</a>'; }
}
if ( !empty( $description ) ) {
	echo '<div class="'.$this->widget_options['classname'].'-description" >';
	echo wpautop( $description );
	echo "</div>";
}
echo $after_widget;
?>