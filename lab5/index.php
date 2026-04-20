<?php

declare(strict_types=1);

/**
 * Интерфейс для хранилища транзакций
 */
interface TransactionStorageInterface
{
    public function addTransaction(Transaction $transaction): void;
    public function removeTransactionById(int $id): void;
    public function getAllTransactions(): array;
    public function findById(int $id): ?Transaction;
}

/**
 * Класс одной транзакции
 */
class Transaction
{
    private int $id;
    private DateTime $date;
    private float $amount;
    private string $description;
    private string $merchant;

    public function __construct(
        int $id,
        string $date,
        float $amount,
        string $description,
        string $merchant
    ) {
        $this->id = $id;
        $this->date = new DateTime($date);
        $this->amount = $amount;
        $this->description = $description;
        $this->merchant = $merchant;
    }

    public function getId(): int { return $this->id; }
    public function getDate(): DateTime { return $this->date; }
    public function getAmount(): float { return $this->amount; }
    public function getDescription(): string { return $this->description; }
    public function getMerchant(): string { return $this->merchant; }

    /**
     * Возвращает количество дней с момента транзакции
     */
    public function getDaysSinceTransaction(): int
    {
        $now = new DateTime();
        return $this->date->diff($now)->days;
    }
}

/**
 * Репозиторий транзакций
 */
class TransactionRepository implements TransactionStorageInterface
{
    private array $transactions = [];

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    public function removeTransactionById(int $id): void
    {
        foreach ($this->transactions as $key => $transaction) {
            if ($transaction->getId() === $id) {
                unset($this->transactions[$key]);
            }
        }
    }

    public function getAllTransactions(): array
    {
        return array_values($this->transactions);
    }

    public function findById(int $id): ?Transaction
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }
        return null;
    }
}

/**
 * Бизнес-логика
 */
class TransactionManager
{
    public function __construct(
        private TransactionStorageInterface $repository
    ) {}

    public function calculateTotalAmount(): float
    {
        return array_reduce(
            $this->repository->getAllTransactions(),
            fn($sum, $t) => $sum + $t->getAmount(),
            0
        );
    }

    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        return array_reduce(
            $this->repository->getAllTransactions(),
            function ($sum, $t) use ($start, $end) {
                if ($t->getDate() >= $start && $t->getDate() <= $end) {
                    return $sum + $t->getAmount();
                }
                return $sum;
            },
            0
        );
    }

    public function countTransactionsByMerchant(string $merchant): int
    {
        return count(array_filter(
            $this->repository->getAllTransactions(),
            fn($t) => $t->getMerchant() === $merchant
        ));
    }

    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, fn($a, $b) =>
            $a->getDate() <=> $b->getDate()
        );

        return $transactions;
    }

    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort($transactions, fn($a, $b) =>
            $b->getAmount() <=> $a->getAmount()
        );

        return $transactions;
    }
}

/**
 * Рендер HTML таблицы
 */
final class TransactionTableRenderer
{
    public function render(array $transactions): string
    {
        $html = "<table border='1' cellpadding='5'>";
        $html .= "<tr>
            <th>ID</th>
            <th>Дата</th>
            <th>Сумма</th>
            <th>Описание</th>
            <th>Получатель</th>
            <th>Категория</th>
            <th>Дней назад</th>
        </tr>";

        foreach ($transactions as $t) {
            $html .= "<tr>
                <td>{$t->getId()}</td>
                <td>{$t->getDate()->format('Y-m-d')}</td>
                <td>{$t->getAmount()}</td>
                <td>{$t->getDescription()}</td>
                <td>{$t->getMerchant()}</td>
                <td>" . $this->getCategory($t->getMerchant()) . "</td>
                <td>{$t->getDaysSinceTransaction()}</td>
            </tr>";
        }

        $html .= "</table>";

        return $html;
    }

    private function getCategory(string $merchant): string
    {
        return match ($merchant) {
            'Amazon', 'eBay' => 'Покупки',
            'McDonalds', 'KFC' => 'Еда',
            'Netflix' => 'Подписки',
            default => 'Другое'
        };
    }
}

/*
   *Тестовые данные
*/

$repo = new TransactionRepository();

$transactions = [
    new Transaction(1, '2026-04-01', 120.5, 'Покупка книги', 'Amazon'),
    new Transaction(2, '2026-04-02', 15.2, 'Обед', 'McDonalds'),
    new Transaction(3, '2026-04-03', 9.99, 'Подписка', 'Netflix'),
    new Transaction(4, '2026-04-04', 220, 'Одежда', 'eBay'),
    new Transaction(5, '2026-04-05', 18.7, 'Ужин', 'KFC'),
    new Transaction(6, '2026-04-06', 50, 'Игры', 'Steam'),
    new Transaction(7, '2026-04-07', 300, 'Техника', 'Amazon'),
    new Transaction(8, '2026-04-08', 12, 'Кофе', 'Starbucks'),
    new Transaction(9, '2026-04-09', 75, 'Продукты', 'LocalStore'),
    new Transaction(10, '2026-04-10', 40, 'Такси', 'Uber'),
];

foreach ($transactions as $t) {
    $repo->addTransaction($t);
}

$manager = new TransactionManager($repo);
$renderer = new TransactionTableRenderer();

/*
 *Вывод
 */

echo "<h2>Все транзакции</h2>";
echo $renderer->render($repo->getAllTransactions());

echo "<h3>Общая сумма: " . $manager->calculateTotalAmount() . "</h3>";

echo "<h3>Сортировка по сумме (убывание)</h3>";
echo $renderer->render($manager->sortTransactionsByAmountDesc());