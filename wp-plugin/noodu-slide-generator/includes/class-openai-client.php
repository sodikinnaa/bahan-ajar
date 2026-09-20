<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Noodu_OpenAI_Client {
    private $base_url;
    private $api_key;
    private $model;

    public function __construct( $base_url, $api_key, $model = null ) {
        $this->base_url = self::normalize_base_url( $base_url );
        $this->api_key  = trim( $api_key );
        $this->model    = $model;
    }

    /**
     * People paste the base URL with or without /v1. Normalize to a form
     * that always ends in a single /v1 so endpoints append cleanly.
     */
    private static function normalize_base_url( $url ) {
        $url = rtrim( trim( $url ), '/' );
        if ( ! preg_match( '#/v\d+$#', $url ) ) {
            $url .= '/v1';
        }
        return $url;
    }

    public function set_model( $model ) {
        $this->model = $model;
    }

    private function headers() {
        return array(
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type'  => 'application/json',
        );
    }

    private function fail_from_response( $response ) {
        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( isset( $data['error']['message'] ) ) {
            return sprintf( 'HTTP %d — %s', $code, $data['error']['message'] );
        }

        return sprintf( 'HTTP %d — %s', $code, mb_substr( wp_strip_all_tags( (string) $body ), 0, 300 ) );
    }

    public function get_models() {
        $response = wp_remote_get(
            $this->base_url . '/models',
            array(
                'headers' => $this->headers(),
                'timeout' => 30,
            )
        );

        if ( is_wp_error( $response ) ) {
            throw new Exception( $response->get_error_message() );
        }

        if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            throw new Exception( $this->fail_from_response( $response ) );
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! isset( $data['data'] ) || ! is_array( $data['data'] ) ) {
            throw new Exception( __( 'The endpoint did not return a model list.', 'noodu-slide-generator' ) );
        }

        $models = array();
        foreach ( $data['data'] as $model ) {
            if ( isset( $model['id'] ) ) {
                $models[] = array( 'id' => $model['id'] );
            }
        }

        usort( $models, function ( $a, $b ) {
            return strcmp( $a['id'], $b['id'] );
        } );

        return $models;
    }

    private function system_prompt() {
        return "You are a slide author for Noodu Academy, an Indonesian course on building and publishing projects with AI.\n\n"
            . "Return ONLY a JSON object of this shape:\n"
            . '{"slides":[{"title":"...","type":"cover|normal|dark","content":"<HTML>","notes":"..."}]}' . "\n\n"
            . "Rules:\n"
            . "- Write in Indonesian unless the user's prompt is in another language.\n"
            . "- Titles are short statement sentences, not topic labels.\n"
            . "- One idea per slide. 12-20 slides for a full module.\n"
            . "- content is simple HTML: <p>, <ul>, <ol>, <li>, <table>, <pre><code>, <strong>.\n"
            . "- Keep each slide short enough to fit a 1280x720 canvas.\n"
            . "- The first slide has type \"cover\"; a closing slide has type \"dark\".\n"
            . "- notes is a one-line speaker note.";
    }

    public function generate_slide_content( $prompt, $context = array() ) {
        if ( empty( $this->model ) ) {
            throw new Exception( __( 'No model selected.', 'noodu-slide-generator' ) );
        }

        $user_content = $prompt;

        if ( ! empty( $context['reference'] ) ) {
            $user_content .= "\n\n--- Reference material extracted from the uploaded file ---\n" . $context['reference'];
        }

        $payload = array(
            'model'       => $this->model,
            'messages'    => array(
                array( 'role' => 'system', 'content' => $this->system_prompt() ),
                array( 'role' => 'user', 'content' => $user_content ),
            ),
            'temperature' => 0.7,
        );

        $response = wp_remote_post(
            $this->base_url . '/chat/completions',
            array(
                'headers' => $this->headers(),
                'body'    => wp_json_encode( $payload ),
                'timeout' => 180,
            )
        );

        if ( is_wp_error( $response ) ) {
            throw new Exception( $response->get_error_message() );
        }

        if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            throw new Exception( $this->fail_from_response( $response ) );
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( ! isset( $data['choices'][0]['message']['content'] ) ) {
            throw new Exception( __( 'The endpoint returned an unexpected response shape.', 'noodu-slide-generator' ) );
        }

        return self::parse_slides( $data['choices'][0]['message']['content'] );
    }

    /**
     * Models wrap JSON in prose or ``` fences often enough that a bare
     * json_decode drops perfectly good decks on the floor.
     */
    private static function parse_slides( $content ) {
        $content = trim( $content );

        $decoded = json_decode( $content, true );
        if ( is_array( $decoded ) ) {
            return $decoded;
        }

        if ( preg_match( '/```(?:json)?\s*(.+?)```/s', $content, $m ) ) {
            $decoded = json_decode( trim( $m[1] ), true );
            if ( is_array( $decoded ) ) {
                return $decoded;
            }
        }

        $start = strpos( $content, '{' );
        $end   = strrpos( $content, '}' );
        if ( false !== $start && false !== $end && $end > $start ) {
            $decoded = json_decode( substr( $content, $start, $end - $start + 1 ), true );
            if ( is_array( $decoded ) ) {
                return $decoded;
            }
        }

        throw new Exception( __( 'The model did not return valid JSON. Try again or pick a different model.', 'noodu-slide-generator' ) );
    }
}
