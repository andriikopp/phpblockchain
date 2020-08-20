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
        $this->chain = [];

        if (!file_exists($this->filename)) {
            $this->chain = [$this->createGenesisBlock()];

            $serialized = serialize($this->chain);
            file_put_contents($this->filename, $serialized, LOCK_EX);
        }
    }

    // ------------------------- !API -------------------------
    /**
     * Pushes a new block with the given data onto the chain.
     */
    public function pushData($data)
    {
        $contents = file_get_contents($this->filename);
        $this->chain = unserialize($contents);

        $index = $this->getLastBlock()->index + 1;
        $this->push(new Block($index, strtotime("now"), $data));

        if ($this->isValid()) {
            $serialized = serialize($this->chain);
            file_put_contents($this->filename, $serialized, LOCK_EX);

            return json_encode([
                "status" => "success",
                "message" => "Block pushed successfully"
            ]);
        }

        return json_encode([
            "status" => "error",
            "message" => "Blockchain is compromised"
        ]);
    }

    /**
     * Gets the data of the last block of the chain.
     */
    public function getLastBlockData()
    {
        $contents = file_get_contents($this->filename);
        $this->chain = unserialize($contents);

        $last = $this->getLastBlock()->data;

        return json_encode(["lastBlock" => $last]);
    }

    /**
     * Gets the data of all blocks of the chain.
     */
    public function getAllBlocks()
    {
        $contents = file_get_contents($this->filename);
        $this->chain = unserialize($contents);

        $data = [];

        for ($i = 0; $i < count($this->chain); $i++) {
            array_push($data, $this->chain[$i]->data);
        }

        return json_encode(["allBlocks" => $data]);
    }
    // ------------------------- /API -------------------------

    // ------------------------- !Methods -------------------------
    /**
     * Creates the genesis block.
     */
    private function createGenesisBlock()
    {
        return new Block(0, strtotime("now"), "Genesis Block");
    }

    /**
     * Gets the last block of the chain.
     */
    private function getLastBlock()
    {
        return $this->chain[count($this->chain) - 1];
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
            $previousBlock = $this->chain[$i - 1];

            if ($currentBlock->hash != $currentBlock->calculateHash()) {
                return false;
            }

            if ($currentBlock->previousHash != $previousBlock->hash) {
                return false;
            }
        }

        return true;
    }
    // ------------------------- /Methods -------------------------
}
