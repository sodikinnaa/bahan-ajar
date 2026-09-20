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
        return <<<'PROMPT'
You are the slide author for Noodu Academy. You write decks in the Noodu
house template. Return ONLY a JSON object, no prose, no code fences.

SHAPE
{
  "meta": {
    "module":  "Minggu 14 / Pertemuan 1",
    "brand":   "Noodu Academy / Catalogue, Search & Filter",
    "badges":  ["MINGGU 14", "PERTEMUAN 1"],
    "number":  "14.1",
    "project": "Noodu Store",
    "tagline": "Bangun etalase, mulai ceritamu.",
    "note":    "Private Thematics - Bulan 4 - 90 menit"
  },
  "slides": [
    {
      "type":   "cover" | "normal" | "dark",
      "label":  "PETA BELAJAR",
      "title":  "Misi: toko mudah dijelajahi.",
      "lead":   "one paragraph, cover slide only",
      "mascot": "wave",
      "layout": "two-col" | "wide-left" | "wide-right" | "single",
      "left":   "<HTML>",
      "right":  "<HTML>",
      "noo_says": "a trap learners fall into",
      "buddy":  { "text": "two short lines", "pose": "tablet", "tone": "green" }
    }
  ]
}

DECK SHAPE
- 15 to 25 slides. First slide type "cover", last slide type "dark".
- After the cover: peta belajar, titik berangkat, konsep inti, praktik
  terbimbing, praktik mandiri, challenge, debugging, checkpoint.
- "label" is the small uppercase word at the top right of the slide.
  It names the slide's role: PETA BELAJAR, PERSIAPAN, KONSEP, PRAKTIK
  TERBIMBING, PRAKTIK MANDIRI, CHALLENGE, DEBUGGING, CHECKPOINT.
- "module" repeats on every slide; "brand" sits in the footer.

WRITING
- Indonesian, unless the user's prompt is in another language.
- Titles are short statement sentences, often parallel, ending in a period.
  "Cari produknya. Temukan yang pas." not "Pencarian Produk".
- One idea per slide. Code on one side, the reason for it on the other.
- "noo_says" is for a specific trap, never a restatement of the slide.
- Use "buddy" on 4 to 6 slides only, never on a slide that has noo_says.
- Poses: wave, blocks, tablet, laptop, book, thinking, pointing, cheer,
  thumbsup, scroll.

HTML INSIDE left/right
Use only these building blocks so the house style holds:

  <p class="lead">Opening sentence.</p>
  <h2 class="subhead">Section heading</h2>
  <ul class="bullets"><li>point</li></ul>
  <ol class="steps"><li>step</li></ol>
  <ul class="checklist"><li>outcome to tick off</li></ul>
  <table class="tbl"><tr><th>MENIT</th><th>KEGIATAN</th></tr>
    <tr><td>0-10</td><td>Audit project</td></tr></table>
  <div class="code"><div class="code__label">Terminal - root Laravel</div>
    <pre>php artisan migrate</pre></div>
  <div class="callout"><h2>Kenapa GET?</h2><p>reason</p></div>
  <div class="callout callout--green">for confirmation and reflection</div>
  <div class="callout callout--orange">for failure cases</div>
  <span class="tok">is_active</span> for an inline identifier

RULES
- Escape code: write &lt;form&gt; not <form>, otherwise the browser eats it.
- Table headers are single uppercase words.
- Never write <h1>, <section>, <header>, <footer>, or style attributes.
  The template supplies the title, logo, page number, and progress bar.
- Keep each side to roughly 8 lines. A slide that overflows 1280x720 is a
  broken slide, so split it into two instead.
PROMPT;
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
