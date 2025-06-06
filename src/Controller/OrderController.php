<?php
namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order_index')]
    public function index(CartService $cartService, EntityManagerInterface $em): Response
    {
        $cart = $cartService->getCart();
        $products = [];
        $total = 0;
        foreach ($cart as $productId => $qty) {
            $product = $em->getRepository(Product::class)->find($productId);
            if ($product) {
                $products[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'subtotal' => $product->getPrice() * $qty
                ];
                $total += $product->getPrice() * $qty;
            }
        }
        return $this->render('order/index.html.twig', [
            'products' => $products,
            'total' => $total,
        ]);
    }

    #[Route('/commande/update/{id}', name: 'order_update_quantity', methods: ['POST'])]
    public function updateQuantity(int $id, Request $request, CartService $cartService): Response
    {
        $quantity = $request->request->getInt('quantity', 1);
        $cartService->updateQuantity($id, $quantity);
        return $this->redirectToRoute('order_index');
    }

    #[Route('/commande/remove/{id}', name: 'order_remove_item', methods: ['POST'])]
    public function removeItem(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        return $this->redirectToRoute('order_index');
    }
}
