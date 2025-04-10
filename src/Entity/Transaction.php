<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sentTransactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $sourceAccount = null;

    #[ORM\ManyToOne(inversedBy: 'receivedTransactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $targetAccount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceAccount(): ?Account
    {
        return $this->sourceAccount;
    }

    public function setSourceAccount(?Account $sourceAccount): static
    {
        $this->sourceAccount = $sourceAccount;

        return $this;
    }

    public function getTargetAccount(): ?Account
    {
        return $this->targetAccount;
    }

    public function setTargetAccount(?Account $targetAccount): static
    {
        $this->targetAccount = $targetAccount;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
