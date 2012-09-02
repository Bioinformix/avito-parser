<?php

function getQueryStringFromParamsArray($params){
	if(!is_array($params)){
		return false;
	}

	$queryString = array();
	foreach($params as $key => $value){
		$queryString[] = $key.'='.urlencode($value);
	}
	return empty($queryString) 
		? '' 
		: '?'.implode('&', $queryString);
}