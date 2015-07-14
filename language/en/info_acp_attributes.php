<?php
//
//	file: language/en/mods/info_acp_attributes.php
//	author: abdev
//	begin: 11/22/2010
//	version: 0.1.4 - 12/27/2013
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

// administration
$lang = array_merge($lang, array(
	'QTE_MANAGE' => 'Manage topic attributes',
	'QTE_MANAGE_TITLE' => 'Topic attributes',
	'QTE_MANAGE_EXPLAIN' => 'Here you can manage the wordings and the icons used as topic attributes.',

	'LOG_ATTRIBUTE_ADDED' => '<strong>Added a new attribute</strong><br />» %s',
	'LOG_ATTRIBUTE_UPDATED' => '<strong>Updated an attribute</strong><br />» %s',
	'LOG_ATTRIBUTE_REMOVED'	=> '<strong>Deleted an attribute</strong><br />» %s',
	'LOG_ATTRIBUTE_MOVE_DOWN'	=> '<strong>Moved an attribute</strong> %1$s <strong>below</strong> %2$s',
	'LOG_ATTRIBUTE_MOVE_UP'	=> '<strong>Moved an attribute</strong> %1$s <strong>above</strong> %2$s',
));