<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Symfony\Component\Serializer\Annotation\Ignore;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Cart::class, cascade={"persist", "remove"})
     * @Ignore()
     */
    private $cart;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="ordr")
     */
    private $products;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalPrice;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $creationDate;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProducts(Product $products): self
    {
        if (!$this->products->contains($products)) {
            $this->products[] = $products;
            $products->setOrdr($this);
        }

        return $this;
    }

    public function removeProducts(Product $products): self
    {
        if ($this->products->removeElement($products)) {
            // set the owning side to null (unless already changed)
            if ($products->getOrdr() === $this) {
                $products->setOrdr(null);
            }
        }

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }
}
