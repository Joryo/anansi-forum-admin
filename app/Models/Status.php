<?php

namespace App\Models;

/**
 * Status model
 */
class Status extends ApiModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the status of the Api server like number of resources, version...
     * 
     * @return Array
     */
    public function getStatus()
    {
        $response = app('api')->get('status', [
            'headers' => $this->header
        ]);

        return $this->getContent($response);
    }
}
