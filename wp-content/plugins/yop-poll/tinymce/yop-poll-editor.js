(function() {
		tinymce.create('tinymce.plugins.YopPollPlugin', {
				init : function(ed, url) {
					ed.addCommand('mceYopPollInsert', function() {
							ed.windowManager.open({
									file : yop_poll_editor_config.dialog_url,
									width : 300 + 'px',
									height : 150 + 'px',
									inline : 1
								});

					});
					ed.addButton('yoppoll', {
							title : yop_poll_editor_config.title,
							cmd : 'mceYopPollInsert',
							image : url + '/yop-poll-admin-menu-icon16.png'
					});
					ed.onNodeChange.add(function(ed, cm, n) {
							cm.setActive('yoppoll', n.nodeName == 'IMG');
					});
				},
				createControl : function(n, cm) {
					return null;
				},
				getInfo : function() {
					return {
						longname : "Yop Poll",
						author : 'YourOwnProgrammer',
						authorurl : 'http://www.YourOwnProgrammer.com',
						infourl : 'http://www.YourOwnProgrammer.com/yop_poll',
						version : "1.0"
					};
				}
		});
		tinymce.PluginManager.add('yoppoll', tinymce.plugins.YopPollPlugin);
})();