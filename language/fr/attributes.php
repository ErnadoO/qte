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
	'QTE_CAN_USE_ATTR'	=> 'Peut utiliser l’attribut %s',

	// select
	'QTE_ATTRIBUTES' => 'Attributs de sujet',
	'QTE_ATTRIBUTE' => 'Attribut de sujet',

	'QTE_ATTRIBUTE_ADD' => 'Ajouter un attribut à ce sujet',
	'QTE_ATTRIBUTE_REMOVE' => 'Supprimer l’attribut de ce sujet',
	'QTE_ATTRIBUTE_DESIRED' => 'Sélectionner l’attribut désiré',
	'QTE_ATTRIBUTE_KEEP' => 'Conserver l’attribut actuel',

	// notifications
	'QTE_ATTRIBUTE_ADDED' => 'Un attribut a été appliqué au titre du sujet',
	'QTE_ATTRIBUTE_UPDATED' => 'L’attribut de ce sujet a été mis à jour',
	'QTE_ATTRIBUTE_REMOVED' => 'L’attribut du sujet a été supprimé',

	'QTE_TOPIC_ATTRIBUTE_ADDED' => 'Un attribut a été appliqué au sujet sélectionné',
	'QTE_TOPICS_ATTRIBUTE_ADDED' => 'Un attribut a été appliqué aux sujets sélectionnés',
	'QTE_TOPIC_ATTRIBUTE_UPDATED' => 'L’attribut du sujet sélectionné a été mis à jour',
	'QTE_TOPICS_ATTRIBUTE_UPDATED' => 'L’attribut des sujets sélectionnés a été mis à jour',
	'QTE_TOPIC_ATTRIBUTE_REMOVED' => 'L’attribut du sujet sélectionné a été supprimé',
	'QTE_TOPICS_ATTRIBUTE_REMOVED' => 'L’attribut des sujets sélectionnés a été supprimé',

	// search
	'QTE_ATTRIBUTE_SELECT' => 'Sélectionner un attribut',
	'QTE_ATTRIBUTE_SEARCH' => 'Recherche par attribut',
	'QTE_ATTRIBUTE_SEARCH_EXPLAIN' => 'Sélectionnez l’attribut que vous souhaitez rechercher',

	// sort
	'QTE_SORT' => 'Selon l’attribut',
	'QTE_ALL' => 'Tous',

	// mistake messages
	'QTE_ATTRIBUTE_UNSELECTED' => 'Vous devez sélectionner un attribut !',

	// placeholders
	'QTE_KEY_USERNAME' => '¦utilisateur¦',
	'QTE_KEY_DATE' => '¦date¦',
));

// topic attributes as keys
$lang = array_merge($lang, array(
	'QTE_SOLVED' => '[Réglé par %mod% :: %date%]',
	'QTE_CANCELLED' => 'Annulé',
));
