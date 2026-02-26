<?php
// Script simple para ver el CSV directamente
$csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vT_YovS-kYOHIkiwx_YQyzMvixS52UQSihicIpKL0mv3Z2QZZShLLk-NnrANoQIKE7ZcbbWdxO40lQa/pub?gid=0&single=true&output=csv";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $csvUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$csvData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

header('Content-Type: text/plain; charset=utf-8');
echo "=== CONTENIDO RAW DEL CSV ===\n\n";
echo $csvData;
?>
