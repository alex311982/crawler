<?php

function include_component($component_name, $action = '', $data = array())
{
	$object = ComponentHelper::factory($component_name);
	$object->execute($data);
}