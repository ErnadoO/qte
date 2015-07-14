<?php
//
//	file: db_rc3_update.php
//	author: abdev
//	begin: 11/23/2010
//	version: 0.0.7 - 10/20/2012
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// ignore
define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
$phpbb_root_path = defined('PHPBB_ROOT_PATH') ? PHPBB_ROOT_PATH : './';
include $phpbb_root_path . 'common.' . $phpEx;

// session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// auth check
if ( $user->data['user_type'] != USER_FOUNDER )
{
	trigger_error('NOT_AUTHORISED');
}

if ( empty($config['qte_version']) || !isset($config['qte_version']) )
{
	// include db_tools
	include $phpbb_root_path . 'includes/db/db_tools.' . $phpEx;

	// perform schema changes
	$db_tools = new phpbb_db_tools($db);

	// remove this field, bye !
	$db_tools->sql_column_remove(FORUMS_TABLE, 'attributes_selected');

	// add these ones !
	$db_tools->sql_column_add(TOPICS_TABLE, 'topic_attr_id', array('UINT', 0));
	$db_tools->sql_column_add(TOPICS_TABLE, 'topic_attr_user', array('UINT', 0));
	$db_tools->sql_column_add(TOPICS_TABLE, 'topic_attr_time', array('TIMESTAMP', 0));

	// add these ones too !!
	$db_tools->sql_column_add(TOPICS_ATTR_TABLE, 'allowed_forums', array('TEXT', ''));
	$db_tools->sql_column_add(TOPICS_ATTR_TABLE, 'allowed_groups', array('TEXT', ''));

	// record now each value in its own field
	$sql = 'SELECT topic_id, topic_attribute FROM ' . TOPICS_TABLE;
	$result = $db->sql_query($sql);

	while ( $row = $db->sql_fetchrow($result) )
	{
		if ( !empty($row['topic_attribute']) )
		{
			list($attr_id, $user_id, $timestamp) = explode(',', $row['topic_attribute']);

			$fields = array(
				'topic_attr_id' => $attr_id,
				'topic_attr_user' => $user_id,
				'topic_attr_time' => $timestamp,
			);

			$sql = 'UPDATE ' . TOPICS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $fields) . '
				WHERE topic_id = ' . (int) $row['topic_id'];
			$db->sql_query($sql);
		}
	}

	// so, remove this field when the loop is finished
	$db_tools->sql_column_remove(TOPICS_TABLE, 'topic_attribute');

	// rename the value and set the permission
	$sql = 'UPDATE ' . MODULES_TABLE . '
		SET module_langname = \'QTE_MANAGE_TITLE\', module_auth = \'acl_a_attr_manage\'
		WHERE module_langname = \'ACP_MANAGE_ATTRIBUTES\'';
	$db->sql_query($sql);

	// add the permission
	include $phpbb_root_path . 'includes/acp/auth.' . $phpEx;
	$auth_admin = new auth_admin();

	$auth_admin->acl_add_option(array(
		'global' => array('a_attr_manage'),
	));

	// set version
	set_config('qte_version', '1.0.0-rc3');

	$message = 'QTE_RC2_UPDATED';
}
else
{
	$message = 'QTE_RC2_GREATER';
}

// load language
$user->add_lang('mods/info_acp_attributes');

// confirm
trigger_error($user->lang[$message]);
