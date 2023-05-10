<?php

namespace Curtis;
use WP_REST_Request;
use WP_REST_Response;


/**
 * Handles HTML rendering of the table,
 * modified by block attributes to toggle showing columns
 * populated by external API data passed through our REST endpoint
 *
 * @since 1.0.0
 */
class Table {

	/**
	 * Attributes from the saved block
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
    public $attr;

	/**
	 * JSON data response from the API
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
    public $json;

	/**
	 * Core constructor.
	 *
	 * @since 1.0.0
	 */
    function __construct( $attributes = null ) {
		// pass along attributes saved in block, if this is a view render
		$this->attr = $attributes;
        $this->restCall( '/curtis/v1/apicall' );
    }

	/**
	 * Calls a REST endpoint to fetch data for the table
	 *
	 * @since 1.0.0
	 */
    public function restCall( $url = null ) {
        if ( is_null( $url ) ) return;
        $request = new WP_REST_Request( 'GET', $url );
        $response = rest_do_request( $request );
        if ( $response->is_error() ) {
            $this->json = null;
        } else {
			$this->json = $response->get_data();
        }
    }

	/**
	 * Assemble and output HTMl for title & table from the data parsing methods
	 *
	 * @since 1.0.0
	 */
	public function renderTable() {
		$this->renderTableTitle();
		$this->renderTableBody();
	}

	/**
	 * Parse json data and output HTML for the title above the table
	 *
	 * @since 1.0.0
	 */
    private function renderTableTitle() {
        if ( empty( $this->json ) ) {
            echo '<h3>'.__( "Error: Data Missing", 'curtis-api' ).'</h3>';
            return;
        }
        echo '<h3>'.esc_html( $this->json[ 'title' ] ).'</h3>';
    }

	/**
	 * Parse json data and output HTML of the table rows
	 *
	 * @since 1.0.0
	 */
	private function renderTableBody() {
        if ( empty( $this->json ) ) {
            echo '<div class="status">';
            esc_html_e( 'Sorry, there was a problem retrieving the data for this table!', 'curtis-api');
            echo '</div>';
            return;
        }
		echo '<div class="table">';
		// header row requires different parsing of JSON because of how data is structured
		echo '<div class="tr th">';
		$col = 1;
		foreach ( $this->json[ 'data' ][ 'headers' ] as $header ) {
			// if saved attributes exist, check to render column or not
			if ( is_null( $this->attr ) || $this->attr[ "col".$col ] ) {
				echo '<div class="td col'.$col.'">'.esc_html( $header ).'</div>';
			}
			$col++;
		}
		echo "</div>";

		// output rest of table rows
		foreach ( $this->json[ 'data' ][ 'rows' ] as $rows ) {
			echo '<div class="tr">';
			$col = 1;
			foreach ( $rows as $key => $value ) {
				// if saved attributes exist, check to render column or not
				if ( is_null( $this->attr ) || $this->attr[ "col".$col ] ) {
					if ( $key == "date" ) {
						$value = date( 'n/j/Y', $value );
					}
					echo '<div class="td col'.$col.'">'.esc_html( $value ).'</div>';
				};
				$col++;
			}
			echo '</div>';
		}
		echo '</div>';
	}

}
