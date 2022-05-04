<?php

function get_contents()
{
    global $userDetails;
?>
    <title>Messenger</title>
    <!-- component -->
    <style>
        .group:hover .group-hover\:block {
            display: block;
        }

        .hover\:w-64:hover {
            width: 45%;
        }
    </style>
    </head>

    <body>
        <!-- Messenger Clone -->
        <div class="h-screen w-full flex antialiased text-gray-200 bg-gray-900 overflow-hidden">
            <div class="flex-1 flex flex-col">
                <main class="flex-grow flex flex-row min-h-0">
                    <section class="flex flex-col flex-none overflow-auto w-24 hover:w-64 group lg:max-w-sm md:w-2/5 transition-all duration-300 ease-in-out">
                        <div class="header p-4 flex md:flex-row flex-col justify-between items-center flex-none">
                            <a href="home" class="flex flex-col">
                                <div class="w-16 h-16 relative flex flex-shrink-0 m-auto logo rounded-full">
                                    <img class="rounded-full w-full h-full object-cover brand" alt="" src="<?= main_url ?>asset/image/logo.png" />
                                </div>
                                <p class="text-md font-bold hidden md:block group-hover:block">Messenger</p>
                            </a>
                            <div onclick="modalHandler(true, 'profile')" class="w-16 h-16 md:order-2 rounded-full grid place-items-center hover:-translate-y-1 hover:scale-110 duration-300">
                                <?php if (!empty($userDetails['avatar'])) : ?>
                                    <img class="rounded-full w-full h-full overflow-hidden" src="<?= main_url . $userDetails['avatar'] ?>" alt="">
                                <?php else : ?>
                                    <div class="">
                                        <?= $_SESSION['username'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <a id="createGroup" onclick="modalHandler(true, 'createGroupModal')" class=" w-16 h-16 block rounded-full hover:bg-gray-700 bg-slate-700 p-2 md:order-3 group-hover:block">
                                <i class="fa-solid fa-plus w-full h-full fill-current"></i>
                            </a>
                        </div>
                        <div class="search-box p-4 flex-none">
                            <form onsubmit="">
                                <div class="relative">
                                    <label>
                                        <input class="rounded-full py-2 pr-6 pl-10 w-full border border-gray-800 focus:border-gray-700 bg-slate-700 focus:bg-gray-900 focus:outline-none text-gray-200 focus:shadow-md transition duration-300 ease-in" type="text" value="" placeholder="Search Messenger" />
                                        <span class="absolute top-0 left-0 mt-2 ml-3 inline-block">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </span>
                                    </label>
                                </div>
                            </form>
                        </div>
                        <!-- all contacts container -->
                        <form method="post">
                            <?php $groups = readUserGroups($_SESSION['username']); ?>
                            <?php foreach ($groups as $group) : ?>
                                <?php $details = abstractGroup($group); ?>
                                <button name="groupID" value="<?= $group ?>" class="contacts p-2 flex-1 text-left w-full">
                                    <div class="flex justify-between items-center p-3 hover:bg-slate-700 rounded-lg relative ">
                                        <div class="w-16 h-16 relative flex flex-shrink-0">
                                            <?php if (!empty($details['avatar'])) : ?>
                                                <img class="shadow-md rounded-full w-full h-full object-cover bg-white text-black text-bold" src="<?= main_url ?>db/groups/<?= $group . '/' . $details['avatar'] ?>" alt="" />
                                            <?php else : ?>
                                                <div class="shadow-md rounded-full w-full h-full object-cover bg-white text-pink-500 text-5xl flex justify-center items-baseline"><?= substr($details['groupName'], 0, 1) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-auto min-w-0 ml-4 mr-6 hidden md:block group-hover:block">
                                            <p><?= $details['groupName'] ?></p>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <div class="min-w-0">
                                                    <p class="truncate"><?= $details['lastMessageUser'] . ': ' . ($details['lastMessageType'] == 'image' ? '&#128444; image' : $details['lastMessage']) ?></p>
                                                </div>
                                                <p class="whitespace-no-wrap ml-auto"><?= str_replace(' ', '<br>', $details['lastMessageTime']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            <?php endforeach ?>
                        </form>
                    </section>
                    <!-- chat section -->
                    <section class="flex flex-col flex-auto border-l border-gray-800 ">
                        <?php if (isset($_POST['groupID'])) : ?>

                            <!-- set rule in this group -->
                            <?php
                            global $groupInfo;
                            $admins = $groupInfo['admins'];

                            if (in_array($_SESSION['user_id'], $admins)) {
                                $rule = 'admin';
                            } else {
                                $rule = 'user';
                            }
                            ?>

                            <!-- edit and delete btn -->
                            <?php include_once('chatButton.php') ?>

                            <!-- chat header -->
                            <div class="chat-header px-6 py-4 flex flex-row flex-none justify-between items-center shadow  border-b-2 border-blue-400">
                                <div class="flex">
                                    <div class="w-12 h-12 mr-4 relative flex flex-shrink-0">
                                        <?php if (!empty($details['avatar'])) : ?>
                                            <img class="shadow-md rounded-full w-full h-full object-cover bg-white text-black text-bold" src="<?= main_url ?>db/groups/<?= $group . '/' . $details['avatar'] ?>" alt="" />
                                        <?php else : ?>
                                            <div class="shadow-md rounded-full w-full h-full object-cover bg-white text-pink-500 text-3xl flex justify-center items-baseline"><?= substr($details['groupName'], 0, 1) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-bold"><?= $groupInfo['groupName'] ?></p>
                                        <p><?= $groupInfo['usersCount'] ?> members</p>
                                    </div>
                                </div>

                                <!-- operator -->
                                <div class="flex">
                                    <a onclick="groupInfoModal()" class="block rounded-full hover:bg-gray-700 bg-slate-700 w-10 h-10 p-2 ml-4">
                                        <i class="fa-solid fa-user-group w-full h-full fill-current text-blue-500"></i>
                                    </a>
                                </div>
                            </div>
                            <div id="groupInfo" class="h-full w-full p-3 border-blue-400 border-t-2 bg-slate-800 border-b-2 overflow-scroll" style="display: none">
                                <div class="text-blue-500 font-bold py-2">Members</div>
                                <hr>
                                <?php foreach ($groupInfo['members'] as $member) : ?>
                                    <?php $username = findUsername($member); ?>
                                    <?php $memberAvatar = user_exists($username) ?>
                                    <div class="flex px-5 py-3 space-x-3 group rounded hover:bg-slate-700">
                                        <?php if (!empty($memberAvatar['avatar'])) : ?>
                                            <img class="rounded-full w-10 h-10" src="<?= main_url . $memberAvatar['avatar'] ?>" alt="">
                                        <?php else : ?>
                                            <div class="bg-pink-500 rounded-full w-10 h-10 text-center flex justify-center items-center"><?= substr($username, 0, 1) ?></div>
                                        <?php endif; ?>
                                        <div class="grow flex items-center gap-x-2">
                                            <?= $username ?>
                                            <?php if (in_array($member, $groupInfo['blocks'])) : ?>
                                                <span class="text-xs inline-block py-1 px-2.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-red-600 text-white rounded">Block</span>
                                            <?php endif ?>
                                        </div>
                                        <?php if (in_array($member, $groupInfo['admins'])) : ?>
                                            <div class="text-blue-400 flex items-center">admin</div>
                                        <?php endif; ?>
                                        <?php if (in_array($_SESSION['user_id'], $groupInfo['admins'])) : ?>
                                            <form class="hidden group-hover:flex space-x-2 flex items-center" method="post">
                                                <?php if (!in_array($member, $groupInfo['admins'])) : ?>
                                                    <button name="addAdmin" value="<?= $member ?>">
                                                        <i class="fa-solid fa-circle-user text-blue-500"></i>
                                                    </button>
                                                <?php elseif (in_array($member, $groupInfo['admins'])) : ?>
                                                    <button name="removeAdmin" value="<?= $member ?>">
                                                        <i class="fa-solid fa-circle-minus text-yellow-500"></i>
                                                    </button>
                                                <?php endif ?>
                                                <?php if (!in_array($member, $groupInfo['blocks'])) : ?>
                                                    <button name="blockMember" value="<?= $member ?>">
                                                        <i class="fa-brands fa-expeditedssl text-red-500"></i>
                                                    </button>
                                                <?php else : ?>
                                                    <button name="UnblockMember" value="<?= $member ?>">
                                                        <i class="fa-solid fa-lock-open fa-shake text-green-500"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <input type="hidden" name="groupID" value="<?= $_POST['groupID'] ?>">
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- chat body -->
                            <div id="chatBody" class="chat-body flex-1 px-6 overflow-y-scroll">
                                <?php $messages = readMessages($_POST['groupID']); ?>
                                <?php if (!empty($messages)) : ?>
                                    <?php loadMessages($messages, $rule) ?>
                                <?php endif; ?>

                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        setInterval(function() {
                                            worker(<?= $_POST['groupID'] ?>, <?= $_SESSION['user_id'] ?>, "<?= $rule ?>", "<?= main_url ?>", "<?= dbType ?>")
                                        }, 1000);

                                    })
                                </script>
                            </div>
                            <!-- chat footer -->
                            <div class="chat-footer flex-none">
                                <!-- Emoji bar -->
                                <div id="emojiBar" class="bg-gray-800 w-[90%] rounded-lg overflow-scroll mb-2 m-auto lg:text-3xl md:text-xl text-sm grid grid-cols-20 gap-2 flex-wrap p-2" style="display: none; width: 95%;">
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😀</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😃</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😄</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😁</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😆</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😀</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😃</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😄</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😂</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😇</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😍</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😜</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😎</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">👋</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">👍</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">👎</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">🖐</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">💖</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😍</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😜</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">😎</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">👋</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">👍</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">👎</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">🖐</div>
                                    <div class="emoji cursor-pointer" onclick="addEmoji(this)">💖</div>
                                </div>
                                <?php if (!in_array($_SESSION['user_id'], $groupInfo['blocks'])) : ?>
                                    <div class="flex flex-row items-center p-4">
                                        <button onclick="modalHandler(true, 'uploadModal')" class="flex flex-shrink-0 focus:outline-none mx-2 block text-blue-600 hover:text-blue-700 w-6 h-6">
                                            <i class="fa-solid fa-image w-full h-full fill-current"></i>
                                        </button>
                                        <form method="post" class="relative flex-grow" id="inputMessage">
                                            <label>
                                                <input id="inputField" name="input" class="rounded-full py-2 px-12 w-full border border-gray-800 focus:border-gray-700 bg-slate-700 focus:bg-gray-900 focus:outline-none text-gray-200 focus:shadow-md transition duration-300 ease-in" type="text" value="" placeholder="Aa" />
                                                <button id="newMessage" name="newMessage" class="absolute top-0 bottom-0 my-auto right-0 mr-3 flex flex-shrink-0 focus:outline-none block text-blue-600 hover:text-blue-700 w-6 h-6">
                                                    <i class="fa-solid fa-paper-plane w-5 h-5 text-blue-600 hover:text-blue-500 focus:text-blue-700 dark:text-blue-500"></i>
                                                </button>
                                                <button onclick="$('#emojiBar').slideToggle()" type="button" class="absolute top-0 left-0 mt-2 ml-3 flex flex-shrink-0 focus:outline-none block text-yellow-400 hover:text-yellow-300 w-6 h-6">
                                                    <i class="fa-regular fa-face-smile w-full h-full"></i>
                                                </button>
                                            </label>
                                            <input type="hidden" name="groupID" value="<?= $_POST['groupID'] ?>">
                                            <input type="hidden" name="userID" value="<?= $_SESSION['user_id'] ?>">
                                            <input type="hidden" name="main_url" value="<?= main_url ?>">
                                            <input type="hidden" name="dbType" value="<?= dbType ?>">
                                            <input type="hidden" name="rule" value="<?= $rule ?>">
                                        </form>

                                        <form method="post" class="relative flex-grow" id="editMessageFrom" style="display: none;">
                                            <label>
                                                <input id="editField" name="editField" style="border: 2px dotted #03cffc" class="rounded-full py-2 px-12 w-full border border-gray-800 focus:border-gray-700 bg-slate-700 focus:bg-gray-900 focus:outline-none text-gray-200 focus:shadow-md transition duration-300 ease-in" type="text" value="" placeholder="Aa" />
                                                <button id="editMessage" name="editMessage" class="absolute top-0 bottom-0 my-auto right-2 mr-3 flex flex-shrink-0 focus:outline-none block text-blue-600 hover:text-blue-700 w-6 h-6">
                                                    <i class="fa-regular fa-pen-to-square w-full h-full"></i>
                                                </button>
                                                <div onclick="editMessage(true, true, false)" id="editMessageClose" style="cursor:pointer" class=" absolute text-4xl -top-10 right-2 mr-3 flex flex-shrink-0 focus:outline-none block text-gray-600 hover:text-red-700">
                                                    &times;
                                                </div>
                                                <button onclick="$('#emojiBar').slideToggle()" type="button" class="absolute top-0 left-0 mt-2 ml-3 flex flex-shrink-0 focus:outline-none block text-yellow-400 hover:text-yellow-300 w-6 h-6">
                                                    <i class="fa-regular fa-face-smile w-full h-full"></i>
                                                </button>
                                            </label>
                                            <input type="hidden" name="groupID" value="<?= $_POST['groupID'] ?>">
                                        </form>
                                    </div>
                                <?php else : ?>
                                    <div class="flex justify-center items-center bg-gray-600 p-4">Sorry. You are &nbsp;<span class="text-red-700 font-extrabold">blocked</span>&nbsp;by admin and just can see messages</div>
                                <?php endif; ?>
                            </div>
                        <?php endif ?>
                    </section>
                </main>
            </div>
        </div>
        <?php include_once('modoles.php') ?>
    </body>
<?php
}

function process_inputs()
{

    global $userDetails;
    $userDetails = user_exists($_SESSION['username']);

    if (!isset($_POST)) return;

    if (isset($_POST['groupID'])) {
        global $groupInfo;
        $groupInfo = abstractGroup($_POST['groupID']);

        seenMessage($_POST['groupID'], $_SESSION['user_id']);
    }

    if (isset($_POST['newGroupName'])) {
        if (empty($_POST['newGroupName'])) {
            add_toast('Fill new group name to add it!', 'warning');
            return;
        }
        $newGroupName = $_POST['newGroupName'];
        if (!createGroup($newGroupName, $_SESSION['username'])) {
            add_toast('something Wrong. Please try later.', 'error');
            return;
        } else {
            add_toast('Group added successfully', 'success');
        }
    }


    if (isset($_POST['deleteMessage'])) {
        if (!deleteMessage($_POST['groupID'], $_POST['deleteMessage'])) {
            add_toast('something Wrong!', 'error');
        }
    }

    if (isset($_POST['editMessage'])) {
        if (empty($_POST['editField'])) {
            return;
        }

        editMessage($_POST['groupID'], $_POST['messageID'], $_POST['editField']);
    }

    if (isset($_FILES['newImage'])) {
        uploadImage($_POST['groupID'], $_FILES['newImage'], $_SESSION['username']);
    }

    if (isset($_POST['changeProf'])) {
        if (empty($_POST['username'])) {
            add_toast('Fill username!', 'error');
            return;
        }
        if (empty($_POST['email'])) {
            add_toast('Fill email!', 'error');
            return;
        }

        if (!editUser($_SESSION['username'], $_POST['username'], $_POST['email'], $_POST['bio'])) {
            add_toast('something Wrong! Try later', 'error');
            return;
        } else {
            add_toast('Profile edit successful', 'success');
            $userDetails = user_exists($_SESSION['username']);
            return;
        }
    }

    if (isset($_FILES['newProfImg'])) {
        uploadImage($_SESSION['username'], $_FILES['newProfImg'], $_SESSION['username'], 'avatar');
        $userDetails = user_exists($_SESSION['username']);
    }

    if (isset($_POST['blockMember']) || isset($_POST['UnblockMember'])) {
        if (isset($_POST['blockMember'])) {
            if (memberOperator($_POST['groupID'], $_POST['blockMember'], false)) {
                add_toast(findUsername($_POST['blockMember']) . ' Blocked!', 'warning');
            } else {
                add_toast('something Wrong!', 'error');
            }
        } elseif (isset($_POST['UnblockMember'])) {
            if (memberOperator($_POST['groupID'], $_POST['UnblockMember'], true)) {
                add_toast(findUsername($_POST['UnblockMember']) . ' UnBlocked', 'success');
            } else {
                add_toast('something Wrong!', 'error');
            }
        }
        $groupInfo = abstractGroup($_POST['groupID']);
        return;
    }

    if (isset($_POST['changeAvatar'])) {
        if (changeAvatar($_SESSION['user_id'], $_POST['changeAvatar'])) {
            add_toast('Profile Image Change', 'success');
            $userDetails = user_exists($_SESSION['username']);
        } else {
            add_toast('something wrong! Try later', 'error');
        }
    }

    if (isset($_POST['deleteAvatar'])) {
        if (removeAvatar($_POST['deleteAvatar'])) {
            add_toast('Profile Image deleted', 'success');
            $userDetails = user_exists($_SESSION['username']);
        } else {
            add_toast('something wrong! Try later', 'error');
        }
    }

    if (isset($_POST['addAdmin'])) {
        if (adminOperator($_POST['addAdmin'], $_POST['groupID'], 'add')) {
            add_toast('Admin Added', 'success');
            $groupInfo = abstractGroup($_POST['groupID']);
        }
    } else if (isset($_POST['removeAdmin'])) {
        if (adminOperator($_POST['removeAdmin'], $_POST['groupID'], 'remove')) {
            add_toast('Admin Removed', 'success');
            $groupInfo = abstractGroup($_POST['groupID']);
        }
    }
}
