<?php

define('STATUS_EMPTY_QUERY', 1);
define('STATUS_OK', 0);

header('Content-type: text/javascript');

if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])){
	die(json_encode(array('status' => STATUS_EMPTY_QUERY)));
}

$productID = intval($_GET['id']);
include('phpQuery.php');

$avitoProductURL = 'http://m.avito.ru/item/'.$productID;
$document = phpQuery::newDocumentFileHTML($avitoProductURL);

$productTitle = $document->find('h2.m_item_title')->text();
$productDescription = $document->find('.m_item_desc')->text();
$productPrice = $document->find('.m_item_price')->text();
$productPrice = preg_replace('#[^0-9]#', '', $productPrice);

$productData = array(
	'title'			=> trim($productTitle),
	'description'	=> trim($productDescription),
	'price'			=> trim($productPrice)
);
echo json_encode(array(
	'status'	=> STATUS_OK,
	'result'	=> $productData,
	'origin'	=> $avitoProductURL
	));