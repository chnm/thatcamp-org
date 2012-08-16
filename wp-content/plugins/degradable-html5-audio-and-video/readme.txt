=== Degradable HTML5 audio and video ===
Contributors: soukie
Author URI: http://soukie.net/
Donate link: http://soukie.net/support-this-site/
Tags: audio, html5, shortcode, video
Requires at least: 2.6
Tested up to: 2.9.2
Stable tag: 1.8.2

Shortcodes for HTML5 video and audio, with auto-inserted links to alternative file types, and degradable performance (lightweight Flash and download).

== Description ==

Embed video and audio on your website using shortcodes. The plugin enables HTML5 native playback for users with compatible browsers while offering an elegant degradation to other users through very lightweight *Flash* players. For HTML5 playback, it auto-detects and offers different alternatives, or degrades to Flash, and (failing even that) to download links.

Typical usage is simply `[audio src="http://myblog.com/wp-content/uploads/2009/09/mysong"]`

The plugin will make sure this does the following:

1. If the user has an HTML5 support for video and audio, it will play the media natively in an appropriate format.
1. Otherwise, if the user has Flash, it will play the media in lightweight Flash players.
1. Otherwise, there will be a link for the audio or video files so that the user can still play them using their installed software players.

The plugin also allows you to provide only one version of the file (and force Firefox to fallback on Flash).

Refer to the **Instructions** section below on how to use the plugin.

**Codecs and browsers:** The current situation with HTML5 is not ideal. For video, you should supply two formats to support all browsers. (But if you do not mind forcing Firefox to fall back to Flash, you can provide H.264 version only.) See the tables below to see what file will be played based on the support in the current browsers.

`VIDEO           Flash   H.264   Ogg Theora
                .flv     .m4v      .ogv
Firefox 3.6      -          -        X
Safari 4.0       -          X        -
Chrome 4.0       -          X        X
Other (Flash)    X          X        -`

When a browser supports multiple formats and the files are available, the preference goes from left to right (e.g. Chrome will prefer the H.264 format; reflecting the quality of the codecs). The FLV format is optional and it is provided for backwards compatibility to play older FLV-only videos either alone (no HTML5 video) or as a preferred fallback version when used alongside versions converted to H.264 and Ogg. 


`AUDIO         WAVE   MPEG-4   Ogg Vorbis   MPEG
              .wav    .m4a       .oga      .mp3
Firefox 3.6     X       -          X         -
Safari 4.0      X       X          -         X
Chrome 4.0      -       X          X         X
Other (Flash)   -       -          -         X`

For audio, you have to include at least .mp3 and .oga/.ogg versions to cover all browsers. (Or, you can supply .mp3 only which will force Firefox to fall back to Flash). Other formats are optional. Again, the preference for multiple available files in browsers supporting multiple codecs is left to right.

The Flash players used are a standalone version of [WordPress Audio Player](http://wpaudioplayer.com/standalone) by Martin Laine, and [Videoplayer](http://code.google.com/p/mrdoob/wiki/videoplayer) by Mr.doob.

[Plugin Homepage](http://soukie.net/degradable-html5-audio-and-video-plugin/)

= Instructions =

Currently, there is no settings panel. All you need is the syntax for the shortcodes explained below.

**Audio**

Basic syntax: `[audio src="`File URL/path`"]`

Advanced syntax: `[audio src="`File URL/path`" options="`special string`" id="`string`" format="`special string`"]`

Example:

`[audio src="http://myblog.com/wp-content/uploads/2009/09/mysong" options="autoplay loop controls" id="header-audio"]`

* `src` is required. It must be an absolute or relative path to audio with the file name ("mysong", in the above example). The files need to be located somewhere in your content folder (usually 'wp-content') unless you specify the files using the `format` option. File extension can be omitted. Upload "mysong.mp3", "mysong.ogg" etc. as applicable to the specified location.
* `options` is optional. It is a space-separated list of attributes defining the player behavior: 
**autoplay** to start playback automatically; **autobuffer** to start preparing the playback, **controls** to display the built-in playback controls (otherwise you can build and hook up your own), and **loop** to start from the beginning when the end is reached. The default value is `"controls autobuffer"`.
* `id` is optional. If you do not include one, the audio tag will have an automatically generated ID of `html5audio-`*number*. The IDs for the Flash players are prefixed with `f-`.
* `class` is optional. It is applied to a 'wrapper' div and to the audio tag. If you do not include one, the class will be 'html5audio'.
* `format` is optional. It is a space-separated list of available file formats. (Recognized values are **auto**, **m4a**, **mp3**, **oga**, **ogg** and **wav**.) The default value is **auto** which autodetects the formats. You can specify a list of available formats manually instead (e.g. `format="oga mp3 wav"`).
 
**Video**

Basic syntax: `[video src="`File URL/path`"]`

Advanced syntax: `[video src="`File URL/path`" width="`pixel size`" height="`pixel size`" options="`string`" id="`string`" format="`special string`"]`

Example:

`[video src="http://myblog.com/videos/vidclip" poster="http://myblog.com/wp-content/uploads/2009/09/clip-teaser.jpg" width="320" height="240" options="autoplay" id="vid-1"]`

* `src` is required. It must be an absolute or relative path to video with the file name ("vidclip", in this example). The files need to be located somewhere in your content folder (usually 'wp-content') unless you specify the files using the `format` option. File extension can be omitted. Upload "vidclip.ogg" (in Ogg Theora format) and "vidclip.m4v" (in mp4 format) to the specified location.
* `poster` is optional. It is a URL to the image you want to display before the video loads/starts playback. (If a jpg with the same file name can be detected, it will be used when 'poster' is not specified.)
* `width` and `height` are optional. The default size is 480x320 (which is the resolution of iPhone; larger videos might not play back on iPhone/iPod Touch).
* `options` is optional. It is a space-separated list of attributes defining the player behavior: 
**autoplay** to start playback automatically; **autobuffer** to start preparing the playback, **controls** to display the built-in playback controls (otherwise you can build and hook up your own), and **loop** to start from the beginning when the end is reached. The default value is `"controls autobuffer"`.
* `id` is optional. If you do not include one, the video tag will have an automatically generated ID of `html5video-`*number*. The IDs for the Flash players are prefixed with `f-`.
* `class` is optional. It is applied to a 'wrapper' div and to the video tag. If you do not include one, the class will be 'html5audio'.
* `format` is optional. It is a space-separated list of available file formats. (Recognized values are **auto**, **flv**, **m4v**, **ogg** and **ogv**.) The default value is **auto** which autodetects the formats. You can specify a list of available formats manually instead (e.g. `format="ogg m4v"`).

If you find this plugin useful, you can rate it and link to [the plugin](http://soukie.net/degradable-html5-audio-and-video-plugin/). If you don't like it, feel free to leave feedback and comments on the webpage.

== Installation ==

1. Upload the unzipped folder `degradable-html5-audio-and-video` to your `plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. And then follow the usage instructions on the Description page

== Frequently Asked Questions ==

If your files do not play properly, check the following points:

= So what file formats are required? =
Only mp3 for audio, and m4v for video. When Ogg versions are missing, JavaScript is used to force Forefox to fall back to Flash. To fully support HTML5 audio and video and bypass JavaScript, include the Ogg versions. All other audio formats are optional. To support migration from legacy video files, it is also possible to use this plugin for playback of FLV files only.

= The final HTML code looks fine but doesn't play in Firefox/Safari =
The server needs to be configured to serve the video files with the proper MIME type. (Setting the type through HTML code alone is not sufficient.) On a Linux host, you can add the following lines to the .htaccess file:

`AddType video/ogg ogg ogv`

`AddType video/mp4 mp4`

= Are there limitations to the extensions? =

The plugin requires specific file formats and extensions. For Ogg formats, the extension has to be .ogv or .ogg for video, and .oga or .ogg for audio. H.264 format needs the extension .m4v, and MPEG-4 audio m4a. Follow the instructions under *Description* for the best results. If you decide not to create two versions of video (using e.g. **ffmpeg2theora**), and you provide only the H.264 file, the plugin will detect this and will use JavaScript to play the file in Firefox using the fallback player.

= The plugin does not recognize the location of my files =
The current version of the plugin will fail if you link to the audio or video using the attachment ID. If you store the files on a different server or outside of your wp-content directory, use the `format` option to manually specify the available file formats; this bypasses the autodetection that requires the files in the content directory of WordPress. On some configurations, you need to specify the location using a server path (e.g. [audio src="/wp-content/uploads/mysong"]); this can be outside of wp-content but if it is, specify the 'format' option. 

= Can I change the look or behavior of the fallback players? =
For audio, you can. Refer to the [available options](http://wpaudioplayer.com/standalone "The options are listed at the end of the page") and add lines to the file `degradable-html5-audio-and-video.php` under the heading `Format the player by inserting lines here`.

= What if the plugin conflicts with another plugin? =
There is a known conflict with *Viper's Video Quicktags*. If you use that, download a modified version of Degradable HTML5 plugin from my website. This uses syntax [html5audio ...] and [html5video ...] to prevent the conflict. *Audio Player* might also not work but you can use Degradable HTML5 plugin to play the audio files instead (it uses the same Flash player and will give the users native HTML5 playback on supported browsers). If you experience problems, try deactivating plugins to determine if there is a conflict. 

== Screenshots ==

1. HTML5 video in Safari, Firefox and the Flash fallback
2. The audio players (native and Flash)
3. HTML5 video and audio in Chrome 3.0

== Changelog ==

= 1.8.2 =
* Addressed problems caused by v1.8.1. If you still have problems with 1.8.2, I updated the 'does not recognize the location' in FAQ.
* Added optional *class* parameter applied to the wrapper div and to audio and video tags. Defaults to 'html5audio' and 'html5video'.

= 1.8.1 =
* Fixed a bug where on some server configurations, the Flash fallback video player would not play the file.

= 1.8.0 =
* Added support of FLV videos (works either as simple Flash playback, or as a prefered version of fallback player when H.264/Ogg versions exist)
* Added support of .ogv and .oga extensions in autodetection and manual format setting (.ogg continues to work)

= 1.7.1 =
* mp3 only is now acceptable for audio. See "So what file formats are required?" in FAQ
* Updated the Flash Audio Player to the latest version
* Added "What if the plugin conflicts with another plugin?" to FAQ
* Fixed the compatibility table: Chrome can play MP3 using HTML5
* Added a call to enqueue jQuery (needed if 'avoiding' Firefox)

= 1.6.1 =
* For a video poster, the plugin will attempt to detect a jpg with the same file name and use it when 'poster' is not specified
* Fixed a bug where H.264-only videos would not play in IE8

= 1.6.0 =
* For audio, you now do not need to provide the Ogg files (This uses JavaScript to force Firefox to the Flash fallback.)
* New <code>format</code> option to manually specify available file formats (avoids auto-detection and allows storage anywhere)

= 1.5.1 =
* For video, you now do not need to provide the Ogg Theora files. If only H.264 version is available, Firefox will be served with the Flash fallback.
* Due to the auto-detection involved, video files should be somewhere under your content folder (usually 'wp-content'). UPDATE: Unless you use manually override this (using v.1.6.0).

= 1.4.2 =
* Compatibility with WordPress 2.9 and 2.9.1

= 1.4.1 =
* The width and height of the HTML5 video element is now explicitly set

= 1.4.0 =
* The *autoplay* and *loop* options (if used) are also applied to the Flash fallback audio player
* The plugin accepts relative paths (e.g.`/wp-content/uploads/2009/09/new_world` in addition to `http://soukie.net/wp-content/...`)
* If the path and name to media includes the file extension ('.mp3' etc.), it will be ignored and the media will function properly

= 1.3.0 =
* Fixed a problem with the audio Flash player introduced by incorrect upload of v1.2 (affects some users). Apologies!
* Switched to a three-digit version numbering.

= 1.2 =
* The audio files no longer need to be under the 'uploads' directory but anywhere within your content (usually 'wp-content').
* Updated the way IDs are applied to the fallback Flash players (they use the supplied or generated ID prefixed with 'f-').

= 1.1 =
* Added optional attribute `options` to define the player behavior (default is: show built-in controls, buffer media when page loads but do not start autoplay and do not loop).
* Added support for .wav (WAVE) audio (the preferred source, if present).
* Fixed bug with incorrect IDs of the fallback Flash players.
* Documentation expanded to cover available codecs, Google Chrome 3.0, and options.

= 1.0 =
* Initial version.
