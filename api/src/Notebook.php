<?php

// namespace App\Controllers;

class Notebook
{
    public function index($params)
    {

        $directoryPath = __DIR__ . "/../../data/" . $params->notebook;
        // $data = $this->parseDir($directoryPath)["folders"][0];
        $data = $this->parseDir($directoryPath);
        // $jsonData = json_encode($this->parseDir($directoryPath)["folders"], JSON_PRETTY_PRINT);
        // echo $jsonData;

        return [
            'data' => $data,
        ];
    }

    function parseDir($path)
    {
        $result = array();

        $files = scandir($path);

        foreach ($files as $file) {
            $filePath = $path . '/' . $file;
            if ($file != "." && $file != ".." && is_dir($filePath)) {
                $meta = json_decode(file_get_contents($filePath . '/meta.json'), true);

                // if (is_dir($filePath)) {
                if ($meta["type"] == "folder") {
                    // $meta->data = $this->parseDir($filePath);
                    $meta = array_merge($meta, $this->parseDir($filePath));
                    $result["folders"][] = $meta;
                    // $result["folders"][] = $this->parseDir($filePath);
                } else {
                    $result["files"][] = $meta;
                    // $result[] = $file;
                }
            }
        }

        return $result;
    }
}
