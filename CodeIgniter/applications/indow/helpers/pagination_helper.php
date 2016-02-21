<?php
function monkeyShit(){
    echo "Monkey Shit";
}

function sanitizeURIforPagination($sort_column,$sort_direction,$page,$default_sort,$default_direction,$default_page)
{
	if(is_numeric($sort_column))
	{
		$page = $sort_column;
		$sort_column = $default_sort;
	}
	if(is_numeric($sort_direction))
	{
		$page = $sort_direction;
		$sort_direction = $default_direction;
	}
	return array($sort_column,$sort_direction,$page);
}

function tableHeaders($controller = null, $page = 1, $sort = null, $direction = null, $columns = array())
{
	foreach ($columns as $column=>$attributes) {
		if($attributes['sortable'] &&  $attributes['key']==$sort)
			echo "<th class=\"$sort $direction\">";
		else
			echo "<th>";
		if($attributes['sortable'] && $attributes['key']==$sort)
		{
			if($direction)
			{
				if($direction == "asc")
					$dir = "desc";
				else
					$dir = "asc";
			}
			else // nothing set, so we're on our default sort and direction, flip to ascending
			{
				$dir = "asc";
			}
		}
		elseif(isset($attributes['default_direction']) && $attributes['default_direction']=='desc')
			$dir = "desc";
		else
			$dir = 'asc';
		$glyph = "glyphicon glyphicon-chevron-up";
		if($dir == "asc")
			$glyph = "glyphicon glyphicon-chevron-down";

		if($attributes['sortable'] && $attributes['key']==$sort)
			echo "<a href=\"/$controller/{$attributes['key']}/$dir/$page\">";
		elseif($attributes['sortable'])
			echo "<a href=\"/$controller/{$attributes['key']}/$dir/1\">";
		echo $column;

		if($attributes['sortable'])
			echo "</a>";
		if($attributes['sortable'] && $attributes['key']==$sort)
			echo " <span class=\"$glyph\"></span>";
		echo "</th>";
	}
}

?>