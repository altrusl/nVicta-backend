<?php

$notebook = "personal";

// $nbMeta = file_get_contents(__DIR__ . "/../data/" . $notebook . "meta.json");

$result = [];

$result = parseLevel($result, __DIR__ . "/../data/" . $notebook);

function parseLevel(&$result, $dir)
{
    $result["meta"] = file_get_contents($dir . "/meta.json");
    glob()
}