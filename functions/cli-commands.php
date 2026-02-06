<?php
/**
 * WP-CLI commands for Sapir Journal.
 *
 * Loaded only when WP_CLI is active (see functions.php guard).
 */

class Sapir_CLI_Commands {

    /**
     * Bulk-create draft articles for a new journal issue from a CSV file.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to the CSV file with article data.
     *
     * [--season=<season>]
     * : Season label for the issue (e.g. "Winter 2026").
     *
     * [--volume=<volume>]
     * : Volume label for the issue (e.g. "Volume Twenty").
     *
     * [--dry-run]
     * : Preview what would be created without writing to the database.
     *
     * [--format=<format>]
     * : Output format. Accepts: table, csv, json. Default: table.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     * ---
     *
     * ## EXAMPLES
     *
     *     wp sapir create-issue articles.csv --season="Winter 2026" --volume="Volume Twenty" --dry-run
     *     wp sapir create-issue articles.csv --season="Winter 2026" --volume="Volume Twenty" --format=csv
     *
     * @subcommand create-issue
     * @when after_wp_load
     */
    public function create_issue( $args, $assoc_args ) {
        $file    = $args[0];
        $season  = $assoc_args['season'] ?? '';
        $volume  = $assoc_args['volume'] ?? '';
        $dry_run = isset( $assoc_args['dry-run'] );
        $format  = $assoc_args['format'] ?? 'table';

        if ( ! $season || ! $volume ) {
            WP_CLI::error( 'Both --season and --volume are required.' );
        }

        // --- Parse CSV ---
        $rows = $this->parse_csv( $file );
        if ( is_wp_error( $rows ) ) {
            WP_CLI::error( $rows->get_error_message() );
        }

        WP_CLI::log( sprintf( 'Parsed %d articles from CSV.', count( $rows ) ) );

        if ( $dry_run ) {
            WP_CLI::log( '--- DRY RUN (no changes will be made) ---' );
        }

        // --- Find or create the Issue CPT entry ---
        // Use the Issue column from the first row as the issue title
        $issue_title = trim( $rows[0]['Issue'] ?? '' );
        if ( ! $issue_title ) {
            WP_CLI::error( 'CSV is missing an "Issue" column or the first row has no issue name.' );
        }

        $issue_id = $this->find_or_create_issue( $issue_title, $season, $volume, $dry_run );

        // --- Find or create the category ---
        $cat_name = trim( $rows[0]['Category'] ?? $issue_title );
        $cat_id   = $this->find_or_create_category( $cat_name, $dry_run );

        // --- Process each article ---
        $results    = [];
        $year       = date( 'Y' );
        $cat_slug   = sanitize_title( $cat_name );

        foreach ( $rows as $i => $row ) {
            $title = trim( $row['Title'] ?? '' );
            if ( ! $title ) {
                WP_CLI::warning( sprintf( 'Row %d: empty title, skipping.', $i + 2 ) );
                continue;
            }

            $slug = sanitize_title( $title );

            // Check for existing post by slug
            $existing = get_page_by_path( $slug, OBJECT, 'post' );
            if ( $existing ) {
                $url = $this->build_url( $cat_slug, $year, $slug );
                $results[] = [
                    'title'  => $title,
                    'status' => 'skipped',
                    'url'    => $url,
                    'id'     => $existing->ID,
                ];
                WP_CLI::warning( sprintf( 'Skipped "%s" — slug already exists (ID %d).', $title, $existing->ID ) );
                continue;
            }

            // Parse authors and interviewers
            $author_names      = $this->parse_names( $row['Authors'] ?? '' );
            $interviewer_names = $this->parse_names( $row['Interviewers'] ?? '' );

            $author_ids      = [];
            $interviewer_ids = [];

            foreach ( $author_names as $name ) {
                $author_ids[] = $this->find_or_create_author( $name, $dry_run );
            }
            foreach ( $interviewer_names as $name ) {
                $interviewer_ids[] = $this->find_or_create_author( $name, $dry_run );
            }

            // Filter out nulls from dry-run placeholder IDs
            $author_ids      = array_filter( $author_ids );
            $interviewer_ids = array_filter( $interviewer_ids );

            $url = $this->build_url( $cat_slug, $year, $slug );

            if ( $dry_run ) {
                $results[] = [
                    'title'   => $title,
                    'status'  => 'would create',
                    'url'     => $url,
                    'id'      => '-',
                    'authors' => implode( ', ', $author_names ),
                ];
                continue;
            }

            // Create the draft post
            $post_id = wp_insert_post( [
                'post_title'    => $title,
                'post_name'     => $slug,
                'post_status'   => 'draft',
                'post_type'     => 'post',
                'post_category' => [ $cat_id ],
            ], true );

            if ( is_wp_error( $post_id ) ) {
                WP_CLI::warning( sprintf( 'Failed to create "%s": %s', $title, $post_id->get_error_message() ) );
                continue;
            }

            // Set ACF fields
            update_field( 'field_605cd86033e5b', $issue_id, $post_id );        // issue (post_object)
            update_field( 'field_605cd85233e5a', $author_ids, $post_id );      // author (post_object, multiple)

            if ( ! empty( $interviewer_ids ) ) {
                update_field( 'field_64078dff145fe', $interviewer_ids, $post_id ); // interviewers
            }

            $results[] = [
                'title'   => $title,
                'status'  => 'created',
                'url'     => $url,
                'id'      => $post_id,
                'authors' => implode( ', ', $author_names ),
            ];

            WP_CLI::log( sprintf( 'Created "%s" (ID %d)', $title, $post_id ) );
        }

        // --- Output results ---
        $this->output_results( $results, $format, $dry_run );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Parse a CSV file and return an array of associative arrays.
     */
    private function parse_csv( $file ) {
        if ( ! file_exists( $file ) ) {
            return new WP_Error( 'file_not_found', "CSV file not found: $file" );
        }

        $handle = fopen( $file, 'r' );
        if ( ! $handle ) {
            return new WP_Error( 'file_unreadable', "Could not open CSV file: $file" );
        }

        $headers = fgetcsv( $handle );
        if ( ! $headers ) {
            fclose( $handle );
            return new WP_Error( 'empty_csv', 'CSV file is empty or has no header row.' );
        }

        // Trim BOM and whitespace from headers
        $headers = array_map( function( $h ) {
            return trim( $h, "\xEF\xBB\xBF \t\n\r\0\x0B" );
        }, $headers );

        $rows = [];
        while ( ( $data = fgetcsv( $handle ) ) !== false ) {
            if ( count( $data ) === 1 && $data[0] === null ) {
                continue; // Skip empty lines
            }
            $row = [];
            foreach ( $headers as $idx => $header ) {
                $row[ $header ] = $data[ $idx ] ?? '';
            }
            $rows[] = $row;
        }

        fclose( $handle );

        if ( empty( $rows ) ) {
            return new WP_Error( 'no_data', 'CSV file has headers but no data rows.' );
        }

        return $rows;
    }

    /**
     * Parse a comma-separated list of names, handling quoted values.
     */
    private function parse_names( $value ) {
        $value = trim( $value );
        if ( $value === '' ) {
            return [];
        }

        // Split on commas, trim each
        $names = array_map( 'trim', explode( ',', $value ) );
        return array_filter( $names );
    }

    /**
     * Split a full name into first + last on the last space.
     * "Bret Stephens" -> ["Bret", "Stephens"]
     * "Mary Jane Watson" -> ["Mary Jane", "Watson"]
     */
    private function split_name( $full_name ) {
        $full_name = trim( $full_name );
        $last_space = strrpos( $full_name, ' ' );

        if ( $last_space === false ) {
            return [ $full_name, '' ];
        }

        return [
            substr( $full_name, 0, $last_space ),
            substr( $full_name, $last_space + 1 ),
        ];
    }

    /**
     * Find or create a category by name. Returns term ID.
     */
    private function find_or_create_category( $name, $dry_run ) {
        $term = get_term_by( 'name', $name, 'category' );
        if ( $term ) {
            WP_CLI::log( sprintf( 'Found existing category: "%s" (ID %d)', $name, $term->term_id ) );
            return $term->term_id;
        }

        if ( $dry_run ) {
            WP_CLI::log( sprintf( 'Would create category: "%s"', $name ) );
            return 0;
        }

        $result = wp_insert_term( $name, 'category' );
        if ( is_wp_error( $result ) ) {
            WP_CLI::error( sprintf( 'Failed to create category "%s": %s', $name, $result->get_error_message() ) );
        }

        WP_CLI::success( sprintf( 'Created category: "%s" (ID %d)', $name, $result['term_id'] ) );
        return $result['term_id'];
    }

    /**
     * Find or create an Issue CPT entry. Returns post ID.
     */
    private function find_or_create_issue( $title, $season, $volume, $dry_run ) {
        $existing = get_posts( [
            'post_type'      => 'issue',
            'title'          => $title,
            'post_status'    => 'any',
            'posts_per_page' => 1,
        ] );

        if ( ! empty( $existing ) ) {
            WP_CLI::log( sprintf( 'Found existing issue: "%s" (ID %d)', $title, $existing[0]->ID ) );
            return $existing[0]->ID;
        }

        if ( $dry_run ) {
            WP_CLI::log( sprintf( 'Would create issue: "%s" (season: %s, volume: %s)', $title, $season, $volume ) );
            return 0;
        }

        $post_id = wp_insert_post( [
            'post_title'  => $title,
            'post_type'   => 'issue',
            'post_status' => 'publish',
        ], true );

        if ( is_wp_error( $post_id ) ) {
            WP_CLI::error( sprintf( 'Failed to create issue "%s": %s', $title, $post_id->get_error_message() ) );
        }

        update_field( 'field_6066107c07bda', $season, $post_id ); // season
        update_field( 'field_6066108207bdb', $volume, $post_id ); // volume

        WP_CLI::success( sprintf( 'Created issue: "%s" (ID %d)', $title, $post_id ) );
        return $post_id;
    }

    /**
     * Find or create an Author CPT entry. Returns post ID.
     */
    private function find_or_create_author( $full_name, $dry_run ) {
        $existing = get_posts( [
            'post_type'      => 'authors',
            'title'          => $full_name,
            'post_status'    => 'any',
            'posts_per_page' => 1,
        ] );

        if ( ! empty( $existing ) ) {
            return $existing[0]->ID;
        }

        if ( $dry_run ) {
            WP_CLI::log( sprintf( 'Would create author: "%s"', $full_name ) );
            return null;
        }

        $post_id = wp_insert_post( [
            'post_title'  => $full_name,
            'post_type'   => 'authors',
            'post_status' => 'publish',
        ], true );

        if ( is_wp_error( $post_id ) ) {
            WP_CLI::warning( sprintf( 'Failed to create author "%s": %s', $full_name, $post_id->get_error_message() ) );
            return null;
        }

        list( $first, $last ) = $this->split_name( $full_name );
        update_field( 'field_63c5b45f618a5', $first, $post_id ); // first_name
        update_field( 'field_63c5b4330ff52', $last, $post_id );  // last_name

        WP_CLI::log( sprintf( 'Created author: "%s" (ID %d)', $full_name, $post_id ) );
        return $post_id;
    }

    /**
     * Build the expected pretty URL for an article.
     */
    private function build_url( $cat_slug, $year, $post_slug ) {
        return sprintf( '/%s/%s/%s/', $cat_slug, $year, $post_slug );
    }

    /**
     * Output results in the requested format.
     */
    private function output_results( $results, $format, $dry_run ) {
        if ( empty( $results ) ) {
            WP_CLI::warning( 'No articles processed.' );
            return;
        }

        switch ( $format ) {
            case 'json':
                WP_CLI::line( json_encode( $results, JSON_PRETTY_PRINT ) );
                break;

            case 'csv':
                // CSV for print team — just title and URL
                WP_CLI::line( 'Title,URL,Status' );
                foreach ( $results as $row ) {
                    WP_CLI::line( sprintf(
                        '"%s",%s,%s',
                        str_replace( '"', '""', $row['title'] ),
                        $row['url'],
                        $row['status']
                    ) );
                }
                break;

            default: // table
                $table_data = array_map( function( $row ) {
                    return [
                        'Title'  => $row['title'],
                        'Status' => $row['status'],
                        'URL'    => $row['url'],
                        'ID'     => $row['id'] ?? '-',
                    ];
                }, $results );

                WP_CLI\Utils\format_items( 'table', $table_data, [ 'Title', 'Status', 'URL', 'ID' ] );
                break;
        }

        $counts = array_count_values( array_column( $results, 'status' ) );
        $summary_parts = [];
        foreach ( $counts as $status => $count ) {
            $summary_parts[] = "$count $status";
        }

        WP_CLI::log( '' );
        WP_CLI::log( 'Summary: ' . implode( ', ', $summary_parts ) );

        if ( $dry_run ) {
            WP_CLI::log( 'Re-run without --dry-run to execute.' );
        }
    }
}

WP_CLI::add_command( 'sapir', 'Sapir_CLI_Commands' );
