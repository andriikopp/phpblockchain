<?php
require_once(__DIR__.'/../blockchain.php');

$testchain = new BlockChain("blockchain");

echo $testchain->pushData(json_encode(["balance" => 500]));
echo $testchain->pushData(json_encode(["balance" => 100]));
echo $testchain->pushData(json_encode(["balance" => 1000]));

echo $testchain->getAllBlocksData();
