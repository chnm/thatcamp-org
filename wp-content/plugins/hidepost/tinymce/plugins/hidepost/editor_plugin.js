(function() {
	tinymce.PluginManager.requireLangPack('hidepost');
	tinymce.create('tinymce.plugins.HidePostPlugin', {
		init : function(ed, url) {
			ed.addCommand('mceHidePostInsert', function() {
				ed.execCommand('mceReplaceContent', 0, insertHidepost('visual', ''));
			});
			ed.addButton('hidepost', {
				title : 'hidepost.insert_hidepost',
				cmd : 'mceHidePostInsert',
				image : url + '/img/hidepost.gif'
			});
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('hidepost', n.nodeName == 'IMG');
			});
		},

		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : 'hidepost',
				author : 'Fu4ny',
				authorurl : 'http://nguyenthanhcong.com',
				version : '1.00'
			};
		}
	});
	tinymce.PluginManager.add('hidepost', tinymce.plugins.HidePostPlugin);
})();

// Function: Insert HidePost Quick Tag
function insertHidepost( ) {
	return '[hidepost='+ insertLevel() + ']'+TinyMCE_getSelectedText()+'[/hidepost]';
}

//returns the selected text from the editor
function TinyMCE_getSelectedText(){
     var inst = tinyMCE.selectedInstance;
   
     if (tinyMCE.isMSIE) {
    var doc = inst.getDoc();
    var rng = doc.selection.createRange();
    selectedText = rng.text;
     } else {
    var sel = inst.contentWindow.getSelection();
   
    if (sel && sel.toString){
                    selectedText = sel.toString();
    }else{
       selectedText = '';
    }
    }
    return selectedText;
} 