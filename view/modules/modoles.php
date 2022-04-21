<!-- profile modal -->
<?php global $userDetails; ?>
<?php $avatar = findAvatar($_SESSION['username']) ?>
<div id="profile" onblur="this.fadeOut()" class="hidden h-screen backdrop-filter backdrop-blur transition duration-75 ease-in-out z-10 absolute top-0 right-0 bottom-0 left-0 flex">
    <div class="m-auto border-2 border-blue-300 rounded-lg overflow-hidden">
        <div class="w-full">
            <div class="w-full bg-blue-700 h-40 flex items-end relative">
                <div onclick="modalHandler(false, 'profile')" class="cursor-pointer absolute top-0 right-0 mt-4 mr-5 text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out rounded focus:ring-2 focus:outline-none focus:ring-gray-600" aria-label="close modal" role="button">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <div class="bg-gray-800 border-t-2 border-blue-300 w-40 h-40 rounded-lg shadow-md border-b-2 border-primary -mb-12 mx-5">
                    <?php if (!empty($avatar)) : ?>
                        <div class="carousel relative shadow-2xl w-full h-full">
                            <div class="carousel-inner overflow-hidden h-full">
                                <?php
                                $setAvatarIndex = array_search($userDetails['avatar'], $avatar);
                                $tmp = $avatar[$setAvatarIndex];
                                unset($avatar[$setAvatarIndex]);
                                array_push($avatar, $tmp);
                                $avatar = array_values($avatar);
                                ?>
                                <?php foreach ($avatar as $counter => $src) : ?>
                                    <div id="ProfImg_<?= $counter ?>" class="h-full" style="<?= ($counter + 1 != count($avatar)) ? 'display: none' : null ?>">
                                        <input class="carousel-open" type="radio" id="carousel-<?= $counter ?>" name="carousel" aria-hidden="true" hidden="" checked="checked">
                                        <div class="carousel-item absolute opacity-0 h-full">
                                            <img class="w-full h-full overflow-hidden rounded-lg" src="<?= main_url .  $src ?>" alt="">
                                        </div>
                                        <?php if ($counter != 0) : ?>
                                            <label onclick="carouselHandler(this, <?= $counter - 1 ?>)" for="carousel-<?= $counter - 1 ?>" class="prev w-6 h-6  absolute cursor-pointer font-bold text-lg text-black hover:text-white rounded-full bg-slate-600 hover:bg-yellow-500 leading-tight text-center z-10 -bottom-8 left-9 my-auto">‹</label>
                                        <?php endif; ?>
                                        <form method="post">
                                            <button type="submit" name="deleteAvatar" value="<?= $src ?>" class="max-w-max w-6 h-6 absolute -bottom-8 inset-x-0 mx-auto">
                                                <i class="fa-solid fa-trash-can text-red-500 w-full h-full"></i>
                                            </button>
                                        </form>
                                        <?php if ($counter + 1 != count($avatar)) : ?>
                                            <form method="post">
                                                <button type="submit" name="changeAvatar" value="<?= $src ?>" class="max-w-max w-6 h-6 absolute -bottom-8 right-0 mx-auto">
                                                    <i class="fa-solid fa-sync fa-spin text-green-500 w-full h-full"></i>
                                                </button>
                                            </form>
                                            <label onclick="carouselHandler(this, <?= $counter + 1 ?>)" for="carousel-<?= $counter + 1 ?>" class="next w-6 h-6  absolute cursor-pointer font-bold text-lg text-black hover:text-white rounded-full bg-slate-600 hover:bg-blue-700 leading-tight text-center z-10 -bottom-8 right-9 my-auto">›</label>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="w-full h-full rounded-lg text-yellow-700 flex flex-col justify-center items-center">
                            <i class="fa-solid fa-image"></i>
                            select profile image
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-7 h-5 w-40 text-white md:text-3xl text-xl grow"><?= $userDetails['name'] ?></div>
                <div onclick="editProf()" class="p-4 text-grey-900 cursor-pointer bg-gray-500 hover:bg-gray-400 focus:bg-gray-600 transition rounded flex -mb-6 mr-5">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div onclick="uploadProfImg('<?= $_SESSION['username'] ?>')" class="cursor-pointer p-4 text-grey-900 bg-sky-500 hover:bg-sky-400 focus:bg-sky-600 transition text-white rounded flex -mb-6 mr-5">
                    <i class="fa-solid fa-file-arrow-up"></i>
                </div>
                <div onclick="modalHandler(true, 'logoutModal')" class="p-4 text-grey-900 cursor-pointer bg-red-600 hover:bg-red-500 transition focus:bg-red-700 text-white rounded flex -mb-6 mr-5">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </div>
            </div>
        </div>
        <section class="m-auto w-full" id="showProf">
            <div class="bg-primary border-t rounded-b-lg p-5 pt-20 flex flex-col font-bold text-white bg-slate-800">
                <label class="my-3 text-gray-500">
                    Username
                    <div class="mb-2 h-5 text-xl text-blue-200 flex justify-between items-center">
                        <div><?= $userDetails['username'] ?></div>
                    </div>
                </label>
                <label class="my-3 text-gray-500">
                    Email
                    <div class="mb-2 h-5 text-xl text-blue-200  flex justify-between items-center"><?= $userDetails['email'] ?>

                    </div>
                </label>
                <div class="text-sm mt-2 text-gray-200">
                </div>

                <div class="py-5 break-all">
                    <label for="bio" class="text-gray-500">Bio</label>
                    <div id="bio" class="w-full text-blue-200 resize-none rounded p-2"><?= !empty($userDetails['bio']) ? $userDetails['bio'] : "add some bio about yourself" ?></div>
                </div>
            </div>
        </section>
        <section class="hidden m-auto w-full" id="editProf">
            <form class="bg-primary border-t rounded-b-lg p-5 pt-20 flex flex-col font-bold text-white bg-sky-900" method="post">
                <label class="my-2 text-gray-500">
                    Username
                    <input name="username" class="h-5 text-xl bg-gray-800 text-gray-300 flex justify-between items-center px-2 py-5 rounded-lg focus:ring-4 focus:ring-blue-700" value="<?= $userDetails['username'] ?>">
                </label>
                <label class="my-2 text-gray-500">
                    Email
                    <input name="email" class="w-full h-5 text-xl bg-gray-800 text-gray-300 flex justify-between items-center px-2 py-5 rounded-lg focus:ring-4 focus:ring-blue-700" value="<?= $userDetails['email'] ?>">
                </label>

                <div class="break-all my-2">
                    <label for="bio" class="text-gray-500">Bio</label>
                    <textarea name="bio" id="bio" class="w-full bg-gray-800 text-gray-300 resize-none rounded p-2 focus:ring-4 focus:ring-blue-700" rows="3" placeholder="add some bio about yourself"><?= !empty($userDetails['bio']) ? $userDetails['bio'] : null ?></textarea>
                </div>
                <button type="submit" onclick="showProf()" class="p-4 text-grey-900 transition bg-green-700 hover:bg-green-600 focus:bg-green-800 focus:ring-8 focus:ring-green-500 rounded flex justify-center" name="changeProf">
                    <i class="fa-regular fa-floppy-disk"></i>
                </button>
            </form>
        </section>
    </div>
</div>
</div>

<!-- upload image modal -->
<div id="uploadModal" class="hidden h-screen backdrop-filter backdrop-blur transition duration-75 ease-in-out z-10 absolute top-0 right-0 bottom-0 left-0 m-auto items-center">
    <div class="h-screen w-full m-auto flex items-center">
        <form class="rounded-lg shadow-xl bg-cyan-900 lg:w-1/3 md:w-2/3 w-full m-auto p-4" method="post" enctype="multipart/form-data">
            <input id="forWhere" type="hidden" name="groupID" value="<?= $_POST['groupID'] ?? null ?>">
            <label class="inline-block mb-2 text-sky-500 text-center">Upload Image</label>
            <div class="flex flex-col space-y-2 items-center justify-center">
                <label class="w-full border-4 border-dashed hover:bg-cyan-700 hover:border-gray-300 focus:bg-cyan-800 relative">
                    <div class="flex flex-col items-center justify-center py-20">
                        <i class="fa-regular fa-images w-12 h-12 text-gray-400 group-hover:text-gray-600"></i>
                        <p class="pt-1 text-sm tracking-wider text-gray-400 group-hover:text-gray-600">
                            Select a image</p>
                    </div>
                    <input id="typeOfInput" name="newImage" accept="image/*" type="file" class="opacity-0 h-0" onchange="previewImage(this, event)" />
                </label>
            </div>
            <div class="flex space-x-4 justify-center mt-3">
                <div id="cancelUpload" class="px-4 py-2 text-white bg-red-600 rounded shadow-xl grow text-center" onclick="modalHandler(false, 'uploadModal')">Cannel</div>
                <button class="px-4 py-2 text-white bg-green-600 rounded shadow-xl grow">Send</button>
            </div>
        </form>
    </div>
</div>


<!-- create group modal -->
<div class="hidden py-12 backdrop-filter backdrop-blur transition duration-150 ease-in-out z-10 absolute top-0 right-0 bottom-0 left-0" id="createGroupModal">
    <div role="alert" class="container mx-auto w-11/12 md:w-2/3 max-w-lg">
        <form method="post" class="relative py-8 px-5 md:px-10 bg-sky-800 shadow-md rounded-xl border border-gray-400">
            <h1 class="font-lg font-bold tracking-normal leading-tight mb-4 text-indigo-300">Create Group
            </h1>
            <label for="name" class="text-white text-sm font-bold leading-tight tracking-normal">Group Name:</label>
            <input id="name" name="newGroupName" class="mb-5 mt-2 text-gray-600 focus:outline-none focus:border focus:border-indigo-700 font-normal w-full h-10 flex items-center pl-3 text-sm border-gray-300 rounded border" placeholder="Group 1" />
            <div class="flex items-center justify-center w-full space-x-3">
                <div class="grow text-center focus:outline-none focus:ring-2 focus:ring-offset-2  focus:ring-gray-400 bg-gray-100 transition duration-150 text-gray-600 ease-in-out hover:border-gray-400 hover:bg-gray-300 border rounded px-8 py-2 text-sm" onclick="modalHandler(false, 'createGroupModal')">Cancel</div>
                <button class="grow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-700 transition duration-150 ease-in-out hover:bg-indigo-600 bg-indigo-600 rounded text-white px-8 py-2 text-sm">Submit</button>
            </div>
            <div class="cursor-pointer absolute top-0 right-0 mt-4 mr-5 text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out rounded focus:ring-2 focus:outline-none focus:ring-gray-600" onclick="modalHandler(false, 'createGroupModal')" aria-label="close modal" role="button">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </form>
    </div>
</div>


<!-- Logout Modal -->
<div class="hidden overflow-y-auto overflow-x-hidden fixed right-0 left-0 top-4 z-50 flex justify-center items-center md:inset-0 sm:h-full" id="logoutModal">
    <div class="relative px-4 w-full max-w-md h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex justify-end p-2">
                <button onclick="modalHandler(false, 'logoutModal')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white" data-modal-toggle="popup-modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 pt-0 text-center cursor-pointer">
                <i class="fa-solid fa-heart-crack w-12 mx-auto my-2 text-red-600"></i>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to logout from Messenger?</h3>
                <div class="flex justify-center space-x-2">
                    <div onclick="modalHandler(false, 'logoutModal')" data-modal-toggle="popup-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">No, cancel</div>
                    <a href="logout" data-modal-toggle="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                        Yes, I'm sure
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>