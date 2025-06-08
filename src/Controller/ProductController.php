<?php
namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/boutique', name: 'product_index')]
    public function index(EntityManagerInterface $em, CartService $cartService): Response
    {
        $products = $em->getRepository(Product::class)->findAll();
        $cartCount = $cartService->getTotalQuantity();
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'cartCount' => $cartCount,
        ]);
    }

    #[Route('/boutique/add/{id}', name: 'product_add_cart', methods: ['POST'])]
    public function addToCart(Product $product, CartService $cartService, Request $request): Response
    {
        $quantity = $request->query->getInt('qty', 1);
        $cartService->add($product->getId(), $quantity);
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'cartCount' => $cartService->getTotalQuantity()
            ]);
        }
        return $this->redirectToRoute('product_index');
    }
}
