<?php
require_once("block.php");

/**
 * A simple blockchain class with proof-of-work (mining).
 */
class BlockChain
{
    /**
     * Instantiates a new Blockchain.
     */
    public function __construct()
    {
        $this->difficulty = 4;
        $this->filename = "blockchain.idx";

        if (file_exists($this->filename)) {
            $this->loadBlockchain();
        } else {
            $this->chain = [$this->createGenesisBlock()];
            $this->saveBlockchain();
        }
    }

    // ------------------------- !API -------------------------
    /**
     * Pushes a new block with the given data onto the chain.
     */
    public function pushData($data)
    {
        $this->loadBlockchain();

        $index = $this->getLastBlock()->index + 1;
        $this->push(new Block($index, strtotime("now"), $data));

        $this->saveBlockchain();
    }

    /**
     * Gets the data of the last block of the chain.
     */
    public function getLastBlockData()
    {
        $this->loadBlockchain();

        return $this->getLastBlock()->data;
    }

    /**
     * Loads the blockchain and validates the blockchain's integrity. True if the blockchain is valid, false otherwise.
     */
    public function isBlockchainValid()
    {
        $this->loadBlockchain();

        return $this->isValid();
    }
    // ------------------------- /API -------------------------

    // ------------------------- !Methods -------------------------
    /**
     * Creates the genesis block.
     */
    private function createGenesisBlock()
    {
        return new Block(0, strtotime("2017-01-01"), "Genesis Block");
    }

    /**
     * Gets the last block of the chain.
     */
    private function getLastBlock()
    {
        return $this->chain[count($this->chain)-1];
    }

    /**
     * Pushes a new block onto the chain.
     */
    private function push($block)
    {
        $block->previousHash = $this->getLastBlock()->hash;
        $this->mine($block);
        array_push($this->chain, $block);
    }

    /**
     * Mines a block.
     */
    private function mine($block)
    {
        while (substr($block->hash, 0, $this->difficulty) !== str_repeat("0", $this->difficulty)) {
            $block->nonce++;
            $block->hash = $block->calculateHash();
        }
    }

    /**
     * Validates the blockchain's integrity. True if the blockchain is valid, false otherwise.
     */
    private function isValid()
    {
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i-1];

            if ($currentBlock->hash != $currentBlock->calculateHash()) {
                return false;
            }

            if ($currentBlock->previousHash != $previousBlock->hash) {
                return false;
            }
        }

        return true;
    }

    /**
     * Locks the file, saves the blockchain to the locked file, and releases the file.
     */
    private function saveBlockchain()
    {
        $file = fopen($this->filename, "w");

        if (flock($file, LOCK_EX)) {
            $serialized = serialize($this->chain);
            fwrite($file, $serialized);
            flock($file, LOCK_UN);
        }

        fclose($file);
    }

    /**
     * Locks the file, loads the blockchain from the locked file, and releases the file.
     */
    private function loadBlockchain()
    {
        $file = fopen($this->filename, "r");

        if (flock($file, LOCK_EX)) {
            $contents = fread($file, filesize($this->filename));
            $this->chain = unserialize($contents);
            flock($file, LOCK_UN);
        }

        fclose($file);
    }
    // ------------------------- /Methods -------------------------
}
