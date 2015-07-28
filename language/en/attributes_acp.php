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
	'QTE_ADD' => 'Add a new attribute',
	'QTE_ADD_EXPLAIN' => 'Here you can define the new attribute fields.',
	'QTE_EDIT' => 'Edit attribute',
	'QTE_EDIT_EXPLAIN' => 'Here you can modify the fields of the selected attribute.',

	'QTE_FIELDS' => 'Attribute fields',
	'QTE_TYPE' => 'Attribute type',
	'QTE_TYPE_TXT' => 'Text',
	'QTE_TYPE_IMG' => 'Image',
	'QTE_NAME' => 'Attribute name',
	'QTE_NAME_EXPLAIN' => '- Use language constant if name is served from language file, or enter directly the attribute name.<br />- Insert <strong>%%mod%%</strong> will display the user name who applied the attribute.<br />- Insert <strong>%%date%%</strong> will display the day date when the attribute was applied.<br /><br />- Example : <strong>[Solved by %%mod%%]</strong> will display <strong>[Solved by %s]</strong>',
	'QTE_DESC' => 'Attribute description',
	'QTE_DESC_EXPLAIN' => 'You can enter a short comment, which will be used in order to differentiate your attributes if some need to have the same name.',
	'QTE_IMG' => 'Attribute image',
	'QTE_IMG_EXPLAIN' => 'You can use the image name if it is served in the imageset, or seize the relative path of the image.',
	'QTE_DATE' => 'Attribute date format',
	'QTE_DATE_EXPLAIN' => 'The syntax used is identical to the PHP <a href="http://www.php.net/date">date()</a> function.',
	'QTE_COLOUR' => 'Attribute colour',
	'QTE_COLOUR_EXPLAIN' => 'Select a value from the <strong>colour picker</strong>, or enter it directly.',
	'QTE_USER_COLOUR' => 'Colour the username, who applied the attribute',
	'QTE_USER_COLOUR_EXPLAIN' => 'If you use the <strong>%mod%</strong> argument and that option is enabled, the user group colour will be applied.',
	'QTE_COPY_AUTHS' => 'Copy permissions from',
	'QTE_COPY_AUTHS_EXPLAIN' => 'If you choose to copy permissions, the attribute will have the same permissions as the one you select here. This will overwrite any permissions you have previously set for this attribute with the permissions of the attribute you select here. If the <strong>Custom</strong> option is selected, the current permissions will be kept.',

	'QTE_PERMISSIONS' => 'Attribute permissions',
	'QTE_ALLOWED_FORUMS' => 'Allowed forums',
	'QTE_ALLOWED_FORUMS_EXPLAIN' => 'Forums, where allowed groups can use that attribute.<br />Select multiple forums by holding <samp>CTRL</samp> or <samp>COMMAND</samp> and clicking.',
	'QTE_ALLOWED_GROUPS' => 'Allowed groups',
	'QTE_ALLOWED_GROUPS_EXPLAIN' => 'Groups, which are allowed to use that attribute.<br />Select multiple groups by holding <samp>CTRL</samp> or <samp>COMMAND</samp> and clicking.',
	'QTE_ALLOWED_AUTHOR' => 'Allow the topic author to use that attribute in the selected forums',
	'QTE_ALLOWED_AUTHOR_EXPLAIN' => 'If that option is enabled, the topic author will be able to use that attribute, even if he is not a member of the selected groups.',
	'QTE_COPY_PERMISSIONS' => 'Copy attributes permissions from',
	'QTE_COPY_PERMISSIONS_EXPLAIN' => 'When created, the forum will have the same attributes permissions as the one you selected. If no forum is selected, attributes will not be displayed while their permissions will not have been defined.',

	'QTE_AUTH_ADD' => 'Add a permission',
	'QTE_AUTH_REMOVE' => 'Remove that permission',
	'QTE_AUTH_NO_PERMISSIONS' => 'Do not copy permissions',

	'QTE_ATTRIBUTE' => 'Attribute',
	'QTE_ATTRIBUTES' => 'Attributes',
	'QTE_USAGE' => 'Usage',

	'QTE_CSS' => 'Probably CSS-managed',
	'QTE_NONE' => 'N/A',

	'QTE_MUST_SELECT' => 'You must select an attribute.',
	'QTE_NAME_ERROR' => 'The “Attribute name” field seems to be empty.',
	'QTE_DESC_ERROR' => 'The “Attribute description” field seems to be too long.',
	'QTE_COLOUR_ERROR' => 'The “Attribute colour” field seems to contain a mistake.',
	'QTE_DATE_ARGUMENT_ERROR' => 'You have defined a date format. But you have not defined the <strong>%date%</strong> argument inside your attribute.',
	'QTE_DATE_FORMAT_ERROR' => 'You have defined the <strong>%date%</strong> argument inside your attribute. But you have not defined the date format.',
	'QTE_USER_COLOUR_ERROR' => 'You have enabled which ensures to colour the username. But you have not defined the <strong>%mod%</strong> argument inside your attribute.',
	'QTE_FORUM_ERROR' => 'You cannot specify a category or a forum link.',

	'QTE_ADDED' => 'A new attribute has been added.',
	'QTE_UPDATED' => 'The selected attribute has been updated.',
	'QTE_REMOVED' => 'The selected attribute has been deleted.',

	'QTE_MIGRATIONS_OUTDATED' => 'Your database is not up to date.<br />Please disable and re-enable the extension in order to update it.<br /><br />Database version: %1$s<br />Files version: %2$s',
	'QTE_DEV_WARNING' => 'You currently use a development version of the extension (%s).<br />You shouldn’t use those versions on a production environment.<br />Those versions can contain many unfinished features and security issues or make unstable your board.',
	'QTE_DEV_WARNING_DEV' => 'We could not be liable for eventual data losses or corruptions.',
	'QTE_BETA_WARNING' => 'You currently use an unstable version of the extension (%s).<br />The unstable versions can contain many errors, security issues or make unstable your board.<br />It is highly recommended you don’t use those versions on a production environment.',
));

// forums
$lang = array_merge($lang, array(
	'QTE_TOPICS_ATTR_SETTINGS' => 'Topic attributes settings',

	'QTE_DEFAULT_ATTR' => 'Default attribute of the forum',
	'QTE_DEFAULT_ATTR_EXPLAIN' => 'The selected attribute will be applied when a new topic is created, whatever the user permissions.',
	'QTE_HIDE_ATTR' => 'Hide the remove option',
	'QTE_HIDE_ATTR_EXPLAIN' => 'The selected groups will not see the remove option.',
	'QTE_FORCE_USERS' => 'Force users to apply an attribute to their topic',
	'QTE_FORCE_USERS_EXPLAIN' => 'If enabled, users will have to select an attribute for their topic in that forum.',
));
