<?php
//
//	file: adm/mods/qte_version.php
//	author: abdev
//	begin: 12/03/2010
//	version: 0.0.8 - 12/27/2013
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// ignore
if ( !defined('IN_PHPBB') )
{
	exit;
}

class qte_version
{
	function version()
	{
		return array(
			'author' => 'ABDev',
			'title' => 'Quick Title Edition',
			'tag' => 'qte',
			'version' => '1.2.5',
			'file' => array('abdev.biz', 'phpbb', 'mods.xml'),
		);
	}
}
