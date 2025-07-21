<?php
if (!function_exists("get")) {
    function get($url, $apiKeyName, $params = [], $isRapidAPI = false, $host = null) {
        $apiKey = getenv($apiKeyName);
        if (!$apiKey) {
            error_log("Missing API key for '$apiKeyName'");
            return ["status" => 400, "response" => "Missing API key"];
        }
        $url .= '?' . http_build_query($params);
        $headers = [];
        if ($isRapidAPI) {
            if (!$host) {
                error_log("RapidAPI host is required when isRapidAPI is true.");
                return ["status" => 400, "response" => "Missing RapidAPI host"];
            }
            $headers[] = "X-RapidAPI-Key: $apiKey";
            $headers[] = "X-RapidAPI-Host: $host";
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ["status" => $status, "response" => $response];
    }
}
