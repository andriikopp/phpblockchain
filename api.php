<?php
require_once("blockchain/blockchain.php");
require_once("peers.php");

$_STORE_PATH = "store/";
$_TEMP_PATH = "tmp/";

if (isset($_GET["action"])) {
    if (isset($_GET["hash"])) {
        if ($_GET["action"] == "sync" && isset($_GET["raw"])) {
            file_put_contents($_TEMP_PATH . $_GET["hash"], $_GET["raw"], LOCK_EX);
            $blockchain = new BlockChain($_TEMP_PATH . $_GET["hash"]);
            $isValid = $blockchain->validateBlockchain();

            if (json_decode($isValid)->status == "success") {
                file_put_contents($_STORE_PATH . $_GET["hash"], $_GET["raw"], LOCK_EX);
            }

            unlink($_TEMP_PATH . $_GET["hash"]);
            echo $isValid;
        } else if ($_GET["action"] == "push" && isset($_GET["data"])) {
            $blockchain = new BlockChain($_STORE_PATH . $_GET["hash"]);
            echo $blockchain->pushData($_GET["data"]);

            $raw = file_get_contents($_STORE_PATH . $_GET["hash"]);

            for ($i = 0; $i < count($_PEERS); $i++) {
                file_get_contents($_PEERS[$i]
                    . "api.php?action=sync&hash=" . $_GET["hash"]
                    . "&raw=" . urlencode($raw)
                );
            }
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
