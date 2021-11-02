tinymce.init({
	menu: {},
	selector: "textarea.tinymce",
	theme: "modern",
	width: "98%",
	forced_root_block: false,
	plugins: 'textcolor, link, image, preview',
	toolbar: "undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview fullpage",
	setup: function(ed) {
		if (ed.getElement().readOnly) {
			ed.settings.readonly = true;
			return;
		}
		var parent = ed.getElement().parentNode;
		while (parent != null) {
			if (parent.tagName == 'TABLE' && parent.className.indexOf('de_readonly') >= 0) {
				if (ed.getElement().className.indexOf('preserve_editing') < 0) {
					ed.settings.readonly = true;
				}
				break;
			}
			parent = parent.parentNode;
		}
	}
});