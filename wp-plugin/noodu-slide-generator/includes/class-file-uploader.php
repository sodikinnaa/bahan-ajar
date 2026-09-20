<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Noodu_File_Uploader {
    private $upload_dir;
    private $allowed_types = array( 'application/pdf', 'image/png', 'image/jpeg' );
    private $max_file_size = 10485760; // 10 MB

    public function __construct() {
        $this->upload_dir = Noodu_Database::file_dir();
        if ( ! is_dir( $this->upload_dir ) ) {
            wp_mkdir_p( $this->upload_dir );
        }
    }

    public function upload( $field ) {
        if ( empty( $_FILES[ $field ] ) || UPLOAD_ERR_OK !== $_FILES[ $field ]['error'] ) {
            throw new Exception( __( 'No file was uploaded.', 'noodu-slide-generator' ) );
        }

        $file = $_FILES[ $field ];

        if ( $file['size'] > $this->max_file_size ) {
            throw new Exception( __( 'The file is larger than 10 MB.', 'noodu-slide-generator' ) );
        }

        $check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
        $type  = $check['type'] ? $check['type'] : '';

        if ( ! in_array( $type, $this->allowed_types, true ) ) {
            throw new Exception( __( 'Only PDF, PNG, and JPEG files are accepted.', 'noodu-slide-generator' ) );
        }

        $ext  = pathinfo( $file['name'], PATHINFO_EXTENSION );
        $name = 'ref-' . wp_generate_password( 12, false, false ) . ( $ext ? '.' . strtolower( $ext ) : '' );
        $path = $this->upload_dir . $name;

        if ( ! move_uploaded_file( $file['tmp_name'], $path ) ) {
            throw new Exception( __( 'Could not save the uploaded file.', 'noodu-slide-generator' ) );
        }

        return array(
            'name' => $file['name'],
            'path' => $path,
            'file' => $name,
            'type' => $type,
            'size' => (int) $file['size'],
        );
    }

    public function extract_text_from_pdf( $path ) {
        if ( ! file_exists( $path ) ) {
            throw new Exception( __( 'Reference file not found.', 'noodu-slide-generator' ) );
        }

        if ( ! Noodu_PDF_Generator::exec_available() ) {
            throw new Exception( __( 'This server cannot run pdftotext, so the PDF was skipped.', 'noodu-slide-generator' ) );
        }

        $output = @shell_exec( 'pdftotext ' . escapeshellarg( $path ) . ' - 2>/dev/null' );

        if ( null === $output || '' === trim( (string) $output ) ) {
            throw new Exception( __( 'Could not read text from the PDF. Install poppler-utils, or paste the content into the prompt instead.', 'noodu-slide-generator' ) );
        }

        return trim( $output );
    }

    public function cleanup( $path ) {
        if ( $path && file_exists( $path ) ) {
            return @unlink( $path );
        }
        return true;
    }
}
