<?php
kreative_install();

function kreative_install()
{
	$kt =& get_instance();
	
	if ( ! get_option('kreativetheme_general'))
	{
		include (TEMPLATEPATH . '/functions/admin/options.php');
		
		foreach($options as $okey => $ovalue)
		{
			$data = array ();
			
			foreach($ovalue as $key => $value)
			{
				$data[$value['id']] = $value['standard'];
			}
			
			$kt->config->set($okey, $data);
			$kt->config->save($okey);
		}
	}
	
	$installed = $kt->config->item('installed', 'plugs');
	
	if ( ! $installed) 
	{
		$installed = array();
	}
	
	if ( ! in_array('related', $installed))
	{
		$success = kreative_install_relative();
		
		if ( !! $success) :
			array_push($installed, 'related');
			$kt->config->set('plugs', $installed, 'installed');
			$kt->config->save('plugs');
		endif;
	}
}

function kreative_install_relative()
{
	global $wpdb, $table_prefix;
	
	$installed = FALSE;
	$success = TRUE;
	
	$indexdata = $wpdb->get_results("SHOW index FROM {$table_prefix}posts");
	foreach ($indexdata as $index) 
	{
		if ($index->Key_name == 'kreative_title') 
		{
			$installed = TRUE;
		}
	}
	
	if ($installed == FALSE)
	{
		if ( ! $wpdb->query("ALTER TABLE {$table_prefix}posts ADD FULLTEXT `kreative_title` ( `post_title`)")) 
		{
			$success = FALSE;
		}
		if ( ! $wpdb->query("ALTER TABLE {$table_prefix}posts ADD FULLTEXT `kreative_content` ( `post_content`)")) 
		{
			$success = FALSE;
		}
	}
	
	return $success;
	
}
