<?php

declare(strict_types=1);

/**
 * List of transactions.
 *
 * Each transaction contains:
 * - id (int): Transaction identifier
 * - date (DateTime): Transaction date
 * - amount (float): Transaction amount
 * - description (string): Transaction description
 * - merchant (string): Merchant name
 *
 * @var array<int, array{
 *     id:int,
 *     date:\DateTime,
 *     amount:float,
 *     description:string,
 *     merchant:string
 * }>
 */
$transactions = [
    [
        "id" => 1,
        "date" => new DateTime("2019-01-01"),
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => new DateTime("2020-02-15"),
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant",
    ],
];

/**
 * Calculates the total amount of all transactions.
 *
 * @param array<int, array{
 *     id:int,
 *     date:\DateTime,
 *     amount:float,
 *     description:string,
 *     merchant:string
 * }> $transactions List of transactions.
 *
 * @return float Total sum of transaction amounts.
 */
function calculateTotalAmount(array $transactions): float
{
    $total = 0.0;

    foreach ($transactions as $transaction) {
        $total += $transaction['amount'];
    }

    return $total;
}

/**
 * Finds the first transaction that contains the given substring
 * in its description (case-insensitive).
 *
 * Uses the global $transactions array.
 *
 * @param string $descriptionPart Substring to search for.
 *
 * @return array{
 *     id:int,
 *     date:\DateTime,
 *     amount:float,
 *     description:string,
 *     merchant:string
 * }|null The matched transaction or null if not found.
 */
function findTransactionByDescription(string $descriptionPart): ?array
{
    global $transactions;

    foreach ($transactions as $transaction) {
        if (stripos($transaction['description'], $descriptionPart) !== false) {
            return $transaction;
        }
    }

    return null;
}

$result = findTransactionByDescription('dinner');

if ($result !== null) {
    echo "<pre>";
    print_r($result);
    echo "</pre>";
}

/**
 * Finds a transaction by its ID using array_filter().
 *
 * Uses the global $transactions array.
 *
 * @param int $id Transaction identifier.
 *
 * @return array{
 *     id:int,
 *     date:\DateTime,
 *     amount:float,
 *     description:string,
 *     merchant:string
 * }|null The matched transaction or null if not found.
 */
function findTransactionById(int $id): ?array
{
    global $transactions;

    $filtered = array_filter(
        $transactions,
        fn($transaction) => $transaction['id'] === $id
    );

    if (empty($filtered)) {
        return null;
    }

    return array_values($filtered)[0];
}

if ($result !== null) {
    echo "<h3>Transaction found:</h3>";
    echo "<ul>";
    echo "<li>ID: " . $result['id'] . "</li>";
    echo "<li>Date: " . $result['date']->format('Y-m-d') . "</li>";
    echo "<li>Amount: " . number_format($result['amount'], 2) . "</li>";
    echo "<li>Description: " . htmlspecialchars($result['description']) . "</li>";
    echo "<li>Merchant: " . htmlspecialchars($result['merchant']) . "</li>";
    echo "</ul>";
} else {
    echo "<p>Transaction not found.</p>";
}

/**
 * Calculates the number of days between the given transaction date
 * and the current date.
 *
 * @param string $date Transaction date in a format supported by DateTime (e.g. "Y-m-d").
 *
 * @return int Number of days since the transaction date.
 */
function daysSinceTransaction(string $date): int
{
    $transactionDate = new DateTime($date);
    $currentDate = new DateTime();

    $difference = $currentDate->diff($transactionDate);

    return (int) $difference->format('%a');
}

/**
 * Adds a new transaction to the global $transactions array.
 *
 * If a transaction with the same ID already exists, the new one
 * will not be added.
 *
 * @param int $id Transaction identifier.
 * @param string $date Transaction date (format supported by DateTime).
 * @param float $amount Transaction amount.
 * @param string $description Transaction description.
 * @param string $merchant Merchant name.
 *
 * @return void
 */
function addTransaction(
    int $id,
    string $date,
    float $amount,
    string $description,
    string $merchant
): void {
    global $transactions;

    foreach ($transactions as $transaction) {
        if ($transaction['id'] === $id) {
            return;
        }
    }

    $transactions[] = [
        "id" => $id,
        "date" => new DateTime($date),
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant,
    ];
}

addTransaction(
    3,
    "2024-12-25",
    250.00,
    "Christmas gifts",
    "Amazon"
);

// Sort by date (ascending)
usort($transactions, function ($a, $b) {
    return $a['date'] <=> $b['date'];
});

// Sort by amount (descending)
usort($transactions, function ($a, $b) {
    return $b['amount'] <=> $a['amount'];
});

$result = findTransactionById(2);
$totalAmount = calculateTotalAmount($transactions);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Transactions</title>
</head>

<body>
    <h2>Список транзакций</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Merchant</th>
                <th>Days Ago</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td>
                        <?= $transaction['id'] ?>
                    </td>
                    <td>
                        <?= $transaction['date']->format('Y-m-d') ?>
                    </td>
                    <td>
                        <?= number_format($transaction['amount'], 2) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($transaction['description']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($transaction['merchant']) ?>
                    </td>
                    <td>
                        <?= daysSinceTransaction($transaction['date']->format('Y-m-d')) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2"><strong>Total:</strong></td>
                <td><strong>
                        <?= number_format($totalAmount, 2) ?>
                    </strong></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</body>

</html>