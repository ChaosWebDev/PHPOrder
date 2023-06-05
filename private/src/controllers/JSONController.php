<?php

namespace ChaosWD\Controller;

use stdClass;
use fileHandler;

class JSONController
{
    use fileHandler;

    public $baseURL;
    public $response;

    public function __construct($baseURL)
    {
        $this->baseURL = $baseURL;
    }

    public function getFull($url = null): stdClass
    {
        $url = ($url !== null) ? $url : $this->baseURL;
        return $this->getJsonDecodedFileContents($url);
    }

    public function getPartial($path, $full = null)
    {
        $pathParts = explode('->', $path);
        if ($full == null) $full = $this->response;
        $body = $full->body;
        $data = new stdClass();
        foreach ($pathParts as $part) :
            if (isset($body->$part) && $body->$part !== null && !empty($body->$part)) :
                $data = $body->$part;
            else :
                break;
            endif;
        endforeach;
        return $data;
    }

    public function getComplex($options)
    {
        $data = new stdClass();

        $url = $options['url'] ?? $this->baseURL;
        $url .= $options['endpoint'] ? '/' . $options['endpoint'] : '';

        $url .= $options['params'] ? '?' . $options['params'] : '';

        $response = json_decode(file_get_contents($url), false);

        if (isset($options['path']) && $options['path'] !== null) {
            $data = $this->getPartial($options['path'], $response);
        } else {
            $data = $response->body ?? $response ?? null;
        }

        if (is_array($data)) {
            $limit = $options['limit'] ?? count($data);
            $res = array_slice($data, 0, $limit);
        } else {
            $res = $data;
        }

        return $res;
    }

    public function getEndpoint(string $endpoint): stdClass
    {
        return $this->getBody($endpoint);
    }

    protected function getBody($endpoint = null): stdClass
    {
        $url = ($endpoint !== null) ? $url = $this->baseURL . "/{$endpoint}" : $url = $this->baseURL;

        $raw = $this->getJsonDecodedFileContents($url);
        if ($raw?->body) {
            return $raw->body;
        } else {
            return $raw;
        }
    }
}
