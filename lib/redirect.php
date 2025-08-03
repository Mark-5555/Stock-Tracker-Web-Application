<?php
function redirect($url)
{
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    } else {
        echo "<script>window.location.href='" . $url . "';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=" . $url . "' /></noscript>";
        exit;
    }
}
