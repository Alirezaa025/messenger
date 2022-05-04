<?php

/**
 * create a new group
 */
function createGroup($groupName, $admin)
{
    $target_path = 'db/groupsDetails.txt';
    // create a file to set groups information in db
    if (!file_exists($target_path)) {
        file_put_contents($target_path, '');
    }

    $groups = file_get_contents($target_path);
    $groups = json_decode($groups, true);

    // generate id for groups
    do {
        $groupID = rand(1112, 9999);
        if (!is_array($groups)) {
            break;
        }
    } while (array_key_exists($groupID, $groups));

    // set new group data to db
    $groups[$groupID] = ['name' => $groupName, 'admin' => $admin];
    $groups = json_encode($groups);
    file_put_contents($target_path, $groups);

    // add new group to list of creator groups
    if (!addGroupToUser($admin, $groupID)) {
        return false;
    }

    // generate directory and require data for group
    if (!file_exists('db/groups/' . $groupID)) {
        mkdir('db/groups/' . $groupID);
        file_put_contents('db/groups/' . $groupID . '/messages.txt', '{}');
        mkdir('db/groups/' . $groupID . '/image');

        $log = [
            'groupName' => $groupName,
            'admins' => [$admin],
            'members' => [$admin],
            'blocks' => [],
            'usersCount' => 1,
            'avatar' => ''
        ];

        file_put_contents('db/groups/' . $groupID . '/log.txt', json_encode($log));

        return true;
    } else {
        return false;
    }
}



/**
 * read abstract groups details 
 */
function abstractGroup($groupID)
{
    if (dbType == 'file') {
        $target = "db/groups/$groupID/log.txt";

        if (!file_exists($target)) {
            return false;
        }

        $abstract = file_get_contents($target);
        $abstract = json_decode($abstract, true);

        $messageDetails = file_get_contents("db/groups/$groupID/messages.txt");
        $messageDetails = json_decode($messageDetails, true);

        return [
            'groupName' => $abstract['groupName'],
            'avatar' => $abstract['avatar'],
            'usersCount' => $abstract['usersCount'],
            'admins' => $abstract['admins'],
            'members' => $abstract['members'],
            'blocks' => $abstract['blocks'],
            'lastMessageId' => is_array($messageDetails) && !empty($messageDetails) ? key(array_slice($messageDetails, -1, 1, true)) : '',
            'lastMessageUser' => is_array($messageDetails) && !empty($messageDetails) ? findUsername(end($messageDetails)['userID']) : '(',
            'lastMessage' => is_array($messageDetails) && !empty($messageDetails) ? end($messageDetails)['message'] : 'Empty',
            'lastMessageTime' => is_array($messageDetails) && !empty($messageDetails) ? end($messageDetails)['time'] : '',
            'lastMessageType' => is_array($messageDetails) && !empty($messageDetails) ? (end($messageDetails)['type'] == 'image' ? 'image' : '') : '',
        ];
    } elseif (dbType == 'mysql') {
        $connInstance = MySqlDatabaseConnection::getInstance();
        $conn = $connInstance->getConnection();

        $query = 'SELECT * FROM `groups` WHERE `group_id` = ? LIMIT 1';
        $group = $conn->prepare($query);
        $group->execute([$groupID]);

        $query = 'SELECT * FROM `groups` WHERE `group_id` = ? LIMIT 1';
        $group = $conn->prepare($query);
        $group->execute([$groupID]);
        $group = $group->fetch(PDO::FETCH_ASSOC);

        $query = 'SELECT `user_id` FROM `groups_users` WHERE `group_id` = ?';
        $members = $conn->prepare($query);
        $members->execute([$groupID]);
        $members = $members->fetch(PDO::FETCH_NUM);

        $usersCount = count($members);

        $query = 'SELECT `user_id` FROM `groups_users` WHERE `group_id` = ? AND `is_admin` == true';
        $admins = $conn->prepare($query);
        $admins->execute([$groupID]);
        $admins = $admins->fetch(PDO::FETCH_NUM);

        $query = 'SELECT `user_id` FROM `groups_users` WHERE `group_id` = ? AND `is_block` == true';
        $blocks = $conn->prepare($query);
        $blocks->execute([$groupID]);
        $blocks = $blocks->fetch(PDO::FETCH_NUM);

        $query = 'SELECT * FROM `messages` ORDER BY `message_id` DESC LIMIT 1';
        $lastMessage = $conn->prepare($query);
        $lastMessage->execute([$groupID]);
        $lastMessage = $lastMessage->fetch(PDO::FETCH_NUM);




        return [
            'groupName' => $group['groupName'],
            'avatar' => $group['avatar'],
            'usersCount' => $usersCount,
            'admins' => $admins,
            'members' => $members,
            'blocks' => $blocks,
            // 'lastMessageId' => is_array($messageDetails) && !empty($messageDetails) ? key(array_slice($messageDetails, -1, 1, true)) : '',
            'lastMessageUser' => !$lastMessage ? '(' : findUsername($lastMessage['user_id']),
            'lastMessage'     => $lastMessage ? $lastMessage['message'] : 'Empty',
            'lastMessageTime' => $lastMessage ? $lastMessage['time'] : '',
            'lastMessageType' => $lastMessage ? $lastMessage['type'] : '',
        ];
    }
}

/**
 * block or unblock chosen user in chosen group
 * @param string $groupID
 * @param string $username
 * @param boolean $type false=block true=unblock
 * @return boolean $username
 */
function memberOperator($groupID, $username, $type)
{
    $target = "db/groups/$groupID/log.txt";
    if (!file_exists($target)) {
        return false;
    }

    $group = file_get_contents($target);
    $group = json_decode($group, true);

    if (!$type) {
        array_push($group['blocks'], $username);
    } else {
        $key = array_search($username, $group['blocks']);

        if ($key !== false) {
            unset($group['blocks'][$key]);
        } else {
            return false;
        }
    }

    $group = json_encode($group);
    file_put_contents($target, $group);

    return true;
}


/**
 * messageHash
 * fine hash of group messages
 * @param $groupID
 * @return $hashMessages
 */
function messageHash($groupID)
{
    echo md5_file("../db/groups/$groupID/messages.txt");
}

if (isset($_POST['function']) && $_POST['function'] == 'hashFileJS') {
    messageHash($_POST['groupID']);
}

/**
 * addAdmin
 * add selected member to admins of group
 * @param integer $groupID
 * @param integer $userID
 */
function addAdmin($groupID, $userID)
{
    $targetLog = "db/groups/$groupID/log.txt";
    $log = file_get_contents($targetLog);
    $log = json_decode($log, true);

    array_push($log['admin'], $userID);

    $log = json_encode($log);
    file_put_contents($targetLog, $log);
}

/**
 * adminOperator
 * add member to group admin
 * @param $userID
 * @param $groupID
 * @param $type
 * @return boolean
 */

function adminOperator($userID, $groupID, $type)
{
    $group = file_get_contents("db/groups/$groupID/log.txt");
    $group = json_decode($group, true);

    if ($type == 'add') {
        array_push($group['admins'], $userID);
    } else {
        unset($group['admins'][array_search($userID, $group['admins'])]);
    }


    if (file_put_contents("db/groups/$groupID/log.txt", json_encode($group))) {
        return true;
    }
}
