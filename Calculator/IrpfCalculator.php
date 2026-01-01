<?php

namespace KimaiPlugin\IrpfBundle\Calculator;

use App\Invoice\CalculatorInterface;
use App\Invoice\InvoiceModel;
use App\Invoice\TaxRow;
use App\Entity\Tax;
use App\Entity\TaxType;

class IrpfCalculator implements CalculatorInterface
{
    public function __construct(
        private CalculatorInterface $inner,
        private bool $applied,
        private float $rate
    ) {   
    }

    public function getEntries(): array
    {
        return $this->inner->getEntries();
    }

    public function setModel(InvoiceModel $model): void
    {
        $this->inner->setModel($model);
    }

    public function getSubtotal(): float
    {
        return $this->inner->getSubtotal();
    }

    public function getTax(): float
    {
        return $this->inner->getTax();
    }

    public function getTotal(): float
    {
        $total = $this->inner->getTotal();
        if ($this->applied) {
            $irpf = $this->getSubtotal() * $this->rate;
            $total -= $irpf;
        }
        return round($total, 2);
    }

    public function getVat(): float
    {
        return $this->inner->getVat();
    }

    public function getTaxRows(): array
    {
        $rows = $this->inner->getTaxRows();
        if ($this->applied) {
            $tax = new Tax(TaxType::STANDARD, 'IRPF', -$this->rate * 100);
            $rows[] = new TaxRow($tax, $this->getSubtotal());
        }
        return $rows;
    }

    public function getTimeWorked(): int
    {
        return $this->inner->getTimeWorked();
    }

    public function getId(): string
    {
        return $this->inner->getId();
    }
}
