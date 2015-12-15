<?php

/**
 * @package Elk Gist Button
 * @author Joshua Dickerson
 * @copyright (c) 2015 Joshua Dickerson
 * @license WTFPL http://www.wtfpl.net/txt/copying/
 *
 * @version 1.0
 *
 */

if (!defined('ELK'))
	die('No access...');

/**
 * ibc_gist_button
 *
 * - Subs hook, integrate_bbc_codes hook, Called from Subs.php
 * - Used to add[gist][/gist] parsing values
 *
 * @param mixed[] $codes array of codes as defined for parse_bbc
 * @param mixed[] $no_autolink_tags array of no autolink codes as defined for parse_bbc
 */
function ibc_gist_button(&$codes, &$no_autolink_tags)
{
	global $modSettings;

	// Only for when bbc is on
	if (empty($modSettings['enableBBC']))
		return;

	// Make sure the admin has not disabled the gist tag
	if (!empty($modSettings['disabledBBC']))
	{
		foreach (explode(',', $modSettings['disabledBBC']) as $tag)
		{
			if ($tag === 'gist')
				return;
		}
	}

	// All good, lets add our tag info to the parser, this controls how the tag will render
	// with parse_bbc when found in a post
	$codes[] = array(
		'tag' => 'gist',
		'type' => 'unparsed_content',
		'content' => '<script src="https://gist.github.com/$1.js"></script>',
		'validate' => function(&$tag, &$data, $disabled) {
			$data = strtr($data, array('<br />' => ''));
			if (strpos($data, 'http://') !== 0 && strpos($data, 'https://') !== 0)
				$data = 'https://' . $data;
			$data = ltrim(parse_url($data, PHP_URL_PATH), '\/');
		},
	);

	$no_autolink_tags[] = 'gist';
}

/**
 * ibb_gist_button
 *
 * - Editor hook, integrate_bbc_buttons hook, Called from Editor.subs.php
 * - Used to add buttons to the editor menu bar
 *
 * @param mixed[] $bbc_tags
 */
function ibb_gist_button(&$bbc_tags)
{
	global $context;

	// This is the group we intend to modify
	$where = $bbc_tags['row2'][0];

	// And here we insert the new value after code
	$bbc_tags['row2'][0] = elk_array_insert($where, 'code', array('gist'), 'after', false);

	// Add the javascript, this tells the editor what to do with the new button
	loadJavascriptFile('GistButton.js', array(), 'GistButton');

	// We need to supply the css for the button image, here we use a data-url to save an image call
	$context['html_headers'] .= '<style>.sceditor-button-gist div {background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAVpJREFUeNqM0s0rRGEUx/F7x0RKxob4A6bZKBYWFkLZqIkkC7FUsrCwoCxsZcN/IFmIP4E9ZWnyurBR3krZeH8b1/dMv5vTpDue+szzzL33nJ5znieIoihIGCGmMIt0+ctSbIUETbhHEbm/EqSD5PGOC2TwgHo04xaPv9tIHhbUoPUMXjAcx4aln9BKDcYxgRR20IJNDKEO69hCFie2JnYx3sGYJcQ5jrU2PTjEDbpwpeeXWPZN3NOLnLb8hm1UoaBAG3P6btR26pt4rblDDarRs6KOMh7fmr/idZxgAW3Y0H/r/IqCfYKU5o/yB1b7kY5tGp04Uwmh++5Vcx59PoGNWtV3pznQXK2SbLf76s8kVv09yLpGRro0SwoawIgrt1fNzPtT2FVd/WjVCdiL9qQb5k8ho3Ia8eTKea50TeMd2LZOXQmfmP9PrL/K3RjURTrAmk4lMcGPAAMAEvmJGW+ZZPAAAAAASUVORK5CYII=)}</style>';
}