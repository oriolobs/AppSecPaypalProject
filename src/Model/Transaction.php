<?php
declare(strict_types=1);

namespace Pw\SlimApp\Model;

final class Transaction
{
    private int $id;
    private string $sender;
    private string $receiver;
    private float $amount;

    public function __construct(
        int $id,
        string $sender,
        string $receiver,
        float $amount
    )
    {
        $this->id = $id;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->amount = $amount;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function sender(): string
    {
        return $this->sender;
    }

    public function receiver(): string
    {
        return $this->receiver;
    }

    public function amount(): float
    {
        return $this->amount;
    }
}