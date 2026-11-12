<?php

declare(strict_types=1);

namespace App\Review\Controller;

use App\Review\Entity\Review;
use App\Review\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ReviewController extends AbstractController
{
    #[Route('/reviews', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        $data = $request->toArray();

        $review = new Review();
        $review->setRentalReference(\is_string($data['rentalReference'] ?? null) ? $data['rentalReference'] : '');
        $review->setRating(\is_int($data['rating'] ?? null) ? $data['rating'] : 0);
        $review->setComment(\is_string($data['comment'] ?? null) ? $data['comment'] : null);

        $violations = $validator->validate($review);
        if (\count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = (string) $violation->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($review);
        $em->flush();

        return $this->json(['id' => $review->getId()], Response::HTTP_CREATED);
    }

    #[Route('/reviews', methods: ['GET'])]
    public function list(Request $request, ReviewRepository $reviews): JsonResponse
    {
        return $this->json(array_map(
            static fn(Review $review): array => [
                'rating' => $review->getRating(),
                'comment' => $review->getComment(),
            ],
            $reviews->findForRental($request->query->getString('rental')),
        ));
    }
}
