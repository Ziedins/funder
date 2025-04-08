<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $name = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $symbol = null;

    /**
     * @var Collection<int, ExchangeRate>
     */
    #[ORM\OneToMany(targetEntity: ExchangeRate::class, mappedBy: 'baseCurrency', orphanRemoval: true)]
    private Collection $exchangeRates;

    /**
     * @var Collection<int, ExchangeRate>
     */
    #[ORM\OneToMany(targetEntity: ExchangeRate::class, mappedBy: 'targetCurrency', orphanRemoval: true)]
    private Collection $sourceExchangeRates;

    public function __construct()
    {
        $this->exchangeRates = new ArrayCollection();
        $this->sourceExchangeRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(?string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * @return Collection<int, ExchangeRate>
     */
    public function getExchangeRates(): Collection
    {
        return $this->exchangeRates;
    }

    public function addExchangeRate(ExchangeRate $exchangeRate): static
    {
        if (!$this->exchangeRates->contains($exchangeRate)) {
            $this->exchangeRates->add($exchangeRate);
            $exchangeRate->setBaseCurrency($this);
        }

        return $this;
    }

    public function removeExchangeRate(ExchangeRate $exchangeRate): static
    {
        if ($this->exchangeRates->removeElement($exchangeRate)) {
            // set the owning side to null (unless already changed)
            if ($exchangeRate->getBaseCurrency() === $this) {
                $exchangeRate->setBaseCurrency(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExchangeRate>
     */
    public function getSourceExchangeRates(): Collection
    {
        return $this->sourceExchangeRates;
    }

    public function addSourceExchangeRate(ExchangeRate $sourceExchangeRate): static
    {
        if (!$this->sourceExchangeRates->contains($sourceExchangeRate)) {
            $this->sourceExchangeRates->add($sourceExchangeRate);
            $sourceExchangeRate->setTargetCurrency($this);
        }

        return $this;
    }

    public function removeSourceExchangeRate(ExchangeRate $sourceExchangeRate): static
    {
        if ($this->sourceExchangeRates->removeElement($sourceExchangeRate)) {
            // set the owning side to null (unless already changed)
            if ($sourceExchangeRate->getTargetCurrency() === $this) {
                $sourceExchangeRate->setTargetCurrency(null);
            }
        }

        return $this;
    }
}
