<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $url = 'https://www.prothomalo.com/bangladesh/6i4zbjs3pp';

    $response = Http::get($url);

    if ($response->successful()) {
        $html = $response->body();

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);  // Ignore warnings for malformed HTML
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        $node = $xpath->query('//*[@data-title-0]')->item(0);

        $paragraphs = $xpath->query('//p');

        $paragraphArray = [];

        foreach ($paragraphs as $paragraph) {
            $class = $paragraph->getAttribute('class');

            if ($class !== 'print-adslot' && $class !== 'story-element-text') {
                $paragraphArray[] = $paragraph->nodeValue;
            }
        }

        if ($node) {
            $title = $node->getAttribute('data-title-0');

            return response()->json([
                'url' => $url,
                'title' => $title,
                'paragraphs' => $paragraphArray,

            ]);
        } else {
            return response()->json([
                'error' => 'No element with data-title-0 attribute found.',
                'url' => $url,
                'paragraphs' => $paragraphArray,
            ]);
        }
    } else {
        return response()->json([
            'error' => 'Failed to fetch the webpage.',
            'status' => $response->status(),
            'url' => $url,
        ], $response->status());
    }

});
