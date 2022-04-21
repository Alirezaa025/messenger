<?php

/**
 * add message to group messages
 */
function addMessage($message, $groupID, $type = 'text', $userID)
{
    
    if ($type == 'text') {
        $abstract = file_get_contents('../db/groups/' . $groupID . '/log.txt');
        $messages = file_get_contents('../db/groups/' . $groupID . '/messages.txt');
    } else {
        $abstract = file_get_contents('db/groups/' . $groupID . '/log.txt');
        $messages = file_get_contents('db/groups/' . $groupID . '/messages.txt');
    }
    $abstract = json_decode($abstract, true);
    $messages = json_decode($messages, true);
    
    if (in_array($userID, $abstract['blocks']) ) {
        print_r('blocks');
        return;
    }

    $time = date("Y-M-d H:i");

    if (is_array($messages)) {
        array_push($messages, ['userID' => $userID, 'message' => $message, 'time' => $time, 'type' => $type, 'seen' => false]);
    } else {
        $messages[0] = ['userID' => $userID, 'message' => $message, 'time' => $time, 'type' => $type, 'seen' => false];
    }

    $messages = json_encode($messages);

    if ($type == 'text') {
        $messages = file_put_contents('../db/groups/' . $groupID . '/messages.txt', $messages);
    } else {
        $messages = file_put_contents('db/groups/' . $groupID . '/messages.txt', $messages);
    }
}

if (isset($_POST['function']) && $_POST['function'] == 'addMessage') {
    if (strlen($_POST['message']) == 0 || strlen($_POST['message']) > 100) {
        echo false;
    } else {
        addMessage($_POST['message'], $_POST['groupID'], 'text', $_POST['userID']);
    }
}

/**
 * edit selected message
 */
function editMessage($groupID, $messageId, $editedMessage)
{
    $messages = file_get_contents('db/groups/' . $groupID . '/messages.txt');
    $messages = json_decode($messages, true);

    $messages[$messageId]['message'] = $editedMessage;

    $messages = json_encode($messages);

    if (file_put_contents('db/groups/' . $groupID . '/messages.txt', $messages)) {
        return true;
    }
    return false;
}

/**
 * delete selected message from group
 * @param string $groupId,$messageId
 * @return bool
 */
function deleteMessage($groupID, $messageID)
{
    $messages = file_get_contents('db/groups/' . $groupID . '/messages.txt');
    $messages = json_decode($messages, true);

    if (!array_key_exists($messageID, $messages)) {
        return false;
    }

    if ($messages[$messageID]['type'] == 'image') {
        deleteDirectory("db/groups/$groupID/image/" . $messages[$messageID]['message']);
    }

    unset($messages[$messageID]);

    $messages = json_encode($messages);

    if (file_put_contents('db/groups/' . $groupID . '/messages.txt', $messages)) {
        return true;
    }
    return false;
}

/**
 * read message from db and send to chat body
 * @return array [message, message send time]
 */
function readMessages($groupID)
{
    $messages = file_get_contents('db/groups/' . $groupID . '/messages.txt');
    $messages = json_decode($messages, true);

    return $messages;
}

/**
 * upload image to directory of images of group
 * and set data of image on messages database
 * @param string $groupID
 * @param file $image
 * @param string $username|$userId
 * @return boolean
 */
function uploadImage($groupID, $image, $username, $type = 'image')
{
    $format = pathinfo($image['name'], PATHINFO_EXTENSION);

    if ($type == 'avatar') {
        if (!file_exists('db/users_avatar')) {
            mkdir('db/users_avatar');
        }
        $userID = findID($username);
        if (!$userID) {
            add_toast("Something Wrong, try again", 'error');
            return false;
        }

        $targetUpload = "db/users_avatar/$userID.jpg";
        $counter = 0;
        $tmp = $userID;

        while (file_exists("db/users_avatar/$tmp.jpg")) {
            $counter++;
            $tmp = $userID . "_$counter";
            $targetUpload = "db/users_avatar/$tmp.jpg";
        }
        changeAvatar($userID, "db/users_avatar/$tmp.jpg");
    } else {
        do {
            $name = rand(1111, 9999);
            $targetUpload = "db/groups/$groupID/image/$name.$format";
        } while (file_exists("db/groups/$groupID/image/$name.$format"));
    }


    // $imageSize = formatSizeUnits($image['size']);


    if (move_uploaded_file($image["tmp_name"], $targetUpload)) {
        if ($type != 'avatar') {
            addMessage("$name.$format", $groupID, 'image', $_SESSION['user_id']);
        } else {
            add_toast('image add successfully', 'success');
        }
    } else {
        add_toast("Something Wrong, try again", 'error');
    }
}

/**
 * seenMessage
 * all messages sent in group seen element bt true
 * @param integer $groupID
 */
function seenMessage($groupID, $userID)
{
    $messages = file_get_contents('db/groups/' . $groupID . '/messages.txt');
    $messages = json_decode($messages, true);

    foreach ($messages as &$message) {
        if ($message['userID'] != $userID) {
            $message['seen'] = true;
        }
    }

    $messages = json_encode($messages);
    file_put_contents('db/groups/' . $groupID . '/messages.txt', $messages);
}

// load messages oh page
function loadMessages($messages, $rule)
{
    foreach ($messages as $id => $message) { ?>
        <div id="messageBody" class="flex flex-row<?= ($message['userID'] == findID($_SESSION['username'])) ? '-reverse' : '' ?> my-2 ">
            <div class="w-8 h-8 relative flex flex-shrink-0 flex-row-reverse self-stretch my-auto <?= ($message['userID'] == findID($_SESSION['username'])) ? 'ml-4' : 'mr-4' ?>">
                <?php $Avatar = user_exists(findUsername($message['userID'])) ?>
                <?php if (!empty($Avatar['avatar'])) : ?>
                    <img class="shadow-md rounded-full w-full h-full overflow-hidden" src="<?= main_url . $Avatar['avatar'] ?>" alt="">
                <?php else : ?>
                    <div class="bg-pink-500 shadow-md rounded-full w-full h-full object-cover flex justify-center items-center"><?= substr(findUsername($message['userID']), 0, 1) ?></div>
                <?php endif; ?>
            </div>
            <div class="messages text-sm text-gray-700 grid grid-flow-row gap-2">
                <div class="flex <?= ($message['userID'] == findID($_SESSION['username'])) ? 'flex-row-reverse' : '' ?> items-center group overflow-hidden">
                    <p class="break-all <?= $message['type'] != 'image' ? 'rounded-full px-6 py-3' : 'rounded p-1' ?> <?= ($message['userID'] == findID($_SESSION['username'])) ? 'bg-blue-800' : 'bg-gray-800' ?> max-w-xs lg:max-w-md text-gray-200">
                        <?php if ($message['type'] == 'image') : ?>
                            <img onclick="biggerSize(this)" class='cursor-zoom-in relative rounded h-full object-contain' src=<?= main_url . "db/groups/$_POST[groupID]/image/$message[message]" ?>>
                        <?php else : ?>
                            <?= $message['message'] ?>
                        <?php endif ?>
                    </p>
                    <?php if ($message['seen']) : ?>
                        <div class="text-green-500 text-lg self-end">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                                <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z" />
                                <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708z" />
                            </svg>
                        </div>
                    <?php endif; ?>
                    <?php
                    if (($message['userID'] == $_SESSION['user_id']) || $rule == 'admin') {
                        chatButton($id, ($message['userID'] == findID($_SESSION['username'])) ? 'main' : 'other', $message['type']);
                    }
                    ?>
                </div>
            </div>
            <div class="self-end text-gray-600 text-sm <?= ($message['userID'] == findID($_SESSION['username'])) ? ' mr-4' : 'ml-4' ?>"><?= $message['time'] ?></div>
        </div>
    <?php }
}



// load messages oh page
function loadMessagesJS($id, $message, $userID, $rule, $main_url)
{
    include_once('users_functions.php');
    include_once('../view/modules/chatButton.php');
    ?>
    <div id="messageBody" class="flex flex-row<?= ($message['userID'] == $userID) ? '-reverse' : '' ?> my-2">
        <div class="w-8 h-8 relative flex flex-shrink-0 self-stretch my-auto <?= ($message['userID'] == $userID) ? 'ml-4 ' : 'mr-4' ?>">
            <?php $Avatar = user_existsJS(findUsernameJS($message['userID'])) ?>
            <?php if (!empty($Avatar['avatar'])) : ?>
                <img class="shadow-md rounded-full w-full h-full overflow-hidden" src="<?= $main_url . $Avatar['avatar'] ?>" alt="">
            <?php else : ?>
                <div class="bg-pink-500 shadow-md rounded-full w-full h-full object-cover flex justify-center items-center"><?= substr(findUsernameJS($message['userID']), 0, 1) ?></div>
            <?php endif; ?>
        </div>
        <div class="messages text-sm text-gray-700 grid grid-flow-row gap-2">
            <div class="flex <?= ($message['userID'] == $userID) ? 'flex-row-reverse' : '' ?> items-center group overflow-hidden">
                <p class="break-all <?= $message['type'] != 'image' ? 'rounded-full px-6 py-3' : 'rounded p-1' ?> <?= ($message['userID'] == $userID) ? 'bg-blue-800' : 'bg-gray-800' ?> max-w-xs lg:max-w-md text-gray-200">
                    <?php if ($message['type'] == 'image') : ?>
                        <img onclick="biggerSize(this)" class='cursor-zoom-in relative rounded h-full object-contain' src=<?= $main_url . "db/groups/$_POST[groupID]/image/$message[message]" ?>>
                    <?php else : ?>
                        <?= $message['message'] ?>
                    <?php endif ?>
                </p>
                <?php if ($message['seen']) : ?>
                    <div class="self-end text-green-500 text-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                            <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z" />
                            <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708z" />
                        </svg>
                    </div>
                <?php endif; ?>
                <?php
                if (($message['userID'] == $userID) || $rule == 'admin') {
                    chatButton($id, ($message['userID'] == $userID) ? 'main' : 'other', $message['type']);
                }
                ?>
            </div>
        </div>
        <div class="self-end text-gray-600 text-sm <?= ($message['userID'] == $userID) ? 'mr-4' : 'ml-4' ?>"><?= $message['time'] ?></div>
    </div>
<?php
}


/**
 * read message from db and send to chat body realtime
 * @return array [message, message send time]
 */
function readMessagesJS($groupID, $userID, $rule, $main_url)
{
    $messages = file_get_contents("../db/groups/$groupID/messages.txt");
    $messages = json_decode($messages, true);


    foreach ($messages as $id => $message) {
        loadMessagesJS($id, $message, $userID, $rule, $main_url);
    }
}

if (isset($_POST['function']) && $_POST['function'] == 'readMessageJS') {
    readMessagesJS($_POST['groupID'], $_POST['userID'], $_POST['rule'], $_POST['main_url']);
}
