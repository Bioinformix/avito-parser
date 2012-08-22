<?php

define('STATUS_EMPTY_QUERY', 1);
define('STATUS_OK', 0);

if(!isset($_GET['q']) || empty($_GET['q'])){
	die(json_encode(array('status' => STATUS_EMPTY_QUERY)));
}
$searchQuery = $_GET['q'];
include('phpQuery.php');

$location_id = 653240;
$limit = 20;
$premium_limit = 2;
$avitoSearchURL = 'http://m.avito.ru/items?query='.$searchQuery.'&limit='.$limit.'&location_id='.$location_id.'&premium_limit='.$premium_limit;

$document = phpQuery::newDocumentFileHTML($avitoSearchURL, $charset = 'utf-8');
$productElements = $document->find('li.arrow.img');

$arSearchResultsData = array();
foreach($productElements as $productElement){
	$productElement = pq($productElement);

	$productTitle = $productElement->find('.thumb img')->attr('alt');
	$productImage = $productElement->find('.thumb img')->attr('src');
	$productPrice = $productElement->find('.price')->text();
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
	'result'	=> $arSearchResultsData
	));