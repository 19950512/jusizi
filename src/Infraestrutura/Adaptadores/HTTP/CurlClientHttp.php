<?php

declare(strict_types=1);

namespace App\Infraestrutura\Adaptadores\HTTP;

use App\Aplicacao\Compartilhado\HTTP\ClientHttp;
use Exception;

class CurlClientHttp implements ClientHttp
{
    private string $_baseURL = '';
    private array $_headers = [];
    private array $_certificado = [];

    public function __construct($config = [])
    {
        if(!function_exists('curl_init')){
            throw new Exception('Ops, é preciso instalar o curl.');
        }

        $this->setConfig($config);
    }

    public function setConfig($config): void
    {

        if(isset($config['baseURL'])){
            $this->_baseURL = $config['baseURL'];
        }
        if(isset($config['headers'])){
            $this->_headers = $config['headers'];
        }
        if(isset($config['certificado'])){
            $this->_certificado = $config['certificado'];
        }
    }

    public function request($data, $method): ResponseClientHttp
    {

        if(!isset($data['endpoint']) or empty($data['endpoint'])){
            throw new Exception('Ops, informe o endpoint.');
        }

        $curl = curl_init();

        $bodyData = '';

        if(isset($data['post']) and in_array('Content-Type: application/json', $this->_headers)){
            $bodyData = json_encode($data['post']);
        }else{
            if(isset($data['post']) and is_array($data['post']) and count($data['post']) > 0){
                $bodyData = http_build_query($data['post']);
            }
        }

        $opcoes = [
            CURLOPT_URL => $this->_baseURL.$data['endpoint'],
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $bodyData,
            CURLOPT_VERBOSE => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $this->_headers,
            CURLOPT_FAILONERROR => false,
        ];

        if(isset($this->_certificado['key'], $this->_certificado['pass']) AND !empty($this->_certificado['pass']) AND !empty($this->_certificado['key'])){
            if(is_file($this->_certificado['pass']) and is_file($this->_certificado['key'])){
                $opcoes[CURLOPT_SSLCERT] = $this->_certificado['key'];
                $opcoes[CURLOPT_SSLKEY] = $this->_certificado['pass'];
                if(isset($this->_certificado['password']) and !empty($this->_certificado['password'])){

                    if(!str_contains($this->_certificado['password'], '/certificados/')){
                        $opcoes[CURLOPT_SSLCERTPASSWD] = $this->_certificado['password'];
                    }
                }
            }
        }


        curl_setopt_array($curl, $opcoes);

        try {

            $resultado = curl_exec($curl);

            if(curl_errno($curl)){
                $resultado = curl_error($curl);
            }

            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $respostaRequest = $resultado;

            if(!empty($resultado)){
                json_decode($respostaRequest, true);
                // se for um json. retorna array
                if(json_last_error() == JSON_ERROR_NONE){
                    $respostaRequest = json_decode($respostaRequest, true);
                }
            }

            return new ResponseClientHttp(
                code: $httpcode,
                body: $respostaRequest
            );

        }catch(Exception $erro){

            throw new Exception('lascou');
        }

    }

    public function get($endpoint): ResponseClientHttp
    {

        return $this->request(
            data: ['endpoint' => $endpoint],
            method: 'GET'
        );
    }

    public function post($endpoint, $data = []): ResponseClientHttp
    {

        return $this->request(
            data: [
                'endpoint' => $endpoint,
                'post' => $data
            ],
            method: 'POST'
        );
    }

    public function delete($endpoint, $data = []): ResponseClientHttp
    {

        return $this->request(
            data: [
                'endpoint' => $endpoint,
                'post' => $data
            ],
            method: 'DELETE'
        );
    }

    public function patch($endpoint, $data = []): ResponseClientHttp
    {

        return $this->request(
            data: [
                'endpoint' => $endpoint,
                'post' => $data
            ],
            method: 'PATCH'
        );
    }

    public function put($endpoint, $data = []): ResponseClientHttp
    {

        return $this->request(
            data: [
                'endpoint' => $endpoint,
                'post' => $data
            ],
            method: 'PUT'
        );
    }
}