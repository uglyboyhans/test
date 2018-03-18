<?php
//bookseat('chooseaddrForm',unescape(window.location.search.substr(1).match(new RegExp("(^|&)" + "id"+ "=([^&]*)(&|$)","i"))[2]),'靠靠')

$ids = [
    '49', '87', '35', '50',
];
$ch = curl_init();
$url = 'https://jlpt.etest.net.cn/chooseAddr.do?bkjb=2';
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// https请求 不验证证书和hosts:
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
try {
    while (true) {
        $output = curl_exec($ch);
        $flag = false;
        foreach ($ids as $id) {
            $str = '"id":' . $id . ',"vacancy":1';
            if (strstr($output, $str)) {
    	    $flag = true;
                echo "___done_" . $id . "__\n";
            }
        }
        if ($flag) {
            echo "\n\n";
        }
    }
} catch (Exception $e) {
    curl_close($ch);
}
