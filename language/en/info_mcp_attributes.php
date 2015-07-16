<?php
/**
 *
 * @package Quick Title Edition Extension
 * @copyright (c) 2015 ABDev
 * @copyright (c) 2015 PastisD
 * @copyright (c) 2015 Geolim4 <http://geolim4.com>
 * @copyright (c) 2015 Zoddo <zoddo.ino@gmail.com>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

// ignore
if ( !defined('IN_PHPBB') )
{
	exit;
}

// init lang ary, if it doesn't !
if ( empty($lang) || !is_array($lang) )
{
	$lang = array();
}

// moderation
$lang = array_merge($lang, array(
	// logs
	'MCP_ATTRIBUTE_ADDED' => '<strong>Added a new attribute to the topic</strong><br />» %s',
	'MCP_ATTRIBUTE_UPDATED' => '<strong>Updated the attribute for the topic</strong><br />» %s',
	'MCP_ATTRIBUTE_REMOVED'	=> '<strong>Deleted the attribute of the topic</strong><br />» %s',
));
