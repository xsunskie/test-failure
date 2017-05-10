<?php

add_filter( 'template_include', 'portfolio_page_template' );
$template = 1;
function portfolio_page_template( $template ) {
	if ( is_page( 'portfolio' )  ) {
		$new_template = locate_template( 'podcast-plugin.php'  );
		if ( '' != $new_template ) {
			return $new_template ;
		}
	}

	return $template;

}

?>