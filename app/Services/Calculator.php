<?php

namespace App\Services;

use App\Config;

class Calculator
{
    public function __construct(public float $number, public int $decimal = Config::DECIMAL_POINT) {}

    public function setDecimal(int $decimal): void
    {
        $this->decimal = $decimal;
    }

    public function plus(float|int $num): self
    {
        $this->number = bcadd($this->number, $num, $this->decimal);

        return $this;
    }

    public function minus(float|int $num): self
    {
        $this->number = bcsub($this->number, $num, $this->decimal);

        return $this;
    }

    public function times(float|int $num): self
    {
        $this->number = bcmul($this->number, $num, $this->decimal);

        return $this;
    }

    public function divide(float|int $num): self
    {
        $this->number = bcdiv($this->number, $num, $this->decimal);

        return $this;
    }

    public function percentage(float|int $percentage): self
    {
        $this->divide(100)->times($percentage);

        return $this;
    }

    public function getDecimal(): int
    {
        return $this->decimal;
    }

    public function getNumber(): float|int
    {
        return $this->number;
    }

    public function getAnswer(): float|int
    {
        return $this->getNumber();
    }

    public function __toString(): string
    {
        return $this->getAnswer();
    }

    public function __toInt(): int
    {
        return $this->getAnswer();
    }

    public function __toNumber(): float
    {
        return $this->getAnswer();
    }

    public function __invoke(...$values): int|float
    {
        return $this->getAnswer();
    }
}
