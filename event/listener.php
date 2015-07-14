<?php
//
//	file: ext/abdev/qte/event/listener.php
//	author: abdev
//	author: pastisd
//	begin: 08/19/2014
//	version: 0.0.1 - 08/19/2014
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// essential
namespace abdev\qte\event;

// ignore
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions' => 'add_permission',
		);
	}

	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions += array(
			// ACP
			'acl_a_attr_manage' => array('lang' => 'ACL_A_ATTR_MANAGE', 'cat' => 'posting'),
		);
		$event['permissions'] = $permissions;
	}
}
