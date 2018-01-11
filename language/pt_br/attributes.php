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
    'QTE_CAN_USE_ATTR'	=> 'Pode usar o atributo %s',

	// select
	'QTE_ATTRIBUTES' => 'Atributos do tópico',
	'QTE_ATTRIBUTE' => 'Atributo do tópico',

	'QTE_ATTRIBUTE_ADD' => 'Selecione um atributo de tópico',
	'QTE_ATTRIBUTE_REMOVE' => 'Remover o atributo do tópico',
	'QTE_ATTRIBUTE_DESIRED' => 'Selecionar o atributo desejado',
	'QTE_ATTRIBUTE_KEEP' => 'Manter o atributo atual',

	// notifications
	'QTE_ATTRIBUTE_ADDED' => 'Um atributo foi aplicado ao título do tópico',
	'QTE_ATTRIBUTE_UPDATED' => 'O atributo do tópico foi atualizado',
	'QTE_ATTRIBUTE_REMOVED' => 'O atributo de tópico foi removido',

	'QTE_TOPIC_ATTRIBUTE_ADDED' => 'Um atributo foi aplicado ao tópico selecionado',
	'QTE_TOPICS_ATTRIBUTE_ADDED' => 'Um atributo foi aplicado aos tópicos selecionados',
	'QTE_TOPIC_ATTRIBUTE_UPDATED' => 'O atributo do tópico selecionado foi atualizado',
	'QTE_TOPICS_ATTRIBUTE_UPDATED' => 'O atributo dos tópicos selecionados foi atualizado',
	'QTE_TOPIC_ATTRIBUTE_REMOVED' => 'O atributo do tópico selecionado foi removido',
	'QTE_TOPICS_ATTRIBUTE_REMOVED' => 'O atributo dos tópicos selecionados foi removido',

	// search
	'QTE_ATTRIBUTE_SELECT' => 'Selecionar um atributo de tópico',
	'QTE_ATTRIBUTE_SEARCH' => 'Pesquisar atributos',
	'QTE_ATTRIBUTE_SEARCH_EXPLAIN' => 'Selecione o atributo que você deseja pesquisar',

	// sort
	'QTE_SORT' => 'Como atributo',
	'QTE_ALL' => 'Todos',

	// mistake messages
	'QTE_ATTRIBUTE_UNSELECTED' => 'Você deve selecionar um atributo!',

	// placeholders
	'QTE_KEY_USERNAME' => '¦user¦',
	'QTE_KEY_DATE' => '¦date¦',
));

// topic attributes as keys
$lang = array_merge($lang, array(
	'QTE_SOLVED' => '[Resolvido por %mod% :: %date%]',
	'QTE_CANCELLED' => 'Cancelado',
));
