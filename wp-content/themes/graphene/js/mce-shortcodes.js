/* Handles the theme's shortcode buttons in the TinyMCE editor */
(function() {  

	tinyMCE.create('tinyMCE.plugins.grapheneShortCodes', {  
	
		init : function(ed, url) {  
			ed.addButton('warning', {  
				title : ed.getLang('graphenemcebuttons.warning_title'),
				image : url+'/buttons/warning.png',  
				onclick : function() {  
					 ed.selection.setContent('[warning]' + ed.selection.getContent() + '[/warning]');  
				}  
			});
			
			ed.addButton('error', {  
				title : ed.getLang('graphenemcebuttons.error_title'), 
				image : url+'/buttons/error.png',  
				onclick : function() {  
					 ed.selection.setContent('[error]' + ed.selection.getContent() + '[/error]');  
				}  
			});
			
			ed.addButton('notice', {  
				title : ed.getLang('graphenemcebuttons.notice_title'), 
				image : url+'/buttons/notice.png',  
				onclick : function() {  
					 ed.selection.setContent('[notice]' + ed.selection.getContent() + '[/notice]');  
				}  
			});
			
			ed.addButton('important', {  
				title : ed.getLang('graphenemcebuttons.important_title'), 
				image : url+'/buttons/important.png',  
				onclick : function() {  
					 ed.selection.setContent('[important]' + ed.selection.getContent() + '[/important]');  
				}  
			});
			
			ed.addButton('pullquote', {  
				title : ed.getLang('graphenemcebuttons.pullquote'), 
				image : url + '/buttons/pullquote.png',
				onclick : function() {  
					 ed.selection.setContent('[pullquote align="left|center|right" textalign="left|center|right" width="30%"]' + ed.selection.getContent() + '[/pullquote]');  
				}  
			});
		},  
		createControl : function(n, cm) {  
			return null;  
		},  
	});  
	tinyMCE.PluginManager.add('grapheneshortcodes', tinyMCE.plugins.grapheneShortCodes);  
})();