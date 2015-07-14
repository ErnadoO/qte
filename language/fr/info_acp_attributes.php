<?php
//
//	file: language/fr/mods/info_acp_attributes.php
//	author: abdev
//	begin: 11/22/2010
//	version: 0.1.4 - 12/27/2013
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// ignore
if (!defined('IN_PHPBB'))
{
	exit;
}

// init lang ary, if it doesn't !
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// administration
$lang = array_merge($lang, array(
	'QTE_MANAGE' => 'Gérer les attributs de sujet',
	'QTE_MANAGE_TITLE' => 'Attributs de sujet',
	'QTE_MANAGE_EXPLAIN' => 'Depuis cette page vous pouvez gérer les libellés et les icônes qui seront utilisés en tant qu’attributs de sujet.',

	'LOG_ATTRIBUTE_ADDED' => '<strong>Ajout d’un nouvel attribut</strong><br />» %s',
	'LOG_ATTRIBUTE_UPDATED' => '<strong>Mise à jour d’un attribut</strong><br />» %s',
	'LOG_ATTRIBUTE_REMOVED'	=> '<strong>Suppression d’un attribut</strong><br />» %s',
	'LOG_ATTRIBUTE_MOVE_DOWN'	=> '<strong>Déplacement d’un attribut</strong> %1$s <strong>en dessous de</strong> %2$s',
	'LOG_ATTRIBUTE_MOVE_UP'	=> '<strong>Déplacement d’un attribut</strong> %1$s <strong>au dessus de</strong> %2$s',
));