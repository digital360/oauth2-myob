<?php

namespace Tenfef\MYOB;

use GuzzleHttp\Psr7\Response;
use UnexpectedValueException;

class AccountRightRequest
{
    public function __construct($provider, $token, $username = null, $password = null)
    {
        $this->token = $token;
        $this->provider = $provider;
        $this->username = $username;
        $this->password = $password;
    }

    public function fetchWithPagination($uri): array
    {
        $result = $this->fetch($uri);
        if (!isset($result['Items'])) {
            return $result;
        }

        $items = $result['Items'];
        if (!empty($result['NextPageLink'])) {
            $result = $this->fetchWithPagination($result['NextPageLink']);
            $items = array_merge($items, $result);
        }

        return $items;
    }

    public function fetch($uri): array
    {
        $options = ['headers' => $this->provider->getHeaders($this->token, $this->username, $this->password)];
        $request = $this->provider->getRequest(Provider::METHOD_GET, $uri, $options);

        $response = $this->provider->getParsedResponse($request);
        if (false === is_array($response)) {
            throw new UnexpectedValueException(
                'Invalid response received from Authorization Server. Expected JSON.'
            );
        }

        return $this->provider->getParsedResponse($request);
    }

    public function fetchStream($uri): Response
    {
        $options = ['headers' => $this->provider->getHeaders($this->token, $this->username, $this->password)];
        $request = $this->provider->getRequest(Provider::METHOD_GET, $uri, $options);

        return $this->provider->getResponse($request);
    }

    public function post($URI, $data)
    {
        return $this->provider->post("/accountright/" . $URI, $data, $this->token, $this->username, $this->password);
    }

    public function put($URI, $data)
    {
        return $this->provider->put("/accountright/" . $URI, $data, $this->token, $this->username, $this->password);
    }

    public function delete($URI)
    {
        return $this->provider->delete("/accountright/" . $URI, $this->token, $this->username, $this->password);
    }

    public function postFullResponse($URI, $data)
    {
        return $this->provider->postFullResponse("/accountright/" . $URI, $data, $this->token, $this->username,
            $this->password);
    }
}
