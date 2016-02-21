$(document).ready(function(){
	$('.delete').click(function(){
		return confirm("Are you sure you want to delete this item? This action cannot be undone.");
	});
});
$(document).ready(function(){
	$('.deletes').click(function(){
		return confirm("Are you sure you want to delete these items? This action cannot be undone.");
	});
});