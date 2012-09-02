<?php

define('STATUS_EMPTY_QUERY', 1);
define('STATUS_OK', 0);

header('Content-type: application/json');

if(!isset($_GET['searchQuery']) || empty($_GET['searchQuery'])){
	die(json_encode(array('status' => STATUS_EMPTY_QUERY)));
}

$searchQuery = $_GET['searchQuery'];
$page = (isset($_GET['p']) && intval($_GET['p']) > 0) ? intval($_GET['p']) : 1;

include('func.php');
include('phpQuery.php');

$searchParams = array(
	'query'			=> $searchQuery,
	'limit'			=> 10,
	'location_id'	=> 653240,
	'premium_limit'	=> 2,
	'page'			=> $page
);
if(isset($_GET['maxPrice']) && intval($_GET['maxPrice']) > 0){
	$searchParams['price_max'] = intval($_GET['maxPrice']);
}
if(isset($_GET['minPrice']) && intval($_GET['minPrice']) > 0){
	$searchParams['price_min'] = intval($_GET['minPrice']);
}

$avitoSearchURL = 'http://m.avito.ru/items'.getQueryStringFromParamsArray($searchParams);

$document = phpQuery::newDocumentFileHTML($avitoSearchURL);
$productElements = $document->find('li.arrow.img');

$arSearchResultsData = array();
foreach($productElements as $productElement){
	$productElement = pq($productElement);

	$productTitle = $productElement->find('.thumb img')->attr('alt');
	if($productTitle == 'Нет фото'){
		$productTitle = $productElement->find('.title strong')->text();
	}
	$productImage = $productElement->find('.thumb img')->attr('src');
	$productPrice = $productElement->find('.price')->text();
	$productPrice = preg_replace('#[^0-9]#', '', $productPrice);

	$productID = str_replace('/item/', '', $productElement->find('>a')->attr('href'));

	$arSearchResultsData[] = array(
		'title'		=> trim($productTitle),
		'price'		=> trim($productPrice),
		'image'		=> trim($productImage),
		'id'		=> $productID
		);
}
echo json_encode(array(
	'status'	=> STATUS_OK,
	'result'	=> $arSearchResultsData,
	'origin'	=> $avitoSearchURL
	));