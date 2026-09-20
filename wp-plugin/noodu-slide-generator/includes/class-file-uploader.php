<?php

class Noodu_File_Uploader {
    private $upload_dir;
    private $allowed_types = array( 'application/pdf', 'image/png', 'image/jpeg' );
    private $max_file_size = 10485760; // 10MB

    public function __construct() {
        $upload_dir_info = wp_upload_dir();
        $this->upload_dir = $upload_dir_info['basedir'] . '/noodu-slides';

        if ( ! is_dir( $this->upload_dir ) ) {
            wp_mkdir_p( $this->upload_dir );
        }
    }

    public function upload( $file_input ) {
        if ( ! isset( $_FILES[ $file_input ] ) || $_FILES[ $file_input ]['error'] !== UPLOAD_ERR_OK ) {
            throw new Exception( __( 'File upload failed or no file provided', 'noodu-slide-generator' ) );
        }

        $file = $_FILES[ $file_input ];
        $mime_type = mime_content_type( $file['tmp_name'] );

        if ( ! in_array( $mime_type, $this->allowed_types, true ) ) {
            throw new Exception( __( 'Invalid file type. Allowed: PDF, PNG, JPEG', 'noodu-slide-generator' ) );
        }

        if ( $file['size'] > $this->max_file_size ) {
            throw new Exception( __( 'File size exceeds 10MB limit', 'noodu-slide-generator' ) );
        }

        $file_name = bin2hex( random_bytes( 16 ) ) . '.' . pathinfo( $file['name'], PATHINFO_EXTENSION );
        $file_path = $this->upload_dir . '/' . $file_name;

        if ( ! move_uploaded_file( $file['tmp_name'], $file_path ) ) {
            throw new Exception( __( 'Failed to move uploaded file', 'noodu-slide-generator' ) );
        }

        return array(
            'name' => $file['name'],
            'path' => $file_path,
            'file' => $file_name,
            'type' => $mime_type,
            'size' => $file['size'],
            'uploaded_at' => current_time( 'mysql' )
        );
    }

    public function extract_text_from_pdf( $file_path ) {
        if ( ! file_exists( $file_path ) ) {
            throw new Exception( __( 'File not found', 'noodu-slide-generator' ) );
        }

        // Use pdftotext command
        $output = shell_exec( 'pdftotext ' . escapeshellarg( $file_path ) . ' -' );

        if ( $output === null ) {
            throw new Exception( __( 'Failed to extract text from PDF. Ensure poppler-utils is installed.', 'noodu-slide-generator' ) );
        }

        return trim( $output );
    }

    public function cleanup( $file_path ) {
        if ( file_exists( $file_path ) ) {
            return unlink( $file_path );
        }
        return true;
    }
}
?>
