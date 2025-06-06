<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;
    private const CART_KEY = 'cart';

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->session->get(self::CART_KEY, []);
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        $this->session->set(self::CART_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->session->get(self::CART_KEY, []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->session->set(self::CART_KEY, $cart);
        }
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $cart = $this->session->get(self::CART_KEY, []);
        if ($quantity > 0) {
            $cart[$productId] = $quantity;
        } else {
            unset($cart[$productId]);
        }
        $this->session->set(self::CART_KEY, $cart);
    }

    public function getTotalQuantity(): int
    {
        $cart = $this->session->get(self::CART_KEY, []);
        return array_sum($cart);
    }

    public function getCart(): array
    {
        return $this->session->get(self::CART_KEY, []);
    }
}
