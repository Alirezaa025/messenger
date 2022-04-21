<?php

function get_contents()
{
?>
<?php global $details; ?>
    <!-- This is an example component -->
    <div class="w-full h-screen flex items-center justify-center bg-slate-900">
        <div class="relative w-96 h-auto bg-slate-600 rounded-md pt-5 pb-8 px-4 shadow-md hover:shadow-lg flex flex-col items-center">
            <div class="absolute rounded-full bg-slate-900 w-28 h-28 p-2 z-10 -top-5 -left-5 shadow-lg hover:shadow-xl transition">
                <div class="rounded-full bg-slate-400 w-full h-full overflow-auto">
                    <img src="<?= findAvatar($details['username']) ?>">
                </div>
            </div>
            <label class="font-bold text-white text-lg">
                <?=$details['name']?>
            </label>
            <label class="font-bold text-blue-400 text-lg">
                <?='@' . $details['username']?>
            </label>
            <p class="text-center text-blue-200 mt-2 leading-relaxed">
                <?= $details['description'] ?? 'I don\'t add any description :)';?>
            <ul class="flex flex-row gap-2 mt-4">

            </ul>
        </div>
    </div>
<?php
}

function process_inputs()
{
    global $details;
    $details = user_exists(array_keys($_GET)['0']);
    echo '<pre>';
    print_r($details);
    echo '</pre>';
}
