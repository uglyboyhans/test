<?php

header("Content-Type: text/html; charset=utf-8");

/**
 * 下载ximalaya.com的专辑文件
 *
 * @param string $url     专辑url
 * @param string $dirPath 文件保存路径
 *
 * @return boolean
 */
function download($url, $dirPath)
{
    echo '<pre>';
    //获取专辑包含音频信息的所有json文件路径
    $jsonAddressArr = getJsonAddresses($url);
    //每一个json文件拆出音频信息并下载
    foreach ($jsonAddressArr as $jsonAddress) {
        //获取音频信息
        $soundInfo = getSoundInfo($jsonAddress);
        //下载文件:
        try {
            var_dump(getFile($soundInfo['fileUrl'], $dirPath, $soundInfo['title'] . ".mp3"));
        } catch (Exception $e) {
            continue;
        }
    }
    return true;
}

/**
 * 获取专辑包含音频信息的所有json文件路径
 *
 * @param string $url  专辑url
 * @param int    $page 页号
 *
 * @return array
 */
function getJsonAddresses($url, $page = 1)
{

    $jsonAddressArr = [];
    $urlWithPage = $url . "?page=" . $page;
    $html = file_get_contents($urlWithPage);
    $dom = new DOMDocument;
    $dom->loadHTML($html);
    $xml = simplexml_import_dom($dom);
    $lis = $xml->xpath('//*[@id="mainbox"]/div/div[1]/div/div[2]/div[4]/ul/li');
    if (is_array($lis) && !empty($lis)) {
        foreach ($lis as $li) {
            $liArr = (array) $li->attributes();
            $sound_id = $liArr['@attributes']['sound_id'];
            $jsonAddressArr[] = "http://www.ximalaya.com/tracks/" . $sound_id . ".json";
        }
        $page++;
        //递归:
        return array_merge($jsonAddressArr, getJsonAddresses($url, $page));
    } else {
        return $jsonAddressArr;
    }
}

/**
 * 获取json,拆出音频名和音频下载地址
 *
 * @param string $jsonAddress json文件的地址
 *
 * @return array
 */
function getSoundInfo($jsonAddress)
{
    //获取json,拆出音频名和音频下载地址
    $json = file_get_contents($jsonAddress);
    $soundInfo = json_decode($json);
    $title = $soundInfo->title;
    $encode = mb_detect_encoding($title, array("ASCII", 'UTF-8', "GB2312", "GBK", "BIG5"));
    return [
        'title' => iconv($encode, "GBK//IGNORE", $title),
        'fileUrl' => $soundInfo->play_path
    ];
}

/**
 * 下载单个文件
 *
 * @param string $url      文件url
 * @param string $save_dir 文件保存路径
 * @param string $filename 文件名
 *
 * @return array
 */
function getFile($url, $save_dir = '', $filename = '')
{
    if (trim($url) == '') {
        return false;
    }
    if (trim($save_dir) == '') {
        $save_dir = './';
    }
    if (0 !== strrpos($save_dir, '/')) {
        $save_dir.= '/';
    }
    //创建保存目录  
    if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
        return false;
    }
    //获取远程文件
    ob_start();
    readfile($url);
    $content = ob_get_contents();
    ob_end_clean();
    //echo $content;  
    $size = strlen($content); //文件大小  
    $fp2 = fopen($save_dir . $filename, "wb");
    fwrite($fp2, $content);
    fclose($fp2);
    unset($content, $url);
    return [
        'file_name' => $filename,
        'save_path' => $save_dir . $filename,
        'file_size' => $size
    ];
}

//run
$url = isset($argv[0]) ? $argv[0] : null;
$url = empty($url) ? $url : $_GET['url'];
$dirPath = "D:/nihonngo/getFile";
download($url, $dirPath);
