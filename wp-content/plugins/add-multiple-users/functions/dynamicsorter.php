<p><strong><?php _e('Use the Dynamic Sort function to define the column order of your CSV data.','amulang'); ?></strong><br><span class="important"><?php _e('Refer to the Dynamic Sorter Help at the bottom of the screen for usage instructions.','amulang'); ?></span></p>

<div id="dynamicsorter">

	<div id="finalorder">
		<div id="ordertitle"><h2><?php _e('Your Column Order','amulang'); ?>:</h2></div>
		<div id="orderfield"><ul id="sorterlist"></ul></div>
        <input type="hidden" name="finalsort" id="finalsort" value="">
    </div>    

	<div id="dyn_add_standard">
		
        <h3><?php _e('Add Standard Column Names','amulang'); ?></h3>
        
        <ul>
        	<li id="dyn_user_login"><a href="#" title="<?php _e('Username/Login Name','amulang'); ?>">user_login</a>
            <li id="dyn_user_pass"><a href="#" title="<?php _e('Password for the user','amulang'); ?>">user_pass</a>
            <li id="dyn_user_email"><a href="#" title="<?php _e('Email Address for the user','amulang'); ?>">user_email</a>
            <li id="dyn_role"><a href="#" title="<?php _e('The users role on the site','amulang'); ?>">role</a>
            <li id="dyn_user_url"><a href="#" title="<?php _e('Website field in user profile','amulang'); ?>">user_url</a>
            <li id="dyn_user_nicename"><a href="#" title="<?php _e('Stripped back version of user login','amulang'); ?>">user_nicename</a>
            <li id="dyn_user_registered"><a href="#" title="<?php _e('The date the user was registered','amulang'); ?>">user_registered</a>
            <li id="dyn_display_name"><a href="#" title="<?php _e('The users Display Name on the site','amulang'); ?>">display_name</a>
            <li id="dyn_first_name"><a href="#" title="<?php _e('The users first name','amulang'); ?>">first_name</a>
            <li id="dyn_last_name"><a href="#" title="<?php _e('The users last name','amulang'); ?>">last_name</a>
            <li id="dyn_nickname"><a href="#" title="<?php _e('The users nickname','amulang'); ?>">nickname</a>
            <li id="dyn_description"><a href="#" title="<?php _e('Biographical Info field in user profile','amulang'); ?>">description</a>
            <li id="dyn_rich_editing"><a href="#" title="<?php _e('Enable/Disable the visual editor for the user','amulang'); ?>">rich_editing</a>
            <li id="dyn_comment_shortcuts"><a href="#" title="<?php _e('Enable/Disable keyboard shortcuts','amulang'); ?>">comment_shortcuts</a>
            <li id="dyn_admin_color"><a href="#" title="<?php _e('Admin theme for the user','amulang'); ?>">admin_color</a>
            <li id="dyn_show_admin_bar_front"><a href="#" title="<?php _e('Show/Hide the admin bar on the website','amulang'); ?>">show_admin_bar_front</a>
            <li id="dyn_aim"><a href="#" title="<?php _e('AIM field in user profile','amulang'); ?>">aim</a>
            <li id="dyn_yim"><a href="#" title="<?php _e('Yahoo IM field in user profile','amulang'); ?>">yim</a>
            <li id="dyn_jabber"><a href="#" title="<?php _e('Jabber field in user profile','amulang'); ?>">jabber</a>
            <li id="dyn_ignore"><a href="#" title="<?php _e('ignore this column','amulang'); ?>">ignore</a>
            
        </ul>
		
	</div>
	
	<div id="dyn_add_custom">
    
    	<h3><?php _e('Add Custom Column Names','amulang'); ?></h3>

        <label for="customcolumn"><?php _e('Add a custom column name','amulang'); ?>:</label>
        <input type="text" name="customcolumn" id="customcolumn" value="">
        <input type="button" name="submitcustom" id="submitcustom" value="<?php _e('Add Custom Column','amulang'); ?>">
        
    </div>
	
</div>