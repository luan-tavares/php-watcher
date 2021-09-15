 <?php

ignore_user_abort(true);

set_time_limit(0);

require __DIR__ ."/app/global/vendor/autoload.php";

if (PHP_SAPI != "cli") {
    exit("Please run this script from the command line");
}

function getStreamHeaders($file): array
{
    $contents = stream_get_meta_data($file)["wrapper_data"];
    $result = [];
    foreach ($contents as $content) {
        $data = explode(": ", $content);

        if (@$data[1]) {
            $result[trim($data[0])] = trim($data[1]);
            continue;
        }
        $result[] = trim($data[0]);
    }


    return $result;
}

function downloadFile($url, $path)
{
    $newfname = $path;
    $file = fopen($url, 'rb');

    $headers = getStreamHeaders($file);

    $totalSize = ((int) $headers["Content-Length"]);
    $partial = 1024 * 8; //1KB
    $i = 0;

    if ($file) {
        $newf = fopen($newfname, 'wb');
        
        if ($newf) {
            while (!feof($file)) {
                $i++;
                fwrite($newf, fread($file, $partial), $partial);
                dump(number_format((ftell($newf)/$totalSize)*100, 1) ."%");
                dump(ftell($newf) ." / ". $totalSize);
            }
        }
    }

    if ($file) {
        fclose($file);
    }

    if ($newf) {
        fclose($newf);
    }
}

downloadFile(
    "https://cdnp.hubspot.net/hubfs/6428836/01.2-visitas__demais_sem_negocio.xlsx?Expires=1621451007&Signature=F9BtZZwOZ3QBdIkRbWXX3i5~owOGE00wixv7RzWpGASzaXM8d3zHunt0PobBikIy1prhsdULraBfHwqLE64a8OQ0lJ-A~V3Fb8KJjFp4nckDfYgX5mdoTkNUbTezR3aPv73gSB1bkXrv-w~QumjRWilfrOI7VluVEESisDkpm5BpSfSj89pjRVP4FrRLe9ISja0sj9ji8jz--FSjEzRxYjqZxHSPeSobsaELgHTo7SqK2LvOQPt9bS9SIBodnjy6PGJ4wYRJWMuBWMiJmNuWxncMe5qgDK1oRGdhBOMVjpcn7SG2NT3Zy4zAnUj1-EDymxY8YHiDdO1BBtoZdqZpMg__&Key-Pair-Id=APKAJDNICOKANPHVCSBQ",
    __DIR__ ."/novo.xlsx"
);

// // The worker will execute every X seconds:
// $seconds = 1;

// // We work out the micro seconds ready to be used by the 'usleep' function.
// $micro = $seconds * 1000000;

// while (true) {
    
//     // This is the code you want to loop during the service...
//     $myFile = "/home/luan/Desktop/daemontest.txt";
//     $fh = fopen($myFile, 'a') or die("Can't open file");
//     $stringData = "File updated at: " . time(). "\n";
//     print($stringData . PHP_EOL);
//     fwrite($fh, $stringData);
//     fclose($fh);

//     // Now before we 'cycle' again, we'll sleep for a bit...
//     usleep($micro);
// }
