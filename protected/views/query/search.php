<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div id="searchBox"></div>
<div id="searchResult"></div>
<script>
    (function($){
	
	$(function(){
	   $('#searchBox').loopSearchBox({
	       prefix: 'search',
	       targetElem: $('#searchResult')
	   });
	});
	
    })(jQuery);
</script>