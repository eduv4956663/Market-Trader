<?php

require_once 'db.php';

class user {

    public static function has_session($skip_ban_check = false): bool {
	if (empty($_COOKIE['session_cookie'])) {
	    return false;
	}

	$session_cookie = $_COOKIE['session_cookie'];

	$sess_row = database::fetch("SELECT * FROM sessions WHERE session_token = ?", [$session_cookie]);
	if ($sess_row === false)
	    return false;

	$user = database::fetch("SELECT * FROM users WHERE id = ?", [$sess_row['user_id']]);

	if ($skip_ban_check === false) {

	    if ($user['is_banned'] === '1') {
		response::redirect('banpage.php');
	    }
	}

	$should_remember = $sess_row['remember_session'] === 1;

	if ($should_remember) {
	    return true;
	}


	if ((time() - strtotime($sess_row['created_at'])) > time::HOUR * 8) {
	    return false;
	}

	return true;
    }

    public static function get_session_token(): mixed {
	return helper::fetch_global($_COOKIE, 'session_cookie');
    }

    public static function get_user_id(): mixed {
	$token = self::get_session_token();

	if ($token === false) {
	    return false;
	}

	$session_row = database::fetch("SELECT * FROM sessions WHERE session_token = ?", [$token]);

	if ($session_row !== false) {
	    return $session_row['user_id'];
	}

	return false;
    }

    public static function revoke_session(): void {
	$token = self::get_session_token();
	database::execute_arr("DELETE FROM sessions WHERE session_token = ?", [$token]);
    }

    public static function get_role(): mixed {
	$user_id = self::get_user_id();

	if ($user_id === false) {
	    response::redirect('/pages/login.php');
	}

	$response = database::fetch("SELECT role FROM users WHERE id = ?", [$user_id]);

	if ($response === false)
	    return false;

	return $response['role'];
    }
}
