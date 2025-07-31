<?php

namespace App\Services;

use App\Config;

class Calculator
{
    public function __construct(public string|float|int $number, public int $decimal = Config::CALCULATOR_DECIMAL_POINT)
    {
        $this->number = $this->format($this->number);
    }

    public function setDecimal(int $decimal): void
    {
        $this->decimal = $decimal;
    }

    public function plus(float|int|string $num): self
    {
        $this->number = bcadd($this->number, $this->format($num), $this->decimal);

        return $this;
    }

    public function minus(float|int|string $num): self
    {
        $this->number = bcsub($this->number, $this->format($num), $this->decimal);

        return $this;
    }

    public function times(float|int|string $num): self
    {
        $this->number = bcmul($this->number, $this->format($num), $this->decimal);

        return $this;
    }

    public function divide(float|int|string $num): self
    {
        $this->number = bcdiv($this->number, $this->format($num), $this->decimal);

        return $this;
    }

    public function percentage(float|int|string $percentage): self
    {
        return $this->divide(100)->times($percentage);
    }

    public function getDecimal(): int
    {
        return $this->decimal;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getAnswer(): float
    {
        return (float) $this->number;
    }

    public function __toString(): string
    {
        return $this->number;
    }

    public function __toInt(): int
    {
        return (int) $this->number;
    }

    public function __toNumber(): float
    {
        return (float) $this->number;
    }

    public function __invoke(...$values): float
    {
        return $this->getAnswer();
    }

    private function format(float|int|string $num): string
    {
        return sprintf('%.'.$this->decimal.'f', (float) $num);
    }
}
