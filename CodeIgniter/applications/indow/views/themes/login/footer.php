</div>
<?php

    /**
     * Used for module specific JS data )
     */

    if(isset($this->data['phpToJavaScript']))
    {
        if(!is_array($this->data['phpToJavaScript'])){
            echo $this->data['phpToJavaScript'];
        }
    }

?>
<? /*
<!-- js in footer -->
	<script src="/js/plugins.js"></script>
	<script src="/js/main.js"></script>
	<script src="/js/vendor/autoNumeric.min.js"></script>
	<script src="/js/bootstrap.min.js"></script>
	<?php if ( isset($js) ) {
	if ( is_array($js) )
		foreach ($js as $j) echo '<script src="/js/'.$js.'.js"></script>';
	else
		echo '<script src="/js/'.$js.'.js"></script>';
	} ?>
*/ ?>
</body>
</html>
