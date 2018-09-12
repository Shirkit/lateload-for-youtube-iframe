<?php
/*
Plugin Name: LateLoad for YouTube Iframe
Author: Shirkit
*/

// Regular themes for pages
add_filter( 'the_content', 'filter_output_html', 10000, 2);

// Oxygen Builder support
add_action( 'ct_builder_start', 'pre_filter', -10000);
add_action( 'ct_builder_end', 'post_filter', 10000);

// Script that actually does the stuff
add_action( 'wp_head', 'print_inline_file', 100001 );

function pre_filter() {
	ob_start();
}

function post_filter() {
	$content = ob_get_clean();
	echo filter_output_html($content);
}

function filter_output_html( $content ) {

	require_once(__DIR__  . DIRECTORY_SEPARATOR . 'simple_html_dom.php');
	$html = str_get_html($content);

	if  ($html != false) {
		foreach($html->find('iframe') as $element) {
			if ($element->src && strpos($element->src , 'youtube.com/embed') !== false) {
				$src = $element->src;
				$element->src = "about:blank";
				$element->setAttribute('data-src', $src);
			}
		}

		return $html->save();
	}

	return $content;
}

function print_inline_file() {
	?>
	<script type="text/javascript">
		function loadElements(e) {
			for (var t = 0; t < e.length; t++) {
				if (e[t].getAttribute("src") === "about:blank") {
					e[t].getAttribute("data-src") && (e[t].setAttribute("src", e[t].getAttribute("data-src")), "SOURCE" == e[t].tagName && e[t].parentNode.load())
				}
			}
		}
		
		window.addEventListener("load", function(event) {
			loadElements(document.querySelectorAll("iframe"));
		});
		document.addEventListener("DOMContentLoaded", function() {
			
		});

</script>
<?php

}