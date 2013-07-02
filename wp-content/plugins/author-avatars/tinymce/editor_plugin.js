/**
* TinyMCE Plugin for inserting authoravatars shortcodes.
*/
(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('authoravatars');
	tinymce.create('tinymce.plugins.authoravatarsPlugin', {
	/**
	* Initializes the plugin, this will be executed after the plugin has been created.
	* This call is done before the editor instance has finished it's initialization so use the onInit event
	* of the editor instance to intercept that event.
	*
	* @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
	* @param {string} url Absolute URL to where the plugin is located.
	*/
	init : function(ed, url) {
		// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceauthoravatars');
		ed.addCommand('mceauthoravatars', function() {
		ed.windowManager.open({
		file: 'admin-ajax.php?action=author-avatars-editor-popup',
		width : 600 + ed.getLang('authoravatars.delta_width', 0),
		height : 500 + ed.getLang('authoravatars.delta_height', 0),
		inline : 1
		}, {
		plugin_url : url // Plugin absolute URL
		});
	});
	// Register authoravatars button
	ed.addButton('authoravatars', {
		title : 'authoravatars.desc',
		cmd : 'mceauthoravatars',
		image : url + '/img/authoravatars.gif'
	});
	// Add a node change handler, selects the button in the UI when a image is selected
	/*ed.onNodeChange.add(function(ed, cm, n) {
	cm.setActive('authoravatars', n.nodeName == 'IMG');
	});*/
	},
	/**
	* Returns information about the plugin as a name/value array.
	* The current keys are longname, author, authorurl, infourl and version.
	*
	* @return {Object} Name/value array containing information about the plugin.
	*/
	getInfo : function() {
		return {
			longname : 'authoravatars plugin',
			author : 'Benedikt Forchhammer,Paul Bearne',
			authorurl : 'http://mind2.de,http://bearne.ca',
			infourl : 'http://wordpress.org/extend/plugins/author-avatars',
			version : "1.5.1"
		};
	}
});
// Register plugin
tinymce.PluginManager.add('authoravatars', tinymce.plugins.authoravatarsPlugin);
})();

