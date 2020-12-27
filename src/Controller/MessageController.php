<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/messages")
 */
class MessageController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, requirements={"id": "\d+"}, name="message_delete")
     */
    public function delete(Message $message)
    {
        $this->entityManager->remove($message);
        $this->entityManager->flush();

        return $this->redirectToRoute('rooms_see', ['id' => $message->getRoom()->getId()]);
    }
}
