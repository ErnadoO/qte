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