<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $balance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Currency $currency = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'sourceAccount', orphanRemoval: true)]
    private Collection $sentTransactions;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'targetAccount')]
    private Collection $receivedTransactions;

    public function __construct()
    {
        $this->sentTransactions = new ArrayCollection();
        $this->receivedTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getSentTransactions(): Collection
    {
        return $this->sentTransactions;
    }

    public function addSentTransaction(Transaction $sentTransaction): static
    {
        if (!$this->sentTransactions->contains($sentTransaction)) {
            $this->sentTransactions->add($sentTransaction);
            $sentTransaction->setSourceAccount($this);
        }

        return $this;
    }

    public function removeSentTransaction(Transaction $sentTransaction): static
    {
        if ($this->sentTransactions->removeElement($sentTransaction)) {
            // set the owning side to null (unless already changed)
            if ($sentTransaction->getSourceAccount() === $this) {
                $sentTransaction->setSourceAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getReceivedTransactions(): Collection
    {
        return $this->receivedTransactions;
    }

    public function addReceivedTransaction(Transaction $receivedTransaction): static
    {
        if (!$this->receivedTransactions->contains($receivedTransaction)) {
            $this->receivedTransactions->add($receivedTransaction);
            $receivedTransaction->setTargetAccount($this);
        }

        return $this;
    }

    public function removeReceivedTransaction(Transaction $receivedTransaction): static
    {
        if ($this->receivedTransactions->removeElement($receivedTransaction)) {
            // set the owning side to null (unless already changed)
            if ($receivedTransaction->getTargetAccount() === $this) {
                $receivedTransaction->setTargetAccount(null);
            }
        }

        return $this;
    }
}
