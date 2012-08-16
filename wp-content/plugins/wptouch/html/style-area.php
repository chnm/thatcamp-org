<?php global $wptouch_settings; ?>

<div class="metabox-holder">
	<div class="postbox">
		<h3><span class="style-options">&nbsp;</span><?php _e( "Style &amp; Color Options", "wptouch" ); ?></h3>

			<div class="left-content skins-left-content">
				<p><?php _e( "Here you can customize some of the more visible features of WPtouch.", "wptouch" ); ?></p>
			</div>
		
			<div class="right-content skins-fixed">


 <!-- Default skin -->
 
		<div class="skins-desc" id="default-skin">
			<p><?php _e( "The default WPtouch theme emulates a native iPhone application.", "wptouch" ); ?></p>
			<ul class="wptouch-make-li-italic">
					<li><select name="style-background">
							<option <?php if ($wptouch_settings['style-background'] == "crossed-stripes-wptouch-bg") echo " selected"; ?> value="crossed-stripes-wptouch-bg">
								<?php _e( "Crossed Stripes (New)", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "fiber-wptouch-bg") echo " selected"; ?> value="fiber-wptouch-bg">
								<?php _e( "Fiber (New)", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "light-noise-diagonal-wptouch-bg") echo " selected"; ?> value="light-noise-diagonal-wptouch-bg">
								<?php _e( "Light Noise Diagonal (New)", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "low-contrast-linen-wptouch-bg") echo " selected"; ?> value="low-contrast-linen-wptouch-bg">
								<?php _e( "Low Contrast Linen (New)", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "random-grey-wptouch-bg") echo " selected"; ?> value="random-grey-wptouch-bg">
								<?php _e( "Random Grey (New)", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "stitched-wool-wptouch-bg") echo " selected"; ?> value="stitched-wool-wptouch-bg">
								<?php _e( "Stitched Wool (New)", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "white-carbon-wptouch-bg") echo " selected"; ?> value="white-carbon-wptouch-bg">
								<?php _e( "White Carbon (New)", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "white-carbon-fiber-wptouch-bg") echo " selected"; ?> value="white-carbon-fiber-wptouch-bg">
								<?php _e( "White Carbon Fiber (New)", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "classic-wptouch-bg") echo " selected"; ?> value="classic-wptouch-bg">
								<?php _e( "Classic", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "horizontal-wptouch-bg") echo " selected"; ?> value="horizontal-wptouch-bg">
								<?php _e( "Horizontal Grey", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "diagonal-wptouch-bg") echo " selected"; ?> value="diagonal-wptouch-bg">
								<?php _e( "Diagonal Grey", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "skated-wptouch-bg") echo " selected"; ?> value="skated-wptouch-bg">
								<?php _e( "Skated Concrete", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "argyle-wptouch-bg") echo " selected"; ?> value="argyle-wptouch-bg">
								<?php _e( "Argyle Tie", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['style-background'] == "grid-wptouch-bg") echo " selected"; ?> value="grid-wptouch-bg">
								<?php _e( "Thatches", "wptouch" ); ?>
							</option>
						</select>
						<?php _e( "Background", "wptouch" ); ?>
					</li> 
					<li><select name="h2-font">
							<option <?php if ($wptouch_settings['h2-font'] == "Helvetica Neue") echo " selected"; ?> value="Helvetica Neue">
								<?php _e( "Helvetica Neue", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['h2-font'] == "Helvetica") echo " selected"; ?> value="Helvetica">
								<?php _e( "Helvetica", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['h2-font'] == "thonburi-font") echo " selected"; ?> value="thonburi-font">
								<?php _e( "Thonburi", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['h2-font'] == "Georgia") echo " selected"; ?> value="Georgia">
								<?php _e( "Georgia", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['h2-font'] == "Geeza Pro") echo " selected"; ?> value="Geeza Pro">
								<?php _e( "Geeza Pro", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['h2-font'] == "Verdana") echo " selected"; ?> value="Verdana">
								<?php _e( "Verdana", "wptouch" ); ?>
							</option>
							<option <?php if ($wptouch_settings['h2-font'] == "Arial Rounded MT Bold") echo " selected"; ?> value="Arial Rounded MT Bold">
								<?php _e( "Arial Rounded MT Bold", "wptouch" ); ?>
							</option>
							</select>
						<?php _e( "Post Title H2 Font", "wptouch" ); ?>
					</li> 
					<li>#<input type="text" id="header-text-color" name="header-text-color" value="<?php echo $wptouch_settings['header-text-color']; ?>" /><?php _e( "Title text color", "wptouch" ); ?></li>
					<li>#<input type="text" id="header-background-color" name="header-background-color" value="<?php echo $wptouch_settings['header-background-color']; ?>" /><?php _e( "Header background color", "wptouch" ); ?></li>
					<li>#<input type="text" id="header-border-color" name="header-border-color" value="<?php echo $wptouch_settings['header-border-color']; ?>" /><?php _e( "Sub-header background color", "wptouch" ); ?></li>
					<li>#<input type="text" id="link-color" name="link-color" value="<?php echo $wptouch_settings['link-color']; ?>" /><?php _e( "Site-wide links color", "wptouch" ); ?></li>
			</ul> 
		</div>
		
		</div><!-- right content -->
	<div class="bnc-clearer"></div>
	</div><!-- postbox -->
</div><!-- metabox -->