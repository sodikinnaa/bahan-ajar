<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Noodu_PDF_Generator {
    private $slides = array();
    private $project_name = '';
    private $project_code = '';
    private $output_path = '';

    public function __construct( $project_name, $project_code, $output_path ) {
        $this->project_name = $project_name;
        $this->project_code = $project_code;
        $this->output_path  = $output_path;
    }

    public function add_slide( $title, $content, $notes = '', $slide_type = 'normal' ) {
        $this->slides[] = array(
            'title'   => $title,
            'content' => $content,
            'notes'   => $notes,
            'type'    => $slide_type,
        );
    }

    public function set_slides( $slides_data ) {
        if ( ! is_array( $slides_data ) ) {
            return;
        }

        // Accept { "slides": [...] } or a bare [...] array.
        $list = isset( $slides_data['slides'] ) ? $slides_data['slides'] : $slides_data;
        if ( ! is_array( $list ) ) {
            return;
        }

        foreach ( $list as $slide ) {
            if ( ! is_array( $slide ) ) {
                continue;
            }
            $this->add_slide(
                isset( $slide['title'] ) ? $slide['title'] : '',
                isset( $slide['content'] ) ? $slide['content'] : '',
                isset( $slide['notes'] ) ? $slide['notes'] : '',
                isset( $slide['type'] ) ? $slide['type'] : 'normal'
            );
        }
    }

    public function get_slide_count() {
        return count( $this->slides );
    }

    /**
     * Chromium is only available on servers where exec() is enabled and a
     * browser binary is installed. Most shared hosting has neither, so the
     * HTML deck is the guaranteed deliverable and the PDF is a bonus.
     */
    public static function find_browser() {
        $candidates = array(
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/opt/pw-browsers/chromium-1194/chrome-linux/chrome',
        );

        foreach ( $candidates as $path ) {
            if ( @is_executable( $path ) ) {
                return $path;
            }
        }

        return false;
    }

    public static function exec_available() {
        if ( ! function_exists( 'exec' ) ) {
            return false;
        }
        $disabled = array_map( 'trim', explode( ',', (string) ini_get( 'disable_functions' ) ) );
        return ! in_array( 'exec', $disabled, true );
    }

    private function generate_html() {
        $title = esc_html( $this->project_name );

        $html  = "<!DOCTYPE html>\n<html lang=\"id\">\n<head>\n";
        $html .= "<meta charset=\"utf-8\">\n";
        $html .= "<title>{$title}</title>\n";
        $html .= "<link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap\">\n";
        $html .= "<style>\n" . $this->deck_css() . "</style>\n";
        $html .= "</head>\n<body>\n";

        foreach ( $this->slides as $slide ) {
            $class = 'slide';
            if ( 'cover' === $slide['type'] ) {
                $class .= ' slide--cover';
            } elseif ( 'dark' === $slide['type'] ) {
                $class .= ' slide--dark';
            }

            $html .= '  <section class="' . esc_attr( $class ) . "\">\n";
            $html .= '    <h1>' . esc_html( $slide['title'] ) . "</h1>\n";
            $html .= "    <div class=\"slide-content\">\n";
            $html .= wp_kses_post( $slide['content'] );
            $html .= "\n    </div>\n";
            $html .= "  </section>\n";
        }

        $html .= "</body>\n</html>";

        return $html;
    }

    private function deck_css() {
        return <<<CSS
:root{--ink:#0d203f;--ink-soft:#33507a;--paper:#fafaf6;--blue:#3366ff;--orange:#f2722b;--green:#49c08b;--line:#d9dde5}
*{box-sizing:border-box}
body{margin:0;background:#e8e9ec;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:var(--ink);-webkit-font-smoothing:antialiased}
.slide{position:relative;width:1280px;height:720px;margin:28px auto;padding:44px 58px 40px;background:var(--paper);overflow:hidden;box-shadow:0 10px 34px rgba(13,32,63,.16)}
.slide::before{content:'';position:absolute;inset:0 auto 0 0;width:5px;background:var(--blue)}
.slide--dark{background:var(--ink);color:#fff}
.slide h1{margin:0 0 30px;font-size:46px;font-weight:800;letter-spacing:-.028em;line-height:1.1}
.slide-content{font-size:20px;line-height:1.5}
.slide-content ul,.slide-content ol{margin:0 0 18px;padding-left:26px}
.slide-content li{margin-bottom:12px}
.slide-content p{margin:0 0 16px}
.slide-content table{width:100%;border-collapse:collapse;font-size:18px}
.slide-content th{text-align:left;padding:0 14px 10px 0;border-bottom:2px solid var(--ink);font-size:13px;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-soft)}
.slide-content td{padding:11px 14px 11px 0;border-bottom:1px solid var(--line);vertical-align:top}
.slide-content code,.slide-content pre{font-family:'JetBrains Mono',monospace}
.slide-content pre{background:var(--ink);color:#eef2f9;border-radius:12px;padding:20px 24px;font-size:15px;line-height:1.6;white-space:pre-wrap}
.slide-content code{background:#e2f4eb;padding:2px 7px;border-radius:5px;font-size:.86em}
.slide-content pre code{background:none;padding:0}
@page{size:13.333in 7.5in;margin:0}
@media print{body{background:#fff}.slide{margin:0;box-shadow:none;break-after:page}.slide:last-child{break-after:auto}}
CSS;
    }

    /**
     * Writes the HTML deck, then tries to render a PDF from it.
     * Returns the basename of whichever file the user can download.
     */
    public function generate() {
        $html_path = $this->output_path . '.html';
        $pdf_path  = $this->output_path . '.pdf';

        if ( false === file_put_contents( $html_path, $this->generate_html() ) ) {
            throw new Exception( __( 'Could not write the deck file. Check permissions on wp-content/uploads.', 'noodu-slide-generator' ) );
        }

        $browser = self::find_browser();
        if ( $browser && self::exec_available() ) {
            $cmd = escapeshellarg( $browser )
                . ' --headless --disable-gpu --no-sandbox --no-pdf-header-footer'
                . ' --print-to-pdf=' . escapeshellarg( $pdf_path )
                . ' ' . escapeshellarg( 'file://' . $html_path )
                . ' 2>&1';

            $output      = array();
            $return_code = 0;
            @exec( $cmd, $output, $return_code );

            if ( 0 === $return_code && file_exists( $pdf_path ) && filesize( $pdf_path ) > 0 ) {
                return basename( $pdf_path );
            }
        }

        // No browser available: the HTML deck is the deliverable.
        // Opening it and using Print > Save as PDF gives the same result.
        return basename( $html_path );
    }
}
