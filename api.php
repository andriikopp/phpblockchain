<?php
require_once("blockchain/blockchain.php");

$_STORE_PATH = "store/";

if (isset($_GET["action"])) {
    if (isset($_GET["hash"])) {
        if ($_GET["action"] == "push" && isset($_GET["data"])) {
            $blockchain = new BlockChain($_STORE_PATH . $_GET["hash"]);
            echo $blockchain->pushData($_GET["data"]);
        } else if ($_GET["action"] == "last") {
            $blockchain = new BlockChain($_STORE_PATH . $_GET["hash"]);
            echo $blockchain->getLastBlockData();
        } else if ($_GET["action"] == "all") {
            $blockchain = new BlockChain($_STORE_PATH . $_GET["hash"]);
            echo $blockchain->getAllBlocksData();
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid action"
            ]);
        }
    } else if ($_GET["action"] == "register") {
        $name = md5(uniqid(rand(), 1));
        $blockchain = new BlockChain($_STORE_PATH . $name);
        echo $name;
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Hash identifier is missing"
        ]);
    }
}
?>
