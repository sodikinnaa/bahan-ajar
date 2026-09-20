<?php
class PDFGenerator {
    private $slides = [];
    private $projectName = '';
    private $projectCode = '';
    private $outputPath = '';

    public function __construct($projectName, $projectCode, $outputPath) {
        $this->projectName = $projectName;
        $this->projectCode = $projectCode;
        $this->outputPath = $outputPath;
    }

    public function addSlide($title, $content, $notes = '', $slideType = 'normal') {
        $this->slides[] = [
            'title' => $title,
            'content' => $content,
            'notes' => $notes,
            'type' => $slideType
        ];
    }

    public function setSlides($slidesData) {
        if (is_array($slidesData) && isset($slidesData['slides'])) {
            foreach ($slidesData['slides'] as $slide) {
                $this->addSlide(
                    $slide['title'] ?? '',
                    $slide['content'] ?? '',
                    $slide['notes'] ?? '',
                    $slide['type'] ?? 'normal'
                );
            }
        }
    }

    private function generateHTML() {
        $brand = "Noodu Academy / Build &amp; Publish with AI";
        $meta = $this->projectName;

        $html = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$this->projectName}</title>
    <link rel="stylesheet" href="../assets/noodu-slides.css">
</head>
<body data-meta="{$meta}"
      data-brand="{$brand}"
      data-assets="../assets">

HTML;

        foreach ($this->slides as $index => $slide) {
            $slideClass = 'slide';
            if ($slide['type'] === 'cover') {
                $slideClass .= ' slide--cover';
            } elseif ($slide['type'] === 'dark') {
                $slideClass .= ' slide--dark';
            }

            $html .= "\n  <section class=\"{$slideClass}\" data-label=\"" . htmlspecialchars($slide['title']) . "\">\n";
            $html .= "    <h1>" . htmlspecialchars($slide['title']) . "</h1>\n";
            $html .= "    <div class=\"slide-content\">\n";
            $html .= $slide['content'];
            $html .= "\n    </div>\n";
            if (!empty($slide['notes'])) {
                $html .= "    <div class=\"slide-notes\" style=\"display:none;\">" . htmlspecialchars($slide['notes']) . "</div>\n";
            }
            $html .= "  </section>\n";
        }

        $html .= "\n  <script src=\"../assets/noodu-slides.js\"><\/script>\n";
        $html .= "</body>\n</html>";

        return $html;
    }

    public function generatePDF() {
        $htmlFile = $this->outputPath . '.html';
        $pdfFile = $this->outputPath . '.pdf';

        // Generate HTML file
        file_put_contents($htmlFile, $this->generateHTML());

        // Use headless browser to generate PDF
        $browserPath = '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';
        if (!file_exists($browserPath)) {
            $browserPath = 'chromium-browser'; // Fallback to system chromium
        }

        $cmd = sprintf(
            '%s --headless --no-sandbox --print-to-pdf="%s" --print-to-pdf-margin-top=0 --print-to-pdf-margin-bottom=0 --print-to-pdf-margin-left=0 --print-to-pdf-margin-right=0 "file://%s"',
            escapeshellarg($browserPath),
            escapeshellarg($pdfFile),
            escapeshellarg(realpath($htmlFile))
        );

        $output = null;
        $returnCode = null;
        exec($cmd, $output, $returnCode);

        // Cleanup HTML file
        @unlink($htmlFile);

        if ($returnCode !== 0 || !file_exists($pdfFile)) {
            throw new Exception('Failed to generate PDF. Browser output: ' . implode("\n", $output ?? []));
        }

        return $pdfFile;
    }

    public function getSlideCount() {
        return count($this->slides);
    }
}
?>
