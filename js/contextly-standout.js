(function() {
	tinymce.create('tinymce.plugins.ContextlyStandoutPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('csoURLBoxCommand', function() {
            if ( disabled )
					return;
            ed.windowManager.open({
					title : 'Standout Stories by Contextly',
					file : url + '/form.html',
					width : 480,
					height : 208,
					inline : 1,
					resizable : 0
				}, {
					plugin_url : url
				});
			});
         ed.addCommand('csoURLBoxRelay', function() {
            return ed.selection.getContent({format:'text'});
         });
			// Register buttons
			ed.addButton('contextlystandout', {
				title : 'Standout Stories by Contextly', 
				cmd : 'csoURLBoxCommand', 
				image: url + '/../img/contextly-standout.gif' 
			});
         ed.onNodeChange.add(function(ed, cm, n, co) {
				disabled = co && n.nodeName != 'A';
            csoEditor = ed;
				csoSelectedText = ed.selection.getContent({format : 'text'});
            if (n.nodeName == 'A' || csoSelectedText != "")
               cm.setActive('contextlystandout', true);
            else 
               cm.setActive('contextlystandout', false);
			});

		},
		getInfo : function() {
			  return {
					longname : 'Standout Stories by Contextly',
					author : 'Ryan Singel',
					authorurl : 'http://contextly.com',
					infourl : 'http://contextly.com',
					version : "1.0"
			  };
		 }
		});
	// Register plugin
	tinymce.PluginManager.add('contextlystandout', tinymce.plugins.ContextlyStandoutPlugin);
})();
