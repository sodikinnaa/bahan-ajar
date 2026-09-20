<?php

class Noodu_Database {

    public static function get_projects( $user_id = null ) {
        global $wpdb;

        $query = "SELECT * FROM {$wpdb->prefix}noodu_projects";
        if ( $user_id ) {
            $query .= $wpdb->prepare( ' WHERE user_id = %d', $user_id );
        }
        $query .= ' ORDER BY created_at DESC';

        return $wpdb->get_results( $query );
    }

    public static function get_project( $project_id ) {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}noodu_projects WHERE id = %d",
                $project_id
            )
        );
    }

    public static function create_project( $data ) {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'noodu_projects',
            array(
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'code' => $data['code'],
                'prompt' => $data['prompt'],
                'model' => $data['model'],
                'status' => $data['status'] ?? 'generating',
                'slides_data' => isset( $data['slides_data'] ) ? wp_json_encode( $data['slides_data'] ) : ''
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        return $wpdb->insert_id;
    }

    public static function update_project( $project_id, $data ) {
        global $wpdb;

        $update_data = array();
        $format = array();

        foreach ( $data as $key => $value ) {
            if ( in_array( $key, array( 'name', 'status', 'pdf_file', 'code', 'prompt', 'model' ), true ) ) {
                $update_data[ $key ] = $value;
                $format[] = '%s';
            } elseif ( in_array( $key, array( 'slide_count', 'user_id' ), true ) ) {
                $update_data[ $key ] = $value;
                $format[] = '%d';
            } elseif ( $key === 'slides_data' ) {
                $update_data[ $key ] = wp_json_encode( $value );
                $format[] = '%s';
            }
        }

        $update_data['updated_at'] = current_time( 'mysql' );
        $format[] = '%s';

        return $wpdb->update(
            $wpdb->prefix . 'noodu_projects',
            $update_data,
            array( 'id' => $project_id ),
            $format,
            array( '%d' )
        );
    }

    public static function delete_project( $project_id ) {
        global $wpdb;

        return $wpdb->delete(
            $wpdb->prefix . 'noodu_projects',
            array( 'id' => $project_id ),
            array( '%d' )
        );
    }

    public static function add_revision( $project_id, $prompt ) {
        global $wpdb;

        return $wpdb->insert(
            $wpdb->prefix . 'noodu_revisions',
            array(
                'project_id' => $project_id,
                'prompt' => $prompt,
                'status' => 'pending'
            ),
            array( '%d', '%s', '%s' )
        );
    }

    public static function get_revisions( $project_id ) {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}noodu_revisions WHERE project_id = %d ORDER BY created_at DESC",
                $project_id
            )
        );
    }
}
?>
