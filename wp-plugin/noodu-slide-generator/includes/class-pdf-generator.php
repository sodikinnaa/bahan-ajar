<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Renders a deck using the Noodu Academy slide template: topbar with the
 * logo, two-column body, NOO callouts with the mascot, and a footer with
 * the progress bar and page number.
 */
class Noodu_PDF_Generator {

    const POSES = array( 'wave', 'blocks', 'tablet', 'laptop', 'book', 'thinking', 'pointing', 'cheer', 'thumbsup', 'scroll' );

    private $slides = array();
    private $meta = array();
    private $project_name = '';
    private $project_code = '';
    private $output_path = '';
    private $asset_cache = array();

    public function __construct( $project_name, $project_code, $output_path ) {
        $this->project_name = $project_name;
        $this->project_code = $project_code;
        $this->output_path  = $output_path;

        $this->meta = array(
            'module'  => $project_name,
            'brand'   => 'Noodu Academy',
            'badges'  => array(),
            'number'  => '',
            'project' => '',
            'tagline' => '',
            'note'    => '',
            'kicker'  => 'YUK, BELAJAR BARENG NOO!',
            'footnote' => 'Now. Opportunity. Do.',
        );
    }

    public function set_slides( $data ) {
        if ( ! is_array( $data ) ) {
            return;
        }

        if ( isset( $data['meta'] ) && is_array( $data['meta'] ) ) {
            foreach ( $this->meta as $key => $default ) {
                if ( isset( $data['meta'][ $key ] ) ) {
                    $this->meta[ $key ] = $data['meta'][ $key ];
                }
            }
        }

        $list = isset( $data['slides'] ) ? $data['slides'] : $data;
        if ( ! is_array( $list ) ) {
            return;
        }

        foreach ( $list as $slide ) {
            if ( is_array( $slide ) ) {
                $this->slides[] = $slide;
            }
        }
    }

    public function get_slide_count() {
        return count( $this->slides );
    }

    /* ---------- assets ---------- */

    private function brand_dir() {
        return NOODU_PLUGIN_DIR . 'assets/brand/';
    }

    /**
     * Data URIs keep the downloaded HTML deck self-contained, so it renders
     * the same whether Chromium opens it from disk or someone emails it on.
     */
    private function asset_uri( $file ) {
        if ( isset( $this->asset_cache[ $file ] ) ) {
            return $this->asset_cache[ $file ];
        }

        $path = $this->brand_dir() . $file;
        $uri  = '';

        if ( is_readable( $path ) ) {
            $uri = 'data:image/png;base64,' . base64_encode( file_get_contents( $path ) );
        }

        $this->asset_cache[ $file ] = $uri;
        return $uri;
    }

    private function pose_uri( $pose ) {
        $pose = strtolower( preg_replace( '/[^a-z]/i', '', (string) $pose ) );
        if ( ! in_array( $pose, self::POSES, true ) ) {
            $pose = 'wave';
        }
        return $this->asset_uri( 'noo-' . $pose . '.png' );
    }

    /**
     * Fonts are embedded rather than linked so a deck renders identically
     * on a server with no outbound network, and after the file is moved.
     */
    private function template_css() {
        $fonts = @file_get_contents( $this->brand_dir() . 'fonts.css' );
        $css   = @file_get_contents( $this->brand_dir() . 'noodu-slides.css' );

        if ( false === $css ) {
            return (string) $fonts;
        }

        $css = preg_replace( '#@import\s+url\([^)]*\);#', '', $css );

        return ( false === $fonts ? '' : $fonts . "\n" ) . $css;
    }

    /* ---------- slide parts ---------- */

    /**
     * Cover titles in this template are parallel sentences stacked one per
     * line, so a title like "Cari produknya. Temukan yang pas." breaks after
     * each full stop rather than wrapping wherever the column runs out.
     */
    private function title_html( $title, $stack = false ) {
        $title = (string) $title;

        if ( false !== strpos( $title, "\n" ) ) {
            $lines = preg_split( '/\R+/', $title );
        } elseif ( $stack ) {
            $lines = preg_split( '/(?<=\.)\s+/', trim( $title ) );
        } else {
            $lines = array( $title );
        }

        $lines = array_filter( array_map( 'trim', $lines ), 'strlen' );

        return implode( '<br>', array_map( 'esc_html', $lines ) );
    }

    private function topbar( $slide ) {
        $label = isset( $slide['label'] ) ? $slide['label'] : '';

        return '<header class="topbar">'
            . '<img class="topbar__logo" src="' . esc_attr( $this->asset_uri( 'noodu-logo.png' ) ) . '" alt="noodu">'
            . '<span class="topbar__rule"></span>'
            . '<span class="topbar__meta">' . esc_html( $this->meta['module'] ) . '</span>'
            . '<span class="topbar__label">' . esc_html( $label ) . '</span>'
            . '</header>';
    }

    private function footer( $index, $total ) {
        $num   = $index + 1;
        $width = $total > 0 ? ( $num / $total ) * 100 : 0;

        return '<footer class="footer">'
            . '<span>' . esc_html( $this->meta['brand'] ) . '</span>'
            . '<span class="footer__track"><span class="footer__bar" style="width:' . esc_attr( round( $width, 2 ) ) . '%"></span></span>'
            . '<span class="footer__num">' . esc_html( str_pad( $num, 2, '0', STR_PAD_LEFT ) . ' / ' . $total ) . '</span>'
            . '</footer>';
    }

    private function noo_says( $text ) {
        if ( '' === trim( (string) $text ) ) {
            return '';
        }

        return '<div class="noo-says">'
            . '<img src="' . esc_attr( $this->pose_uri( 'pointing' ) ) . '" alt="">'
            . '<div><h3>NOO bilang</h3><p>' . wp_kses_post( $text ) . '</p></div>'
            . '</div>';
    }

    private function buddy( $buddy ) {
        if ( ! is_array( $buddy ) || empty( $buddy['text'] ) ) {
            return '';
        }

        $tone  = isset( $buddy['tone'] ) ? $buddy['tone'] : '';
        $class = 'noo-buddy';
        if ( in_array( $tone, array( 'green', 'orange' ), true ) ) {
            $class .= ' noo-buddy--' . $tone;
        }
        if ( ! empty( $buddy['left'] ) ) {
            $class .= ' noo-buddy--left';
        }

        $pose = isset( $buddy['pose'] ) ? $buddy['pose'] : 'tablet';

        return '<div class="' . esc_attr( $class ) . '">'
            . '<div class="noo-buddy__bubble">'
            . '<span class="noo-buddy__tag">Teman belajarmu · NOO</span>'
            . '<p>' . wp_kses_post( $buddy['text'] ) . '</p>'
            . '</div>'
            . '<img src="' . esc_attr( $this->pose_uri( $pose ) ) . '" alt="">'
            . '</div>';
    }

    private function cover( $slide ) {
        $badges = '';
        if ( ! empty( $this->meta['badges'] ) && is_array( $this->meta['badges'] ) ) {
            $badges .= '<div class="badges">';
            foreach ( array_slice( $this->meta['badges'], 0, 3 ) as $badge ) {
                $badges .= '<span class="badge">' . esc_html( $badge ) . '</span>';
            }
            $badges .= '</div>';
        }

        $lead = isset( $slide['lead'] ) ? $slide['lead'] : '';
        $pose = isset( $slide['mascot'] ) ? $slide['mascot'] : 'wave';

        $left = $badges
            . '<h1>' . $this->title_html( $slide['title'], true ) . '</h1>'
            . ( $lead ? '<p class="lead">' . wp_kses_post( $lead ) . '</p>' : '' )
            . '<div class="cover-rule"></div>'
            . ( $this->meta['project'] ? '<p class="cover-project"><b>Project:</b> ' . esc_html( $this->meta['project'] ) . '</p>' : '' )
            . ( $this->meta['note'] ? '<p class="cover-meta">' . esc_html( $this->meta['note'] ) . '</p>' : '' );

        $card = '<div class="cover-card">'
            . '<span class="cover-card__circle"></span>'
            . '<span class="cover-card__blob"></span>'
            . '<p class="cover-card__kicker">' . esc_html( $this->meta['kicker'] ) . '</p>'
            . '<p class="cover-card__note">Teman belajar. Partner berkarya.</p>'
            . ( $this->meta['number'] ? '<span class="cover-card__num">' . esc_html( $this->meta['number'] ) . '</span>' : '' )
            . '<img class="cover-card__art" src="' . esc_attr( $this->pose_uri( $pose ) ) . '" alt="">'
            . ( $this->meta['tagline'] ? '<p class="cover-card__line">' . esc_html( $this->meta['tagline'] ) . '</p>' : '' )
            . '<p class="cover-card__sub">' . esc_html( $this->meta['footnote'] ) . '</p>'
            . '</div>';

        return '<div class="cover-grid"><div>' . $left . '</div>' . $card . '</div>';
    }

    private function body( $slide ) {
        $left  = isset( $slide['left'] ) ? $slide['left'] : '';
        $right = isset( $slide['right'] ) ? $slide['right'] : '';

        // Older/looser payloads just carry one content blob.
        if ( '' === $left && '' === $right && isset( $slide['content'] ) ) {
            $left = $slide['content'];
        }

        $extra = '';
        if ( ! empty( $slide['noo_says'] ) ) {
            $extra = $this->noo_says( $slide['noo_says'] );
        }

        if ( '' === trim( (string) $right ) ) {
            return '<div class="slide-body">' . wp_kses_post( $left ) . $extra . '</div>';
        }

        $layout = isset( $slide['layout'] ) ? $slide['layout'] : 'two-col';
        $class  = 'cols';
        if ( 'wide-left' === $layout ) {
            $class .= ' cols--wide-left';
        } elseif ( 'wide-right' === $layout ) {
            $class .= ' cols--wide-right';
        }

        return '<div class="' . esc_attr( $class ) . '">'
            . '<div>' . wp_kses_post( $left ) . '</div>'
            . '<div>' . wp_kses_post( $right ) . $extra . '</div>'
            . '</div>';
    }

    private function generate_html() {
        $total = count( $this->slides );
        $css   = $this->template_css();

        $html  = "<!DOCTYPE html>\n<html lang=\"id\">\n<head>\n<meta charset=\"utf-8\">\n";
        $html .= '<title>' . esc_html( $this->project_name ) . "</title>\n";
        $html .= "<style>\n" . $css . "\n.slide-body{font-size:20px;line-height:1.45}\n</style>\n";
        $html .= "</head>\n<body>\n";

        foreach ( $this->slides as $i => $slide ) {
            $type  = isset( $slide['type'] ) ? $slide['type'] : 'normal';
            $class = 'slide';

            if ( 'cover' === $type ) {
                $class .= ' slide--cover';
            } elseif ( 'dark' === $type ) {
                $class .= ' slide--dark';
            }

            $title = isset( $slide['title'] ) ? $slide['title'] : '';

            $html .= '<section class="' . esc_attr( $class ) . '">';
            $html .= $this->topbar( $slide );

            if ( 'cover' === $type ) {
                $html .= $this->cover( $slide );
            } else {
                $html .= '<h1>' . $this->title_html( $title ) . '</h1>';
                $html .= $this->body( $slide );
            }

            if ( ! empty( $slide['buddy'] ) ) {
                $html .= $this->buddy( $slide['buddy'] );
            }

            $html .= $this->footer( $i, $total );
            $html .= "</section>\n";
        }

        $html .= "</body>\n</html>";

        return $html;
    }

    /* ---------- rendering ---------- */

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
                . ' --virtual-time-budget=10000'
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

        return basename( $html_path );
    }
}
