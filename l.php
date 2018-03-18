<?php
//bookseat('chooseaddrForm',unescape(window.location.search.substr(1).match(new RegExp("(^|&)" + "id"+ "=([^&]*)(&|$)","i"))[2]),'靠靠')

while (true) {
    $ch = curl_init();
    $url = 'https://jlpt.etest.net.cn/chooseAddr.do?bkjb=2';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // https请求 不验证证书和hosts:
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $output = curl_exec($ch);

    $ids = [
        '49', '87', '35', '50',
    ];
    $flag = true;
    foreach ($ids as $id) {
        $str = '"id":' . $id . ',"vacancy":1';
        if (strstr($output, $str)) {
            exec("start https://jlpt.etest.net.cn/index.do?id=" . $id);
            echo "\n\n\n___done_" . $id . "__\n";
            $flag = false;
            break;
        }
    }
    if (!$flag) {
        break;
    }
    curl_close($ch);
    usleep(50000);
    // echo $output;
}
