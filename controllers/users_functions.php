<?php

/**
 * check for username exists in the database
 * if exists, return data of username
 * @return array|false
 */
function user_exists($username)
{
    $users_data = file_get_contents(db_path);
    $users_data = json_decode($users_data, true);

    if (!is_array($users_data)) { // if first users requested
        return false;
    }
    if (array_key_exists($username, $users_data)) {
        return
            [
                'id' => $users_data[$username]['id'],
                'username' => $username,
                'name' => $users_data[$username]['name'],
                'email' => $users_data[$username]['email'],
                'password' => $users_data[$username]['password'],
                'avatar' => $users_data[$username]['avatar'] ?? '',
                'groups' => $users_data[$username]['groups'] ?? [],
                'bio' => $users_data[$username]['bio'] ?? '',
            ];
    } else {
        return false;
    }
}

/**
 * user_existsJS
 * check for username exists in the database for ajax
 * if exists, return data of username
 * @return array|false
 */
function user_existsJS($username)
{
    $users_data = file_get_contents('../db/users_data.txt');
    $users_data = json_decode($users_data, true);

    if (!is_array($users_data)) { // if first users requested
        return false;
    }
    if (array_key_exists($username, $users_data)) {
        return
            [
                'id' => $users_data[$username]['id'],
                'username' => $username,
                'name' => $users_data[$username]['name'],
                'email' => $users_data[$username]['email'],
                'password' => $users_data[$username]['password'],
                'avatar' => $users_data[$username]['avatar'] ?? '',
                'groups' => $users_data[$username]['groups'] ?? [],
                'bio' => $users_data[$username]['bio'] ?? '',
            ];
    } else {
        return false;
    }
}


function email_exists($email)
{
    $users_data = file_get_contents(db_path);
    if (!preg_match("/\"$email\"/", $users_data)) {
        return false;
    }
    return true;
}


/**
 * add user information to database
 */
function add_user($name, $username, $email, $password)
{
    // if username already exists do nothing
    if (user_exists($username)) {
        return false;
    }

    // read data from database
    $data = file_get_contents(db_path);
    $users_data = json_decode($data, true); // data stored in json format in database so use decode

    // add user information to array of data
    $users_data[$username] = [
        'id' => rand(1112, 9999),
        'username' => $username,
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'groups' => [1111], //default group for all of user
    ];

    // add user to group log
    addToGroup(1111, $users_data[$username]['id']);

    // create avatar directories if it doesn't exist for save user avatar
    if (!file_exists('db/users_avatar')) {
        mkdir('db/users_avatar');
    }

    set_user_cookie($username);

    set_user_session($users_data[$username]);

    $json_users_data = json_encode($users_data); // encode data in json format 

    if (file_put_contents(db_path, $json_users_data))   // write dat to database
        return true;
    else
        return false;
}

/**
 * addToGroup
 * add user to group log
 * @param integer $groupID
 * @param integer $userID
 * @return boolean
 */
function addToGroup($groupID, $userID)
{
    $group = file_get_contents('db/groups/' . $groupID . '/log.txt');
    $group = json_decode($group, true);

    array_push($group['members'], $userID);
    $group['usersCount']++;

    $group = json_encode($group);
    file_put_contents('db/groups/' . $groupID . '/log.txt', $group);
}


function auth($username = null, $password = null)
{
    // authentication
    if ($username === null or $password === null) { // if empty username or password not authenticated
        return false;
    }

    if (!($user_data = user_exists($username))) { // check if username exists and fetch data
        return false;
    }

    if ($user_data['password'] != $password) { // check password
        return false;
    }

    return true; // true means is user is authenticated
}


/**
 * validation on submitted data at signup
 */
function signup_validation($name, $username, $email, $password)
{
    if (empty($name)) {
        add_toast('name required!', 'error');
        return false;
    } else {
        $pattern = '/[a-z\s]{3,32}/';
        if (!preg_match($pattern, $name)) {
            add_toast('name  must be at least 3 to 32 and contain only alphabetic and space!', 'error');
        }
    }

    if (empty($username)) {
        add_toast('username required!', 'error');
        return false;
    } else {
        $pattern = '/[a-zA-Z\d\s\_]{3,32}/';
        if (!preg_match($pattern, $username)) {
            add_toast('name  must be at least 3 to 32 and contain only alphabetic, number and _ space!', 'error');
        }
    }

    if (empty($email)) {
        add_toast('email required!', 'error');
        return false;
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            add_toast('email invalid!', 'error');
        }
    }

    if (empty($password)) {
        add_toast('password required!', 'error');
        return false;
    } else {
        $pattern = '/.{4,32}/';
        if (!preg_match($pattern, $username)) {
            add_toast('password must be at least 4 to 32 characters!', 'error');
        }
    }
}


function set_user_session($user_data)
{

    $_SESSION['user_id'] = $user_data['id'];
    $_SESSION['username'] = $user_data['username'];
    $_SESSION['password'] = $user_data['password'];
    $_SESSION['groups'] = $user_data['groups'];
}


function set_user_cookie($username)
{
    setcookie('username', $username, time() + 60 * 60 * 24);
}


/**
 * check cookie for username and load user data to session
 */
function check_cookie()
{
    if (!isset($_COOKIE['username'])) {
        return false;
    }

    if (!($user = user_exists($_COOKIE['username']))) {
        return false;
    }

    set_user_session($user);
    return true;
}

/**
 * edit user data in database of users
 */
function editUser($oldUsername, $newUsername, $email = null, $bio = null, $group = null)
{
    $users = file_get_contents('db/users_data.txt');
    $users = json_decode($users, true);

    if (!array_key_exists($oldUsername, $users)) {
        return false;
    }


    if ($oldUsername != $newUsername) {
        $users[$oldUsername]['username'] = $newUsername;
        $users[$newUsername] = $users[$oldUsername];
        unset($users[$oldUsername]);
        $_SESSION['username'] = $newUsername;
        setcookie('username', $newUsername, time() + 60 * 60 * 24);
    }

    if (!empty($email)) {
        $users[$newUsername]['email'] = $email;
    }

    if (!empty($bio)) {
        $users[$newUsername]['bio'] = $bio;
    }

    if (!empty($group)) {
        array_push($users[$newUsername]['groups'], $group);
    }

    $users = json_encode($users);
    file_put_contents(db_path, $users);

    return true;
}


/**
 * read users groups from database
 * @return groups_id
 */
function readUserGroups($username)
{
    if (!($user = user_exists($username))) {
        return false;
    }

    return $user['groups'];
}


/**
 * find user avatar
 * @param string $username
 * @return array $avatarImg
 */
function findAvatar($username)
{
    $images = [];
    $userID = findID($username);
    foreach (glob("db/users_avatar/$userID" . "*.jpg") as $filename) {
        array_push($images, $filename);
    }

    if (empty($images)) {
        return false;
    }
    return $images;
}


/**
 * add group to user's groups in the database of groups
 */
function addGroupToUser($username, $groupID)
{
    if (!$user = user_exists($username)) {
        return false;
    }

    // set group id to user data
    if (!isset($user['groups'])) {
        $user['groups'] = [$groupID];
    } else {
        array_push($user['groups'], $groupID);
    }

    editUser($username, $username, null, null, $groupID);

    return true;
}

/**
 * findID
 * find id of user from username
 * @param string $username
 * @return integer $userId
 */
function findID($username)
{
    $user = user_exists($username);
    if ($user) {
        return $user['id'];
    } else {
        return false;
    }
}


/** 
 * findUsername
 * find username of user from db by id
 * @param integer $userID
 * @return string $username
 */
function findUsername($userID)
{
    $users = file_get_contents('db/users_data.txt');
    $users = json_decode($users, true);
    foreach ($users as $user) {
        if ($user['id'] == $userID) {
            return $user['username'];
        }
    }
}


/** 
 * findUsernameJS
 * find username of user from db by id for ajax
 * @param integer $userID
 * @return string $username
 */
function findUsernameJS($userID)
{
    $users = file_get_contents('../db/users_data.txt');
    $users = json_decode($users, true);
    foreach ($users as $user) {
        if ($user['id'] == $userID) {
            return $user['username'];
        }
    }
}

/**
 * changeAvatar
 * change profile avatar
 * @param integer $user_id
 * @param string $oldAvatarName
 * @param string $newAvatarName
 * @return boolean
 */
function changeAvatar($user_id, $newAvatarName)
{
    $users = file_get_contents('db/users_data.txt');
    $users = json_decode($users, true);

    $username = findUsername($user_id);


    $users[$username]['avatar'] = $newAvatarName;

    $users = json_encode($users);
    file_put_contents('db/users_data.txt', $users);
    return true;
}

/**
 * removeAvatar
 * remove profile avatar
 * @param integer $user_id
 * @param string $selectedAvatar
 * @return boolean
 */
function removeAvatar($selectedAvatar)
{
    if (deleteDirectory($selectedAvatar)) {
        $user = user_exists($_SESSION['username']);
        if ($user['avatar'] == $selectedAvatar) {
            $tmp = json_decode(file_get_contents('db/users_data.txt'), true);
            $tmp[$_SESSION['username']]['avatar'] = '';
            file_put_contents('db/users_data.txt', json_encode($tmp));
        }
        return true;
    }
    return false;
}

/** 
 * findSetAvatar
 * @param array $avatars
 * @return string $setAvatar
 */
function findSetAvatar($avatars, $user_id)
{
    $pattern = "/$user_id\_set.jpg/";
    $setAvatar  = preg_grep($pattern, $avatars);

    if (count($setAvatar) != 1) {
        return false;
    }
    foreach ($setAvatar as $tmp) {
        return $tmp;
    }
}
