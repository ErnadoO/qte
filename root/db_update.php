<?php
//
//	file: db_update.php
//	author: abdev
//	begin: 11/23/2010
//	version: 0.1.7 - 12/27/2013
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// ignore
define('IN_PHPBB', true);
define('UMIL_AUTO', true);
$phpbb_root_path = defined('PHPBB_ROOT_PATH') ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include $phpbb_root_path . 'common.' . $phpEx;

// start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if ( !file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx) )
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// language file which will be included when installing
$language_file = 'mods/info_acp_attributes';

// name of the mod
$mod_name = 'QTE';

// name of the config variable
$version_config_name = 'qte_version';

// logo image
$logo_img = 'images/qte_logo_small.png';

// array of versions and actions within each
$versions = array(
	'1.2.5' => array(),

	'1.2.2' => array(),

	'1.2.1' => array(),

	'1.2.0' => array(
		'table_column_add' => array(
			array('phpbb_forums', 'default_attr', array('UINT', 0)),
			array('phpbb_forums', 'hide_attr', array('TEXT', '')),
		),

		'cache_purge' => array('', 'auth', 'imageset', 'template', 'theme'),
	),

	'1.1.1' => array(),

	'1.1.0' => array(

		'table_column_update' => array(
			array('phpbb_topics_attr', 'attr_desc', array('VCHAR:60', '')),
		),

	),

	'1.0.0' => array(),

	'1.0.0-rc6' => array(

		'config_remove' => array(
			array('qte_force_users'),
		),

		'table_column_add' => array(
			array('phpbb_forums', 'force_attr', array('BOOL', 0)),
		),

	),

	'1.0.0-rc5' => array(),

	'1.0.0-rc4' => array(

		'table_column_remove' => array(
			array('phpbb_topics_attr', 'allowed_forums'),
			array('phpbb_topics_attr', 'allowed_groups'),
		),

		'table_column_add' => array(
			array('phpbb_topics_attr', 'attr_desc', array('VCHAR', '')),
			array('phpbb_topics_attr', 'attr_auths', array('MTEXT', '')),
		),

	),

	'1.0.0-rc3' => array(

		'permission_add' => array(
			array('a_attr_manage', true),
		),

		'permission_set' => array(
			array('ROLE_ADMIN_STANDARD', 'a_attr_manage'),
			array('ROLE_ADMIN_FORUM', 'a_attr_manage'),
			array('ROLE_ADMIN_FULL', 'a_attr_manage'),
		),

		'config_add' => array(
			array('qte_force_users', false),
		),

		'table_add' => array(
			array('phpbb_topics_attr', array(
				'COLUMNS' => array(
					'attr_id' => array('UINT', NULL, 'auto_increment'),
					'attr_type' => array('BOOL', 0),
					'attr_name' => array('VCHAR', ''),
					'left_id' => array('UINT', 0),
					'right_id' => array('UINT', 0),
					'attr_img' => array('VCHAR', ''),
					'attr_date' => array('VCHAR:30', ''),
					'attr_colour' => array('VCHAR:6', ''),
					'attr_user_colour' => array('BOOL', 0),
					'allowed_forums' => array('TEXT', ''),
					'allowed_groups' => array('TEXT', ''),
				),
				'PRIMARY_KEY'	=> 'attr_id',
			)),
		),

		'table_column_add' => array(
			array('phpbb_topics', 'topic_attr_id', array('UINT', 0)),
			array('phpbb_topics', 'topic_attr_user', array('UINT', 0)),
			array('phpbb_topics', 'topic_attr_time', array('TIMESTAMP', 0)),
		),

		'module_add' => array(
			array('acp', 'ACP_MESSAGES', array(
				'module_basename' => 'attributes',
				'modes' => array('manage'),
				'module_auth' => 'acl_a_attr_manage',
				'after' => 'ACP_MESSAGE_SETTINGS',
			)),
		),

	),
);

// include the UMIL Auto file, it handles the rest
include $phpbb_root_path . 'umil/umil_auto.' . $phpEx;
