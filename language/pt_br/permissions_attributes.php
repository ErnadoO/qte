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

$lang = array_merge($lang, array(
	'ACL_A_ATTR_MANAGE'     => 'Pode gerenciar atributos de tópicos',
	'ACL_M_ATTR_DEL'		=> 'Pode deletar atributos',
	'ACL_M_ATTR_EDIT'		=> 'Pode editar atributos',

	'ACL_CAT_QTE'			=> 'QTE',
));
