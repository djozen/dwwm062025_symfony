<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private RequestStack $requestStack;
    private const CART_KEY = 'cart';


    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->requestStack->getSession()->get(self::CART_KEY, []);
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        $this->requestStack->getSession()->set(self::CART_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->requestStack->getSession()->get(self::CART_KEY, []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
           $this->requestStack->getSession()->set(self::CART_KEY, $cart);
        }
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $cart = $this->requestStack->getSession()->get(self::CART_KEY, []);
        if ($quantity > 0) {
            $cart[$productId] = $quantity;
        } else {
            unset($cart[$productId]);
        }
        $this->requestStack->getSession()->set(self::CART_KEY, $cart);
    }

    public function getTotalQuantity(): int
    {
        $cart = $this->requestStack->getSession()->get(self::CART_KEY, []);
        return array_sum($cart);
    }

    public function getCart(): array
    {
        return $this->requestStack->getSession()->get(self::CART_KEY, []);
    }
}
