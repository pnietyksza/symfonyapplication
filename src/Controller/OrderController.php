<?php

namespace App\Controller;

use App\Entity\OrderDetail;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Order;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data))
            return new JsonResponse(['error' => 'Error Processing Request'], 404);

        $order = new Order();
        $order->setCreatedAt(new \DateTime());

        $total = 0;
        foreach ($data as $item) {
            $product = $em->getRepository(Product::class)->find($item['id']);

            if (!$product) {
                return new JsonResponse(['error' => 'Product not found'], 404);
            }

            $orderDetail = new OrderDetail();
            $orderDetail->setProduct($product);
            $orderDetail->setQuantity($item['quantity']);
            $orderDetail->setPrice($product->getPrice() * $item['quantity']);
            $orderDetail->setOrder($order);

            $total += $orderDetail->getPrice();

            $order->addOrderDetail($orderDetail);
            $em->persist($orderDetail);
        }
        $order->setTotal($total);
        $order->setUpdatedAt(new \DateTime());
        $order->setStatus(Order::STATUS_NEW);
        $em->persist($order);
        $em->flush();

        $orderJson = (OrderController::orderInformations($order->getId(), $em))->getContent();

        return new JsonResponse(json_decode($orderJson, true), 200);
    }

    #[Route('/order/{id}', name: 'get_order', methods: ['GET'])]
    public function getOrder(int $id, EntityManagerInterface $em): JsonResponse
    {
        $orderJson = (OrderController::orderInformations($id, $em))->getContent();

        return new JsonResponse(json_decode($orderJson, true), 200);
    }

    public static function orderInformations(int $id, EntityManagerInterface $em): JsonResponse
    {
        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($normalizers, $encoders);

        $order = $em->getRepository(Order::class)->find($id);

        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], 404);
        }

        $context = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['customer', 'products'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];

        $orderData = $serializer->serialize($order, 'json', $context);

        return new JsonResponse($orderData, 200, [], true);
    }
}
