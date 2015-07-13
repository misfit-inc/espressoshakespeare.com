<?php
// pager class
class mgk_pager{
	// pager flag
	var $pager_flag = 'pageoffset';	
	var $page_url   = 'admin.php?page=';
	
	function mgk_pager(){	
	}
	
	// get query limit
	function get_query_limit($per_page=50){
		global  $wpdb;
		$this->per_page     = $per_page;
		$userspage          = isset($_REQUEST[$this->pager_flag]) ? $_REQUEST[$this->pager_flag] : null;
		$this->current_page = (int) ( '' == $userspage ) ? 1 : $userspage;
		$offset             = ($this->current_page - 1) * $this->per_page;
		// send query limit
		return $wpdb->prepare(" LIMIT %d, %d", $offset, $this->per_page);
	}
	
	// pager_links
	function get_pager_links($page_url='',$args=false) {
		global $wpdb;
		
		// args
		$args        = (is_array($args)) ? $args : array();
		// total
		$row         = $wpdb->get_row("SELECT FOUND_ROWS() AS total_rows");	
		$total_rows  = $row->total_rows;
		$paging_text = "";
		// when greater
		if ( $total_rows > $this->per_page ) { // have to page the results
			// init arguments
			/*$args = array();	
			// alpha
			if(isset($_GET['alpha'])){	
				$args['alpha'] = urlencode($_GET['alpha']);
			}*/
			// query string append

			// text
			$paging_text = paginate_links( array(
				'total'    => ceil($total_rows / $this->per_page),
				'current'  => $this->current_page,
				'base'     => $page_url.((!preg_match('/\?/',$page_url)) ? '?' : '&').'%_%',
				'format'   => $this->pager_flag.'=%#%',
				'add_args' => $args
			) );
			
			// format
			if ( $paging_text ) {
				$paging_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s ' ) . '</span> %s',
							   number_format_i18n( ( $this->current_page - 1 ) * $this->per_page + 1 ),
							   number_format_i18n( min( $this->current_page * $this->per_page, $total_rows ) ),
							   number_format_i18n( $total_rows ),
							   $paging_text);
			}
		}	
		// return
		return str_replace('page-numbers','', $paging_text);
	}
}
// end of file