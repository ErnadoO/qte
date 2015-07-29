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

$lang = array_merge($lang, array(
	// select
	'QTE_ATTRIBUTES' => 'Attributi topic',
	'QTE_ATTRIBUTE' => 'Attributo topic',

	'QTE_ATTRIBUTE_ADD' => 'Seleziona un attributo topic',
	'QTE_ATTRIBUTE_REMOVE' => 'Rimuovi attributo topic',
	'QTE_ATTRIBUTE_DESIRED' => 'Selezionare l’attributo desiderato',
	'QTE_ATTRIBUTE_KEEP' => 'Mantieni attributo corrente',
	'QTE_ATTRIBUTE_RESTRICT' => 'Cancellazione attributo non permessa',

	// notifications
	'QTE_ATTRIBUTE_ADDED' => 'È stato applicato un attributo al titolo del topic',
	'QTE_ATTRIBUTE_UPDATED' => 'L’attributo del topic è stato aggiornato',
	'QTE_ATTRIBUTE_REMOVED' => 'L’attributo del topic è stato rimosso',

	'QTE_TOPIC_ATTRIBUTE_ADDED' => 'È stato applicato un attributo al topic selezionato',
	'QTE_TOPICS_ATTRIBUTE_ADDED' => 'È stato applicato un attributo ai topic selezionati',
	'QTE_TOPIC_ATTRIBUTE_UPDATED' => 'L’attributo del topic selezionato è stato aggiornato',
	'QTE_TOPICS_ATTRIBUTE_UPDATED' => 'L’attributo dei topic selezionati è stato aggiornato',
	'QTE_TOPIC_ATTRIBUTE_REMOVED' => 'L’attributo del topic selezionato è stato rimosso',
	'QTE_TOPICS_ATTRIBUTE_REMOVED' => 'L’attributo dei topic selezionati è stato rimosso',

	// search
	'QTE_ATTRIBUTE_SELECT' => 'Seleziona un attributo topic',
	'QTE_ATTRIBUTE_SEARCH' => 'Cerca per attributi',
	'QTE_ATTRIBUTE_SEARCH_EXPLAIN' => 'Selezionare l’attributo che si desidera cercare',

	// sort
	'QTE_SORT' => 'Per attributo',
	'QTE_ALL' => 'Tutti',

	// mistake messages
	'QTE_ATTRIBUTE_UNSELECTED' => 'È necessario selezionare un attributo!',

	// placeholders
	'QTE_KEY_USERNAME' => '�utente�',
	'QTE_KEY_DATE' => '�data�',
));

// topic attributes as keys
$lang = array_merge($lang, array(
	'QTE_SOLVED' => '[Risolto da %mod% :: %date%]',
	'QTE_CANCELLED' => 'Cancellato',
));
