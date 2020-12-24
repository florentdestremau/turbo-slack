<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Room;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
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
     * @Route("/", name="rooms", methods={"GET"})
     */
    public function index(): Response
    {
        $rooms = $this->entityManager->getRepository(Room::class)->findAll();

        return $this->render(
            'room/index.html.twig',
            [
                'rooms' => $rooms,
            ]
        );
    }

    /**
     * @Route("/rooms/{id}", name="rooms_see", methods={"GET"})
     */
    public function see(Room $room)
    {
        $message = new Message($room);

        $form =
            $this->createForm(
                MessageType::class,
                $message,
                ['action' => $this->generateUrl('messages_create', ['id' => $room->getId()])]
            );

        return $this->render('room/see.html.twig', ['room' => $room, 'form' => $form->createView()]);
    }

    /**
     * @Route("/rooms", name="rooms_create", methods={"POST"})
     */
    public function create(Request $request)
    {
        $room = new Room("Room ".bin2hex(random_bytes(5)));
        $this->entityManager->persist($room);
        $this->entityManager->flush();

        return $this->redirectToRoute('rooms_see', ['id' => $room->getId()]);
    }

    /**
     * @Route("/rooms/{id}/messages", name="messages_create", methods={"POST"})
     */
    public function sendMessage(Request $request, Room $room)
    {
        $message = new Message($room);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return $this->redirectToRoute('rooms_see', ['id' => $room->getId()]);
        }

        return $this->render('room/see.html.twig', ['room' => $room, 'form' => $form->createView()]);
    }
}
