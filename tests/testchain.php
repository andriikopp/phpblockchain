<?php
require_once(__DIR__.'/../blockchain.php');

$testchain = new BlockChain();

$testchain->pushData(json_encode(["balance" => 500]));

echo json_encode($testchain, JSON_PRETTY_PRINT);
