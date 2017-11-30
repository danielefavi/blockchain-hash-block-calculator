<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

// list of the transactions
$transactions = [
    [
        'sender' => 'john',
        'receiver' => 'jenny',
        'amount' => '100'
    ],
    [
        'sender' => 'yang',
        'receiver' => 'jin',
        'amount' => '200'
    ],
    [
        'sender' => 'giacomo',
        'receiver' => 'alehandro',
        'amount' => '300'
    ],
    [
        'sender' => 'satoshi',
        'receiver' => 'piripoppi',
        'amount' => '30'
    ],
];



createAndDisplayBlockchain($transactions);



/**
 * Take the list of transactions in input, calculate the hashes for each block
 * that starts with 000 and display all the blocks.
 *
 * @param array $transactions
 * @return void
 */
function createAndDisplayBlockchain($transactions)
{
    $prev_block_hash = '0';

    // iterating to all the transactions in the array
    foreach($transactions as $transaction) {
        $result = hashCalculator($transaction, $prev_block_hash);

        dd(' /* ************************************* */ ', false);
        dd($result, false);

        // setting the last hash found: this will be sent to the next block in the
        // next iteration
        if ($result) {
            $prev_block_hash = $result['hash'];
        }
    }
}



/**
 * It calculates the hash of the block that starts with 000; it is done by
 * inclementing the NONCE.
 *
 * @param array $transaction
 * @param string|null $prev_block_hash
 * @return array
 */
function hashCalculator($transaction, $prev_block_hash=null)
{
    // better to limit the cicles to avoid that the application freeze!
    $cycles_limit = 10000000;

    for($nonce=1; $nonce<$cycles_limit; $nonce++) {
        $block = getBlockContent($transaction, $nonce, $prev_block_hash);

        // calculating the hash SHA-256 of the text block
        $hash = hash('sha256', $block);

        // checking if the block starts with three zeros
        if (substr($hash, 0, 4) === '0000') {
            return [
                'block_content' => $block,
                'hash' => $hash,
            ];
        }
    }

    throw new Exception('HASH not found! Try to increase the cycles limit ;)');
}



/**
 * Assemble the content of the block from the transazion array parameter.
 * For instance, it returns a text like this:
 *      PREV:5feceb66ffc86f38d952786c6d696c79c2dbc239dd4e91b46729d73a27fb57e9
 *      SENDER:usr1
 *      RECEIVER:usr2
 *      AMOUNT:100
 *      NONCE:10924
 *
 * @param array $transaction
 * @param numeric $nonce
 * @param string|null $prev_block_hash
 * @return string
 */
function getBlockContent($transaction, $nonce, $prev_block_hash=null)
{
    // hash of 0 when the previous bloc hash is not set
    // if (!$prev_block_hash) $prev_block_hash = hash('sha256', 0);

    $text = "PREV:" . $prev_block_hash; // previous block hash
    $text .= "\nSENDER:" . $transaction['sender']; // sender name
    $text .= "\nRECEIVER:" . $transaction['receiver']; // receiver name
    $text .= "\nAMOUNT:" . $transaction['amount']; // amount
    $text .= "\nNONCE:" . $nonce; // nonce number

    return $text;
}



/**
 * This is just a die and dump function for testing.
 *
 * @param mixed $val
 * @param boolean|null $stop
 * @return void
 */
function dd($val, $stop=true)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';

    if ($stop) die();
}
