<?php

require_once '/home/luan/Desktop/app/global/vendor/autoload.php';

$path = $argv[1];

$fd = inotify_init();
stream_set_blocking($fd, 0);

$watch_descriptor = inotify_add_watch($fd, $path, IN_ACCESS | IN_MODIFY | IN_CREATE | IN_DELETE);


$poll = 0;
while (true) {
    dump(intval((memory_get_usage() / 1024 / 1024)*1000)/1000 .'MB');
    $ecount = 0;
    $fcount = 0;
    // Poll for queued events, generated meanwhile
    // Because while (true) { } has some seriously bad mojo
    sleep(3);
    $poll++;
    $events = inotify_read($fd);

    if ($events) {
        $ecount = count($events);
    }
    dump('=== '.date('Y-m-d H:i:s')." inotify poll #$poll contains ".$ecount.' events');

    if ($events) {
        dump(':');
    }


    if ($events) {
        foreach ($events as $event) {
            dump($event);
            $fcount++;
            dump('        inotify Event #'.$fcount.' - Object: '.$event['name']."\n");
        }
    }
}

inotify_rm_watch($fd, $watch_descriptor);

fclose($fd);