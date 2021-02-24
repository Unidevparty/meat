<?php

namespace App;
use App\Role;
class Member {
	const ADMIN_GROUP_IDS = [
        4 => 'Администратор',
        36 => 'Редактор',
        13 => 'Newsmaker',
        32 => 'Human Resources',
        37 => 'Главный редактор',
    ];
	const MEMBERS_GROUP_ID = 3;
	const MODERATORS_GROUP_ID = 6;
	const GUEST_GROUP_ID = 2;
    

    private static $_instance = null;

	public function __construct() {
        //dd(\IPS\Member::reportCount());
        //$member = 
        //
        //
        //data['notification_cnt']
        //data['notification_cnt']
        

		$ips_member = \IPS\Member::loggedIn()->apiOutput();

        // dd(
        //     \IPS\Member::load($ips_member['id'])->warnings(100),
        //     \IPS\Member::load($ips_member['id'])->reportCount()
        // );

		if (!empty($ips_member['id'])) {
			foreach ($ips_member as $key => $value) {
				$this->$key = $value;
			}
		}
	}
    public static function get()
    {
    	$instance = self::getInstance();
    	if (!empty($instance->id)) {
    		return $instance;
    	}

    	return null;
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getAdminRoles() {
        return Role::where('role', $this->primaryGroup['id'])
                    ->where('value', '1')
                    ->pluck('rule');
    }

    public function hasRule($rule) {
        $role = Role::where('role', $this->primaryGroup['id'])
                    ->where('rule', $rule)
                    ->where('value', '1')
                    ->first();

        if ($role) {
            return true;
        }

        return false;
    }

    public function getAdminRules() {
        $ids = self::ADMIN_GROUP_IDS;
        return isset($ids[ $this->primaryGroup['id'] ]) ? $ids[ $this->primaryGroup['id'] ] : '';
    }

    public function is_admin() {
        $ids = self::ADMIN_GROUP_IDS;
    	return isset($ids[ $this->primaryGroup['id'] ]);
    }

    public function is_member() {
    	return $this->primaryGroup['id'] == self::MEMBERS_GROUP_ID;
    }

    public function is_moderator() {
    	return $this->primaryGroup['id'] == self::MODERATORS_GROUP_ID;
    }

    public function is_guest() {
    	return $this->primaryGroup['id'] == self::GUEST_GROUP_ID;
    }
}