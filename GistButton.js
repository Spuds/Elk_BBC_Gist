// What to add in the editor window when the button is clicked, based on which mode the editor is in
$.sceditor.command
	.set("gist", {
		// Show the button on/off state
		state: function() {
			var currentNode = this.currentNode();

			return $(currentNode).is("gscript") || $(currentNode).parents("gscript").length > 0 ? 1 : 0;
		},
		exec: function () {
			this.insert("[gist] ", "[/gist]", false)
		},
		txtExec: ["[gist]", "[/gist]"],
		tooltip: "Gist"
	}
);

// Called when toggling back and forth between wizzy and text, if you do not intend to render
// the tag in WYSIWYG mode, you can omit this
$.sceditor.plugins.bbcode.bbcode
	.set("gist", {
		tags: {
			gscript: null,
		},
		isInline: false,
		format: function(element, content) {
			// Coming from wysiwyg (html) mode to bbc (text) mode
			content = content.replace('&lt;', '<').replace('&gt;', '>');
			return "[gist]" + content.replace("<br />", "") + "[/gist]";
		},
		html: function(element, attrs, content) {
			// Going to wysiwyg from bbc
			content = content.replace('&lt;', '<').replace('&gt;', '>');
			return "<gscript>" + content.replace("<br />", "") + "</gscript>";
		}
	}
);