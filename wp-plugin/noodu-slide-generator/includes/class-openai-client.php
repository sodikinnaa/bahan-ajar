<?php

class Noodu_OpenAI_Client {
    private $base_url;
    private $api_key;
    private $model;

    public function __construct( $base_url, $api_key, $model = null ) {
        $this->base_url = rtrim( $base_url, '/' );
        $this->api_key = $api_key;
        $this->model = $model;
    }

    public function set_model( $model ) {
        $this->model = $model;
    }

    public function get_models() {
        $url = $this->base_url . '/v1/models';

        $response = wp_remote_get( $url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json'
            ),
            'timeout' => 60
        ) );

        if ( is_wp_error( $response ) ) {
            throw new Exception( $response->get_error_message() );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( empty( $data['data'] ) ) {
            throw new Exception( __( 'Failed to fetch models from API', 'noodu-slide-generator' ) );
        }

        return $data['data'];
    }

    public function generate_slide_content( $prompt, $context = array() ) {
        if ( ! $this->model ) {
            throw new Exception( __( 'Model not set', 'noodu-slide-generator' ) );
        }

        $system_prompt = __( "You are an expert educational content creator for Noodu Academy. Your task is to create high-quality, engaging slide content. Structure responses as JSON with 'slides' array containing objects with 'title', 'content' (HTML), and 'notes'.", 'noodu-slide-generator' );

        $messages = array(
            array(
                'role' => 'system',
                'content' => $system_prompt
            ),
            array(
                'role' => 'user',
                'content' => $prompt
            )
        );

        $url = $this->base_url . '/v1/chat/completions';

        $payload = array(
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 4000,
            'response_format' => array( 'type' => 'json_object' )
        );

        $response = wp_remote_post( $url, array(
            'body' => wp_json_encode( $payload ),
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json'
            ),
            'timeout' => 60
        ) );

        if ( is_wp_error( $response ) ) {
            throw new Exception( $response->get_error_message() );
        }

        $http_code = wp_remote_retrieve_response_code( $response );
        if ( $http_code !== 200 ) {
            throw new Exception( sprintf( __( 'API Error (HTTP %d)', 'noodu-slide-generator' ), $http_code ) );
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( empty( $data['choices'][0]['message']['content'] ) ) {
            throw new Exception( __( 'Invalid API response format', 'noodu-slide-generator' ) );
        }

        $content = $data['choices'][0]['message']['content'];
        return json_decode( $content, true );
    }
}
?>
