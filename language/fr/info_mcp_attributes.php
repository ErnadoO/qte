<?php
//
//	file: language/fr/mods/info_mcp_attributes.php
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
	'MCP_ATTRIBUTE_ADDED' => '<strong>Ajout d’un attribut à un sujet</strong><br />» %s',
	'MCP_ATTRIBUTE_UPDATED' => '<strong>Mise à jour de l’attribut du sujet</strong><br />» %s',
	'MCP_ATTRIBUTE_REMOVED'	=> '<strong>Suppression de l’attribut du sujet</strong><br />» %s',
));
