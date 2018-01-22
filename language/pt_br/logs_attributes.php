<?php
/**
 *
 * @package Quick Title Edition Extension
 * @copyright (c) 2015 ABDev
 * @copyright (c) 2015 PastisD
 * @copyright (c) 2015 Geolim4 <http://geolim4.com>
 * @copyright (c) 2015 Zoddo <zoddo.ino@gmail.com>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 * Brazilian Portuguese translation by eunaumtenhoid (c) 2017 [ver 2.0.0-a2] (https://github.com/phpBBTraducoes)
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
	'LOG_ATTRIBUTE_ADDED' => '<strong>Adicionado um novo atributo</strong><br />» %s',
	'LOG_ATTRIBUTE_UPDATED' => '<strong>Atualizado um atributo</strong><br />» %s',
	'LOG_ATTRIBUTE_REMOVED'	=> '<strong>Deletado um atributo</strong><br />» %s',
	'LOG_ATTRIBUTE_MOVE_DOWN'	=> '<strong>Moveu um atributo</strong> %1$s <strong>abaixo</strong> %2$s',
	'LOG_ATTRIBUTE_MOVE_UP'	=> '<strong>Moveu um atributo</strong> %1$s <strong>acima</strong> %2$s',

	'MCP_ATTRIBUTE_ADDED' => '<strong>Adicionado um novo atributo ao tópico</strong><br />» %s',
	'MCP_ATTRIBUTE_UPDATED' => '<strong>Atualizado um novo atributo ao tópico</strong><br />» %s',
	'MCP_ATTRIBUTE_REMOVED'	=> '<strong>Deletado um novo atributo ao tópico</strong><br />» %s',
));
