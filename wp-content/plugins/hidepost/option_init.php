<?php
$hidepost_content_text = 'Please %login% or %register% to read the rest of this content.';
$hidepost_role_text = 'You must be a(n) %role% to view this content';
$hidepost_link_text = 'Please %login% or %register% to see the link.';
$hidepost_content_text_hide = 0;
$hidepost_link_text_hide = 0;
$hidepost_role_text_hide = 0;
$hidepost_hide_content = 0;
$hidepost_hide_link = 0;
$hidepost_disable_notice = 0;

//My replaced var,
if (get_option('hidepost_content_text')) $hidepost_content_text = trim(get_option('hidepost_content_text'));
if (get_option('hidepost_link_text')) $hidepost_link_text = trim(get_option('hidepost_link_text'));
if (get_option('hidepost_role_text')) $hidepost_role_text = trim(get_option('hidepost_role_text'));
if (get_option('hidepost_hide_link')) $hidepost_hide_link = get_option('hidepost_hide_link');
if (get_option('hidepost_hide_content')) $hidepost_hide_content = get_option('hidepost_hide_content');
if (get_option('hidepost_content_text_hide')) $hidepost_content_text_hide = get_option('hidepost_content_text_hide');
if (get_option('hidepost_link_text_hide')) $hidepost_link_text_hide = get_option('hidepost_link_text_hide');
if (get_option('hidepost_role_text_hide')) $hidepost_role_text_hide = get_option('hidepost_role_text_hide');
if (get_option('hidepost_disable_notice')) $hidepost_disable_notice = get_option('hidepost_disable_notice');
?>