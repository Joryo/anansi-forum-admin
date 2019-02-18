<?php

namespace App\Models;

class ApiModel
{
    /**
     * Max response data count from Api
     */
    const RESPONSE_DATA_LIMIT = 20;

    public $type;
    protected $header;

    public function __construct()
    {
        if(empty($_SESSION['jwt'])) {
            $jwt = '';
        } else {
            $jwt = $_SESSION['jwt'];
        }
        $this->header = ['Content-Type' => 'application/vnd.api+json', 'Authorization' => "Bearer {$jwt}"];
    }

    /**
     * Get all resources
     * @param int $offset
     *
     * @return Array
     */
    public function getAll($offset = 0)
    {
        $response = app('api')->get($this->type . 's?page[offset]=' . $offset, [
            'headers' => $this->header
        ]);

        $content = $this->getContent($response);
        $content->offsets = $this->addPaginationFromLinks($content->links);

        return $content;
    }

    /**
     * Get all resources filtered by a field value
     * @param int $offset
     * @param string $field
     * @param string $value
     *
     * @return Array
     */
    public function getFiltered($offset = 0, $field, $value)
    {
        $response = app('api')->get($this->type . 's?page[offset]=' . $offset . '&filter['
            . $field . ']=' . $value, [
            'headers' => $this->header
        ]);

        $content = $this->getContent($response);
        $content->offsets = $this->addPaginationFromLinks($content->links);

        return $content;
    }

    /**
     * Get single resource
     * @param $id - Resource id
     *
     * @return Array
     */
    public function get($id)
    {
        $response = app('api')->get($this->type . 's/' . $id, [
            'headers' => $this->header
        ]);

        return $this->getContent($response);
    }

    /**
     * Get last created relationship of a resource
     * @param int $id - Id of the resource
     * @param string $relationship - Relationship type
     *
     * @return Array
     */
    public function getLastRelationships($id, $relationship)
    {
        $response = app('api')->get($this->type . 's/' . $id . '/'
            . $relationship . '?sort=-date-created', [
            'headers' => $this->header
        ]);


        return $this->getContent($response);
    }

    /**
     * Get all relationship of a resource
     * @param int $id - Id of the resource
     * @param string $relationship - Relationship type
     * @param int $offset
     *
     * @return Array
     */
    public function getRelationships($id, $relationship, $offset = 0)
    {

        $response = app('api')->get($this->type . 's/' . $id . '/'
            . $relationship . '?sort=-date-created&page[offset]=' . $offset, [
            'headers' => $this->header
        ]);

        $content = $this->getContent($response);
        $content->offsets = $this->addPaginationFromData($content->data, (int) $offset);
        return $content;
    }

    /**
     * Delete a single resource
     * @param int $id - Id of the resource
     */
    public function delete($id)
    {
        $response = app('api')->delete($this->type . 's/' . $id, [
            'headers' => $this->header
        ]);
    }

    /**
     * Update a single resource
     * @param int $id - Id of the resource
     * @param array $data - Data of the resource
     *
     * @return int status code
     */
    public function update($id, $data = [])
    {
        $response = app('api')->patch($this->type . 's/' . $id, [
            'headers' => $this->header,
            'json' => [
                'data' => [
                    'type' => $this->type,
                    'id' => $id,
                    'attributes' => array_merge(['id' => $id], $data)
                ]
            ]
        ]);

        return $response->getStatusCode();
    }

    /**
     * Create a single resource
     * @param array $data - Data of the resource
     *
     * @return int status code
     */
    public function create($data = [])
    {
        $response = app('api')->post($this->type . 's', [
            'headers' => $this->header,
            'json' => [
                'data' => [
                    'type' => $this->type,
                    'attributes' => $data
                ]
            ]
        ]);

        return $response->getStatusCode();
    }

    /**
     * Search a resource
     * @param Array $data - Search data
     *
     * @return Array
     */
    public function search($data = [])
    {
        if ($data['field'] == 'id') {
            $url_params = '/' . $data['query'];
        } else {
            $url_params = '?filter[' . $data['field'] . ']=' . $data['query'];
        }
        $response = app('api')->get($this->type . 's' . $url_params, [
            'headers' => $this->header,
        ]);

        $offset = isset($data['offset']) ? $data['offset'] : 0;

        $content = $this->getContent($response);
        $content->offsets = $this->addPaginationFromData($content->data, $offset);

        return $content;
    }

    /**
     * Get response data content
     * @param Object $response - Api response
     * @param Bool $force_collections
     *
     * @return Array
     */
    protected function getContent($response, $force_collections = true)
    {
        $content = $response->getBody()->getContents();
        $content_decoded = json_decode($content);
        if ($force_collections && !is_array($content_decoded->data)) {
            $content_decoded->data = [$content_decoded->data];
        }

        return $content_decoded;
    }

    /**
     * Extract previous an next page offset fom jsonapi links
     * @param Array $links
     *
     * @return Array
     */
    private function addPaginationFromLinks($links)
    {
        $prev = ['page' => ['offset' => false]];

        if (!empty($links->prev)) {
            parse_str(explode('?', $links->prev)[1], $prev);
        }
        $next = ['page' => ['offset' => false]];
        if (!empty($links->next)) {
            parse_str(explode('?', $links->next)[1], $next);
        }

        return $this->getPaginationLinks($prev['page']['offset'], $next['page']['offset']);
    }

    /**
     * Add offset when jsonApi don't provide links
     * @param Array $data
     * @param int $offset
     *
     * @return Array
     */
    private function addPaginationFromData($data, $offset)
    {
        $prev = false;
        $next = false;

        if(count($data) == static::RESPONSE_DATA_LIMIT) {
            $next = $offset + static::RESPONSE_DATA_LIMIT;
        }

        if ($offset) {
            if ($offset < static::RESPONSE_DATA_LIMIT) {
                $prev = 0;
            } else {
                $prev = $offset - static::RESPONSE_DATA_LIMIT;
            }
        }

        return $this->getPaginationLinks($prev, $next);
    }

    /**
     * Create pagination links from prev and next offsets
     * @param Int $prev_offset
     * @param Int $next_offset
     *
     * @return Array
     */
    private function getPaginationLinks($prev_offset, $next_offset)
    {
        $path = app('Illuminate\Http\Request')->path();

        return [
            'next' => $next_offset !== false ? '/' . $path . '?offset=' . $next_offset : false,
            'prev' => $prev_offset !== false ? '/' . $path . '?offset=' . $prev_offset : false,
        ];
    }

}
