<?php
namespace ShortCord\Helpers;

/**
 * Get Server uptime, Linux only
 * @return array
 */
function Uptime(): array {
    $uptime = shell_exec("cut -d. -f1 /proc/uptime");

    return [
        'days' => floor($uptime/60/60/24),
        'hours' => floor($uptime/60/60%24),
        'minutes' => floor($uptime/60%60),
        'seconds' => floor($uptime%60)
    ];
}

/**
 * Format bytes to a human readable string
 * @param int $bytes
 * @return string
 */
function format_bytes($bytes): string {
    if ($bytes < 1024) {
        return $bytes.'B';
    } else if ($bytes < 1048576) {
        return round($bytes / 1024, 2).'KB';
    } else if ($bytes < 1073741824) {
        return round($bytes / (1024*1024), 2).'MB';
    } else if ($bytes < 1099511627776) {
        return round($bytes / (1024*1024*1024), 2).'GB';
    } else {
        return round($bytes / (1024*1024*1024*1024), 2).'TB';
    }
}

/**
 * Read a local file
 * @param string $file
 * @return string
 */
function readFile($file): string {
	return file_get_contents($file);
}

/**
 * Read a Web file using CURL
 * @param string $file
 * @return string
 */
function readWebFile($file): string {
    $toReturn = "";

    $cS = \curl_init();
    \curl_setopt($cS, CURLOPT_URL, $file);
    \curl_setopt($cS, CURLOPT_BINARYTRANSFER, true);
    \curl_setopt($cS, CURLOPT_RETURNTRANSFER, true);
    $toReturn = \curl_exec($cS);
    \curl_close($cS);

    return $toReturn;
}

/**
 * Get a sanitized value from the $_SERVER array
 * @param string $key
 * @return string
 */
function getFromServer($key) {
    return filter_input(INPUT_SERVER, $key, FILTER_SANITIZE_STRING);
}