<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Noodu_Database {

    public static function projects_table() {
        global $wpdb;
        return $wpdb->prefix . 'noodu_projects';
    }

    public static function revisions_table() {
        global $wpdb;
        return $wpdb->prefix . 'noodu_revisions';
    }

    public static function file_dir() {
        $upload_dir = wp_upload_dir();
        return trailingslashit( $upload_dir['basedir'] ) . 'noodu-slides/';
    }

    public static function file_url( $file_name ) {
        $upload_dir = wp_upload_dir();
        return trailingslashit( $upload_dir['baseurl'] ) . 'noodu-slides/' . rawurlencode( $file_name );
    }

    public static function get_projects( $user_id = null ) {
        global $wpdb;
        $table = self::projects_table();

        if ( null === $user_id ) {
            return $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC" );
        }

        return $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC", $user_id )
        );
    }

    public static function get_project( $project_id ) {
        global $wpdb;
        $table = self::projects_table();

        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $project_id )
        );
    }

    public static function create_project( $data ) {
        global $wpdb;

        $now = current_time( 'mysql' );

        $inserted = $wpdb->insert(
            self::projects_table(),
            array(
                'user_id'     => isset( $data['user_id'] ) ? (int) $data['user_id'] : 0,
                'name'        => isset( $data['name'] ) ? $data['name'] : '',
                'code'        => isset( $data['code'] ) ? $data['code'] : '',
                'prompt'      => isset( $data['prompt'] ) ? $data['prompt'] : '',
                'model'       => isset( $data['model'] ) ? $data['model'] : '',
                'status'      => isset( $data['status'] ) ? $data['status'] : 'generating',
                'pdf_file'    => '',
                'slide_count' => 0,
                'slides_data' => isset( $data['slides_data'] ) ? wp_json_encode( $data['slides_data'] ) : '',
                'created_at'  => $now,
                'updated_at'  => $now,
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
        );

        return $inserted ? (int) $wpdb->insert_id : 0;
    }

    public static function update_project( $project_id, $data ) {
        global $wpdb;

        $values = array();
        $format = array();

        $text_cols = array( 'name', 'code', 'prompt', 'model', 'status', 'pdf_file' );
        foreach ( $text_cols as $col ) {
            if ( isset( $data[ $col ] ) ) {
                $values[ $col ] = $data[ $col ];
                $format[]       = '%s';
            }
        }

        if ( isset( $data['slide_count'] ) ) {
            $values['slide_count'] = (int) $data['slide_count'];
            $format[]              = '%d';
        }

        if ( isset( $data['slides_data'] ) ) {
            $values['slides_data'] = is_string( $data['slides_data'] ) ? $data['slides_data'] : wp_json_encode( $data['slides_data'] );
            $format[]              = '%s';
        }

        if ( empty( $values ) ) {
            return false;
        }

        $values['updated_at'] = current_time( 'mysql' );
        $format[]             = '%s';

        return $wpdb->update(
            self::projects_table(),
            $values,
            array( 'id' => (int) $project_id ),
            $format,
            array( '%d' )
        );
    }

    public static function delete_project( $project_id ) {
        global $wpdb;

        $project = self::get_project( $project_id );
        if ( $project && $project->pdf_file ) {
            $base = self::file_dir() . $project->pdf_file;
            foreach ( array( $base, preg_replace( '/\.(pdf|html)$/', '.html', $base ), preg_replace( '/\.(pdf|html)$/', '.pdf', $base ) ) as $path ) {
                if ( $path && file_exists( $path ) ) {
                    @unlink( $path );
                }
            }
        }

        $wpdb->delete( self::revisions_table(), array( 'project_id' => (int) $project_id ), array( '%d' ) );

        return $wpdb->delete( self::projects_table(), array( 'id' => (int) $project_id ), array( '%d' ) );
    }

    public static function add_revision( $project_id, $prompt ) {
        global $wpdb;

        return $wpdb->insert(
            self::revisions_table(),
            array(
                'project_id' => (int) $project_id,
                'prompt'     => $prompt,
                'status'     => 'completed',
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%s', '%s', '%s' )
        );
    }

    public static function get_revisions( $project_id ) {
        global $wpdb;
        $table = self::revisions_table();

        return $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM $table WHERE project_id = %d ORDER BY created_at DESC", $project_id )
        );
    }
}
