<?php
/*
Plugin Name: File List Shortcode
Plugin URI: https://www.pragmatticode.com
Description: Produces an unordered list of file based on supplied group(s); adds a "folder" taxonomy to media library.
Version: 1.0
Author: Matt Walters
Author URI: https://www.pragmatticode.com
License: GPL2
*/

// Register taxonomy for files
function prag_create_folder_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x( 'Folder', 'taxonomy general name' ),
        'singular_name'     => _x( 'Folder', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Folders' ),
        'all_items'         => __( 'All Folders' ),
        'parent_item'       => __( 'Parent Folder' ),
        'parent_item_colon' => __( 'Parent Folder:' ),
        'edit_item'         => __( 'Edit Folder' ),
        'update_item'       => __( 'Update Folder' ),
        'add_new_item'      => __( 'Add New Folder' ),
        'new_item_name'     => __( 'New Folder Name' ),
        'menu_name'         => __( 'Folders' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'folder' ),
    );

    register_taxonomy( 'folder', array( 'attachment' ), $args );
}
add_action( 'init', 'prag_create_folder_taxonomies', 0 );


// Add shortcode column to help user with shortcodes for file lists
function prag_folder_tax_columns($columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'shortcode' => __('Shortcode'),
        'posts' => __('Files')
        );
    return $new_columns;
}
add_filter("manage_edit-folder_columns", 'prag_folder_tax_columns');


// Add Shortcode helper to taxonomy listing
function prag_manage_folder_columns($out, $column_name, $folder_id) {
    $folder = get_term($folder_id, 'folder');
    switch ($column_name) {
        case 'shortcode':
            // get header image url
            $out .= '[file-list folder="' . $folder->slug . '"]';
            break;

        default:
            break;
    }
    return $out;
}
add_filter("manage_folder_custom_column", 'prag_manage_folder_columns', 10, 3);


// Add shortcode for listing Files within a Folder
function prag_file_list( $atts ) {
    $content = '';

    extract( shortcode_atts( array(
        'folder'   => '',
        'relation' => ''
    ), $atts ) );

    if ($relation != 'AND' && $relation != 'OR') {  // Make sure $relation has a valud value
        $relation = '';
    }

    if ($folder == '') {
        return '';
    } else {

        $folders     = explode(',', $folder);
        $folderCount = count($folders);
        $taxQuery    = array();

        if ($relation != '' && $folderCount > 1) {
            $taxQuery['relation'] = $relation;
        } elseif ($relation == '' && $folderCount > 1) {
            $taxQuery['relation'] = 'AND';
        }

        foreach ($folders as $f) {
            $taxQuery[] = array(
                'taxonomy' => 'folder',
                'field' => 'slug',
                'terms' => $f
            );
        }

        $args = array(
            'posts_per_page'  => -1,
            'orderby'         => 'title',
            'order'           => 'ASC',
            'post_type'       => 'attachment',
            'post_status'     => 'any',
            'tax_query'       => $taxQuery
        );
        $files = get_posts($args);

        $content = '<ul class="file-list">';
        foreach ($files as $file) {
            $permalink = wp_get_attachment_url($file->ID);
            $extension = pathinfo($permalink, PATHINFO_EXTENSION);

            $content .= '<li>';
                $content .= '<a target="_blank" title="View file: ' . $file->post_title . '" href="' . $permalink . '">' . $file->post_title . '</a> <small>(' . prag_bytesToSize(filesize(get_attached_file($file->ID))) .')</small>';
                $content .= '<div class="entry-summary">' . $file->post_content . '</div>';
            $content .= '</li>';
        }
        $content .= '</ul>';

    }

    return $content;
}
add_shortcode( 'file-list', 'prag_file_list' );

// Convert bytes to human readable format
function prag_bytesToSize($bytes, $precision = 2) {
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;

    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';

    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';

    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';

    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';

    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}
