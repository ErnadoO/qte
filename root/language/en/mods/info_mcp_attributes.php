<?php
//
//	file: language/en/mods/info_mcp_attributes.php
//	author: abdev
//	begin: 11/22/2010
//	version: 0.0.2 - 12/02/2010
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

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
