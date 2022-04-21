<?php

define('rootDirectory', '/' . 'maktab/messenger/');


$tmp = str_replace($_SERVER['QUERY_STRING'], '0', $_SERVER['REQUEST_URI']);
$tmp1 = substr($tmp, 0, strrpos($tmp, '/') + 1);
define('main_url', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . rootDirectory);

define('db_path', 'db/users_data.txt');
define('group_db_path', 'db/groups/');


// create db directories if it doesn't exist for save user information
if (!file_exists('db')) {
    mkdir('db');
}
if (!file_exists(db_path)) {
    file_put_contents(db_path, '');
}

// create groups directory
if (!file_exists(group_db_path)) {
    mkdir(group_db_path);
}



// create default group
$target_path = 'db/groupsDetails.txt';
// create a file to set groups information in db
if (!file_exists($target_path)) {
    file_put_contents($target_path, '');
}

$groups = file_get_contents($target_path);
$groups = json_decode($groups, true);

// generate id for groups
if (!isset($groups['1111'])) {
    $groupID = '1111';


    // read data from database
    $data = file_get_contents(db_path);
    $users_data = json_decode($data, true); // data stored in json format in database so use decode

    // add user information to array of data
    $users_data['admin'] = [
        'id' => 1111,
        'username' => 'admin',
        'name' => 'admin',
        'email' => 'admin@admin.com',
        'password' => sha1('admin'),
        'groups' => [1111], //default group for all of user
    ];



    $json_users_data = json_encode($users_data); // encode data in json format 
    file_put_contents(db_path, $json_users_data);  // write dat to database



    // set new group data to db
    $groups[$groupID] = ['name' => 'maktab', 'admin' => 'admin'];
    $groups = json_encode($groups);
    file_put_contents($target_path, $groups);

    // generate directory and require data for group
    if (!file_exists('db/groups/' . $groupID)) {
        mkdir('db/groups/' . $groupID);
        file_put_contents('db/groups/' . $groupID . '/messages.txt', '{}');
        mkdir('db/groups/' . $groupID . '/image');

        $log = [
            'groupName' => 'maktab',
            'admins' => [1111],
            'members' => [1111],
            'blocks' => [],
            'usersCount' => 1,
            'avatar' => ''
        ];

        file_put_contents('db/groups/' . $groupID . '/log.txt', json_encode($log));
    }
}


foreach (glob('controllers/*.php') as $file) {
    include_once($file);
}

session_start();

include_once('controllers/page_loader.php');

load_module();
