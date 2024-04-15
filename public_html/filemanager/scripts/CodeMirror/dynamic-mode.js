var instantiateCodeMirror = function(CmMode, config) {

	// if no code highlight needed, we apply default settings
	if (!config.edit.codeHighlight) {

		currentmode = 'default';

	// we highlight code according to extension file
	} else {

		if (CmMode === 'txt') {
			var currentmode = 'default';
		}
		if (CmMode === 'js') {
			loadJS('/public/filemanager/scripts/CodeMirror/mode/javascript/javascript.js');
			var currentmode = 'javascript';
		}
		if (CmMode === 'css') {
			loadJS('/public/filemanager/scripts/CodeMirror/mode/css/css.js');
			var currentmode = 'css';
		}
		if (CmMode === 'html') {
			loadJS('/public/filemanager/scripts/CodeMirror/mode/xml/xml.js');
			var currentmode = 'text/html';
		}
		if (CmMode === 'xml') {
			loadJS('/public/filemanager/scripts/CodeMirror/mode/xml/xml.js');
			var currentmode = 'application/xml';
		}
		if (CmMode === 'php') {
			loadJS('/public/filemanager/scripts/CodeMirror/mode/htmlmixed/htmlmixed.js');
			loadJS('/public/filemanager/scripts/CodeMirror/mode/xml/xml.js');
			loadJS('/public/filemanager/scripts/CodeMirror/mode/javascript/javascript.js');
			loadJS('/public/filemanager/scripts/CodeMirror/mode/css/css.js');
			loadJS('/public/filemanager/scripts/CodeMirror/mode/clike/clike.js');
			loadJS('/public/filemanager/scripts/CodeMirror/mode/php/php.js');
			var currentmode = 'application/x-httpd-php';
		}
		if (CmMode === 'sql') {
			loadJS('/public/filemanager/scripts/CodeMirror/mode/sql/sql.js');
			var currentmode = 'text/x-mysql';
		}

	}

	var editor = CodeMirror.fromTextArea(document.getElementById("edit-content"), {
		styleActiveLine : true,
		viewportMargin : Infinity,
		lineNumbers : config.edit.lineNumbers,
		lineWrapping : config.edit.lineWrapping,
		theme : config.edit.theme
	});

	// we finnaly set option
	editor.setOption("mode", currentmode);
	//console.log('CodeMirror mode  : ' + editor.getOption("mode"));

	return editor;
}
