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

$lang = array_merge($lang, array(
	// @TODO : Traduire.
	'ACL_A_ATTR_MANAGE' => 'Peut gérer les attributs de sujet',
));
