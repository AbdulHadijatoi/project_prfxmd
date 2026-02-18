<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PammService
{
    private $baseUrl;
    private $username;
    private $password;
    private $authorizationHeader;

    public function __construct()
    {
        $this->baseUrl = settings()['pamm_url'];
        $this->username = settings()['pamm_username'];
        $this->password = settings()['pamm_password'];
        $this->authorizationHeader = 'Basic ' . base64_encode('pamm-admin-web:');
    }
    private function getOAuthToken()
    {
        $response = Http::asForm()->withHeaders([
            'Authorization' => $this->authorizationHeader,
        ])->post("{$this->baseUrl}/api/oauth2/token", [
            'grant_type' => 'password',
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json()['access_token'] ?? null;
    }
    public function makeApiRequest($endpoint, $method = 'GET', $data = [], $headers = [])
    {
        $bearerToken = $this->getOAuthToken();
        if (!$bearerToken) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        $response = Http::withToken($bearerToken)
            ->withHeaders(array_merge([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ], $headers))
            ->{$method}("{$this->baseUrl}{$endpoint}", $data);

        return $response->json();
    }
    public function makeCurlRequest($url, $method, $data = [], $headers = [])
    {
        $bearerToken = $this->getOAuthToken();
        $ch = curl_init($url);
        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Connection: keep-alive',
            'Authorization: Bearer ' . $bearerToken
        ];
        $headers = array_merge($defaultHeaders, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            return [
                'error' => true,
                'message' => curl_error($ch)
            ];
        }
        curl_close($ch);
        return json_decode($response, true);
    }
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}
