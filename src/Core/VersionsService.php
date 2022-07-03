<?php

namespace WPCSWooSubscriptions\Core;

class VersionsService
{
    public function getAll()
    {
        $response = wp_remote_get('https://api.' . WPCS_API_REGION . '.wpcs.io/v1/versions', [
            'headers' => [
                'Authorization' => "Basic " . base64_encode(WPCS_API_KEY . ":" . WPCS_API_SECRET),
            ]
        ]);

        return json_decode($response['body']);
    }
}