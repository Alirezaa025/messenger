<?php

/**
 * if rule == admin can delete and edit self message and delete others message
 * if rule == self can delete and edit self message
 */

function chatButton($id, $side, $type)
{ ?>
    <?php if ($side == 'main' && $type != 'image') : ?>
        <!-- edit button -->
        <button onclick="editMessage(this, <?= $id ?>, true);" type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-blue-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen" viewBox="0 0 16 16">
                <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001zm-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708l-1.585-1.585z" />
            </svg>
        </button>
    <?php endif; ?>
    <!-- delete button and confirm delete button -->
    <form method="POST" onclick="removeMessage(this)">
        <button method="POST" type="submit" disabled="disabled" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-red-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
            </svg>
        </button>
        <input type="hidden" name="groupID" value="<?= $_POST['groupID'] ?>">
        <input type="hidden" name="deleteMessage" value="<?= $id ?>">
    </form>
    <button type="button" style="display:none;" class="hidden text-green-700 group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
        </svg>
    </button>
<?php
}
