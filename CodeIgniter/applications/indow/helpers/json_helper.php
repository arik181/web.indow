<?
function markFieldsAsNumeric($objects,$fields)
{
	foreach ($objects as $object) 
	{
		foreach ($object as $key => $value) {
			if(in_array($key, $fields))
			{
				$object->$key = (float) $value;
			}
		}
	}
}

?>