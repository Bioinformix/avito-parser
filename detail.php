<?php

define('STATUS_EMPTY_QUERY', 1);
define('STATUS_OK', 0);

header('Content-type: application/json');

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

$productOwnerPhoneImageURL = $document->find('.pho')->attr('src');
$productOwnerPhone = '+79212306424';

$productImages = array();
$productImageElements = $document->find('.m_item_img');
foreach($productImageElements as $productImageElement){
	$productImageElement = pq($productImageElement);

	$productImageURL = trim($productImageElement->attr('src'));
	if(!empty($productImageURL)){
		$productImages[] = $productImageURL;
	}
}

$productOwnerNameElement = $document->find('.m_item_offer')->next()->find('li:eq(0)');
$productOwnerNameElement->find('strong')->remove();
$productOwnerName = $productOwnerNameElement->text();

$productFullPageURL = $document->find('.bottom .list_b_a:eq(1)')->attr('href');
//echo $productFullPageURL;

$productData = array(
	'id'			=> $productID,
	'title'			=> trim($productTitle),
	'description'	=> trim(strip_tags($productDescription)),
	'price'			=> trim($productPrice),
	'phone'			=> trim($productOwnerPhone),
	'ownerName'		=> trim($productOwnerName),
	'images'		=> $productImages
);
echo json_encode(array(
	'status'	=> STATUS_OK,
	'result'	=> $productData,
	'origin'	=> $avitoProductURL
	));