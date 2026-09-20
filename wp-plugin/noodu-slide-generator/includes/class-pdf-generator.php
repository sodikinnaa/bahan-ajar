<?php

class Noodu_PDF_Generator {
    private $slides = array();
    private $project_name = '';
    private $project_code = '';
    private $output_path = '';

    public function __construct( $project_name, $project_code, $output_path ) {
        $this->project_name = $project_name;
        $this->project_code = $project_code;
        $this->output_path = $output_path;
    }

    public function add_slide( $title, $content, $notes = '', $slide_type = 'normal' ) {
        $this->slides[] = array(
            'title' => $title,
            'content' => $content,
            'notes' => $notes,
            'type' => $slide_type
        );
    }

    public function set_slides( $slides_data ) {
        if ( is_array( $slides_data ) && isset( $slides_data['slides'] ) ) {
            foreach ( $slides_data['slides'] as $slide ) {
                $this->add_slide(
                    $slide['title'] ?? '',
                    $slide['content'] ?? '',
                    $slide['notes'] ?? '',
                    $slide['type'] ?? 'normal'
                );
            }
        }
    }

    private function generate_html() {
        $brand = 'Noodu Academy / Build &amp; Publish with AI';
        $meta = $this->project_name;

        $html = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$this->project_name}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: #e8e9ec; color: #0d203f; -webkit-font-smoothing: antialiased; }
        .slide { position: relative; width: 1280px; height: 720px; margin: 28px auto; padding: 44px 58px 40px; background: #fafaf6; overflow: hidden; box-shadow: 0 10px 34px rgba(13, 32, 63, .16); }
        .slide::before { content: ''; position: absolute; inset: 0 auto 0 0; width: 5px; background: #3366ff; }
        .slide h1 { margin: 22px 0 30px; font-size: 50px; font-weight: 800; letter-spacing: -.028em; line-height: 1.08; }
        .slide-content { font-size: 18px; line-height: 1.6; }
        @media print { @page { size: 13.333in 7.5in; margin: 0; } body { background: #fff; } .slide { margin: 0; box-shadow: none; break-after: page; } }
    </style>
</head>
<body>

HTML;

        foreach ( $this->slides as $index => $slide ) {
            $slide_class = 'slide';
            $title = htmlspecialchars( $slide['title'] );

            $html .= "  <section class=\"{$slide_class}\">\n";
            $html .= "    <h1>{$title}</h1>\n";
            $html .= "    <div class=\"slide-content\">\n";
            $html .= $slide['content'];
            $html .= "\n    </div>\n";
            $html .= "  </section>\n";
        }

        $html .= "</body>\n</html>";

        return $html;
    }

    public function generate_pdf() {
        $upload_dir = wp_upload_dir();
        $html_file = $this->output_path . '.html';
        $pdf_file = $this->output_path . '.pdf';

        // Generate HTML file
        file_put_contents( $html_file, $this->generate_html() );

        // Try to use Chromium for PDF generation
        $browser_path = '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';
        if ( ! file_exists( $browser_path ) ) {
            $browser_path = 'chromium-browser';
        }

        $cmd = sprintf(
            '%s --headless --no-sandbox --print-to-pdf="%s" --print-to-pdf-margin-top=0 --print-to-pdf-margin-bottom=0 --print-to-pdf-margin-left=0 --print-to-pdf-margin-right=0 "file://%s" 2>/dev/null',
            escapeshellarg( $browser_path ),
            escapeshellarg( $pdf_file ),
            escapeshellarg( realpath( $html_file ) )
        );

        exec( $cmd, $output, $return_code );

        // Cleanup HTML file
        @unlink( $html_file );

        if ( $return_code !== 0 || ! file_exists( $pdf_file ) ) {
            // Fallback: Return HTML file as downloadable
            rename( $html_file, str_replace( '.pdf', '.html', $pdf_file ) );
            return str_replace( '.pdf', '.html', $pdf_file );
        }

        return $pdf_file;
    }

    public function get_slide_count() {
        return count( $this->slides );
    }
}
?>
