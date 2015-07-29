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

// moderation
$lang = array_merge($lang, array(
	// logs
	'LOG_ATTRIBUTE_ADDED' => '<strong>Nuovo attributo aggiunto</strong><br />» %s',
	'LOG_ATTRIBUTE_UPDATED' => '<strong>Aggiornato attributo esistente</strong><br />» %s',
	'LOG_ATTRIBUTE_REMOVED'	=> '<strong>Rimosso attributo esistente</strong><br />» %s',
	'LOG_ATTRIBUTE_MOVE_DOWN'	=> '<strong>Spostato attributo</strong> %1$s <strong>dopo</strong> %2$s',
	'LOG_ATTRIBUTE_MOVE_UP'	=> '<strong>Spostato attributo</strong> %1$s <strong>prima di</strong> %2$s',

	'MCP_ATTRIBUTE_ADDED' => '<strong>Aggiunto nuovo attributo al topic</strong><br />» %s',
	'MCP_ATTRIBUTE_UPDATED' => '<strong>Aggiornato attributo del topic</strong><br />» %s',
	'MCP_ATTRIBUTE_REMOVED'	=> '<strong>Rimosso attributo del topic</strong><br />» %s',
));
