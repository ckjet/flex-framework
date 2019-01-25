<?php

namespace Hypilon\Service;

class Service
{
    /**
     * @var array $connectors
     */
    protected $connectors;

    /**
     * @var array $methods
     */
    protected $methods;

    static public function get($method, $params = [])
    {
        $service = new static;
        return $service->executeGet($method, $params);
    }

    static public function post($method, $params = [])
    {
        $service = new static;
        return $service->executePost($method, $params);
    }

    public function executePost($method, $params)
    {
        $url = $this->getUrl($method);
        if(!$url) {
            return [];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }

    public function executeGet($method, $params)
    {
        $url = $this->getUrl($method);
        if(!$url) {
            return [];
        }
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }

    public function getUrl($method, $exclude = [])
    {
        if(sizeof($exclude)) {
            $connectors = [];
            foreach($this->connectors as $key => $connector) {
                if(!in_array($key, $exclude)) {
                    $connectors[] = $connector;
                }
            }
        } else {
            $connectors = $this->connectors;
        }
        if(sizeof($connectors)) {
            $count_connector = count($connectors);
            $rand_connector = rand(0, $count_connector) % $count_connector;
            $rand_connector = $this->connectors[$rand_connector];
            $url = 'http://' . $rand_connector['host'] . ':' . $rand_connector['port'] . $this->methods[$method];
            if (!$this->checkConnector($url)) {
                $exclude[] = $rand_connector;
                return $this->getUrl($method, $exclude);
            }
            return $url;
        }
        return false;
    }

    private function checkConnector($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        $errors = curl_error($ch);
        curl_close($ch);
        if($errors) {
            return false;
        }
        return true;
    }
}