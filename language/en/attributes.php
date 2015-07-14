<?php
//
//	file: language/en/mods/attributes.php
//	author: abdev
//	begin: 05/03/2008
//	version: 0.1.7 - 08/12/2012
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
	'QTE_ATTRIBUTES' => 'Topic attributes',
	'QTE_ATTRIBUTE' => 'Topic attribute',

	'QTE_ATTRIBUTE_ADD' => 'Select a topic attribute',
	'QTE_ATTRIBUTE_REMOVE' => 'Remove the topic attribute',
	'QTE_ATTRIBUTE_DESIRED' => 'Select desired attribute',
	'QTE_ATTRIBUTE_KEEP' => 'Keep the actual attribute',
	'QTE_ATTRIBUTE_RESTRICT' => 'Forbidden attribute deletion',

	// notifications
	'QTE_ATTRIBUTE_ADDED' => 'An attribute has been applied to the topic title',
	'QTE_ATTRIBUTE_UPDATED' => 'The attribute of that topic has been updated',
	'QTE_ATTRIBUTE_REMOVED' => 'The topic attribute has been removed',

	'QTE_TOPIC_ATTRIBUTE_ADDED' => 'An attribute has been applied to the selected topic',
	'QTE_TOPICS_ATTRIBUTE_ADDED' => 'An attribute has been applied to the selected topics',
	'QTE_TOPIC_ATTRIBUTE_UPDATED' => 'The attribute of the selected topic has been updated',
	'QTE_TOPICS_ATTRIBUTE_UPDATED' => 'The attribute of the selected topics has been updated',
	'QTE_TOPIC_ATTRIBUTE_REMOVED' => 'The attribute of the selected topic has been removed',
	'QTE_TOPICS_ATTRIBUTE_REMOVED' => 'The attribute of the selected topics has been removed',

	// search
	'QTE_ATTRIBUTE_SELECT' => 'Select a topic attribute',
	'QTE_ATTRIBUTE_SEARCH' => 'Search for attributes',
	'QTE_ATTRIBUTE_SEARCH_EXPLAIN' => 'Select the attribute you wish to search',

	// sort
	'QTE_SORT' => 'As attribute',
	'QTE_ALL' => 'All',

	// mistake messages
	'QTE_ATTRIBUTE_UNSELECTED' => 'You must select an attribute!',
));

// topic attributes as keys
$lang = array_merge($lang, array(
	'QTE_SOLVED' => '[Solved by %mod% :: %date%]',
	'QTE_CANCELLED' => 'Cancelled',
));
