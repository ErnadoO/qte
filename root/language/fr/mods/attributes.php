<?php
//
//	file: language/fr/mods/attributes.php
//	author: abdev
//	begin: 05/03/2008
//	version: 0.1.6 - 08/12/2012
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
	// select
	'QTE_ATTRIBUTES' => 'Attributs de sujet',
	'QTE_ATTRIBUTE' => 'Attribut de sujet',

	'QTE_ATTRIBUTE_ADD' => 'Ajouter un attribut à ce sujet',
	'QTE_ATTRIBUTE_REMOVE' => 'Supprimer l’attribut de ce sujet',
	'QTE_ATTRIBUTE_DESIRED' => 'Sélectionner l’attribut désiré',
	'QTE_ATTRIBUTE_KEEP' => 'Conserver l’attribut actuel',
	'QTE_ATTRIBUTE_RESTRICT' => 'Suppression de l’attribut interdite',

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
	'QTE_ATTRIBUTE_UNSELECTED' => 'Vous devez sélectionner un attribut!',
));

// topic attributes as keys
$lang = array_merge($lang, array(
	'QTE_SOLVED' => '[Réglé par %mod% :: %date%]',
	'QTE_CANCELLED' => 'Annulé',
));
