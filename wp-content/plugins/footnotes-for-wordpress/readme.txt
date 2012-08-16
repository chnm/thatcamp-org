=== Footnotes for WordPress ===
Contributors: Charles Johnson
Donate link: http://projects.radgeek.com/wp-footnotes/
Tags: footnotes, formatting
Requires at least: 2.9
Tested up to: 3.0.1
Stable tag: 2010.0822

Footnotes for WordPress enables easy-to-use fancy footnotes for WordPress posts.

== Description ==

Footnotes for WordPress is a simple plugin designed with a simple aim in mind: to make
it dead-easy to include decently-formatted footnotes in posts and pages on your
WordPress blog.

The syntax is roughly based on the common MediaWiki syntax for footnotes, but uses the
WordPress shortcode conventions. So, to include a footnote with the text "Text," you use:

	This is footnoted.[ref]Text[/ref]

And that's all you need to do. When you add a footnote, Footnotes for WordPress will
create a note marker at the point that the foonote appears in the text, and includes
the text of the footnote in a styled list of notes down the page. When a reader clicks
on the link in a JavaScript-enabled browser, a script included with the plugin will
create a small bubble inline in the text, which pops up over the footnote marker for
easy reading without losing their place. In non-JavaScript-enabled contexts, clicking
on the footnote marker jumps down the page to the text of the note.

The jump is based on an unique anchor which the plugin can automatically generate for
you. However, if you want to create a specific ID of your own for the footnote, you
can do so using the `name="..."` attribute:

	This is footnoted.[ref name="my-unique-id-1"]Text[/ref]

If you define an ID for a footnote, you can also refer back to the same footnote
later on in the document, using the `[backref name="..."]` shortcode.

	This is footnoted.[ref name="source1"]Source 1[/ref]
	
	So is this.[ref name="source2"]Source[/ref]
	
	And this one comes from the same source as the first.[backref name="source1"]
	
By default, the list of footnotes appears at the bottom of the post. However, if you
wish them to appear somewhere above the bottom of the post, for formatting or other
reasons, you can do so using the `[references/]` shortcode:

	This is footnoted.[ref]Text[/ref]

	As are some[ref]Text[/ref] other things.

	[references/]

	See also: Endnotes.

Which will cause the two footnotes to appear beneath the second paragraph but above
the "See also:" text.

== Screenshots ==

1.  Add inline footnotes with dead-simple shortcode syntax.

2.  A note marker with a link to the text of the note will appear next to the footnoted
    text.

3.  The text of the note(s) will appear down the page.

4.  For ease of reading, users with JavaScript-enabled browsers can read the text of notes
    inline, without needing to jump down the page or lose their place.

== Installation ==

1.  Upload the `wp-footnotes` directory to the `/wp-content/plugins/` directory of
    your WordPress installation.

2.  Activate the plugin through the 'Plugins' panel in the WordPress admin interface

3.  Start writing posts with inline footnotes.`[ref]Using this shortcode![/ref]`

== Usage ==

*   To create a footnote, use `[ref]...[/ref]` to wrap the text of your note.

    Example: `This is footnoted.[ref]This is the text of the of the footnote.[/ref]`

*   To create a footnote with a specific unique ID, instead of the one that the plugin
    will automatically generate for you, use `[ref name="..."]...[/ref]`

    Example: `This is footnoted.[ref name="my-unique-id"]This is the note text.[/ref]`

*   If you define an ID for a footnote, you can also refer back to the same
    footnote later on in the document, using the `[backref name="..."]`
    shortcode.

	This is footnoted.[ref name="source1"]Source 1[/ref]
	
	So is this.[ref name="source2"]Source[/ref]
	
	And this one comes from the same source as the first.[backref name="source1"]

*   To force the plugin to display notes at a different location instead of at the bottom
    of the post, use `[references/]`

    Example:
    	
	This is footnoted.[ref]Text[/ref]

	As are some[ref]Text[/ref] other things.

	[references/]

	See also: Endnotes.

*   If you wish to change how footnotes are styled, you can alter them in your
    WordPress theme stylesheet, or using JavaScript. The default elements and
    classes are `<a class="footnoted">` for superscript links to footnotes,
    `<ol class="footnotes">` for the list of references, and
    `<li class="footnote">` for each individual footnote. So, for example, to
    display footnotes at the bottom in a simple list, rather than in individual
    boxes with "Note" headers, add the following line to your Theme stylesheet:
    
    	ol.footnotes li {
    		background: transparent !important;
    		padding: 0px !important;
    		border: none !important;
    		margin: 0.5em 2em !important;
    	}
    
    The use of `!important` will ensure that it overrides the default styles
    set up by the plugin.

*   Alternatively, if you want to change the CSS class which is applied to to
    the footnotes list, you can do so using the `class` parameter on
    `[references/]`:
    
    	This is a test.[ref]Lewis (2000).[/ref]
    	
    	<h3>Notes</h3>
    	
    	[references class="compact" /]
    	
    ... which will produce the following HTML:
    
    	<p>This is a test.<sup>[<a href="#test-n-1" class="footnoted"
    	id="to-test-n-1">1</a>]</sup></p>

    	<h3>Notes</h3>
    	<ol class="compact">
    	<li class="footnote" id="test-n-1"><strong><sup>[1]</sup></strong> Lewis
    	(2000). <a class="note-return" href="#to-test-n-1">&#x21A9;</a></li
    	></ol>

    Since the default styling is based on children of `ol.footnotes`, those
    styles will not apply, and you can apply whatever styles you wish to the
    class that you've created.

== ChangeLog ==

=== Changes from version 2010.0309 to version 2010.0822 ===

* Added `[backref name="..." /]` shortcode allowing you to refer back to a note with an established name

* Eliminated a bug which caused duplicate IDs if you tried to set up multiple sections of footnotes in the same post using repeated `[references/]` codes.

* Added `[references class="foo" /]` syntax, allowing user to supply their own class for CSS re-styling purposes.

* Tested for and verified compatibility up to WordPress 3.0.1 and WordPress trunk.

=== Changes from version 2010.0306 to version 2010.0309 ===

* Eliminates a harmless but potentially annoying source of "Missing argument" warnings from PHP

