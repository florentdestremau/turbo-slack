<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Room;
use App\Form\MessageType;
use App\Form\RoomType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/rooms")
 */
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
     * @Route("", name="rooms", methods={"GET"})
     */
    public function index(): Response
    {
        $rooms = $this->entityManager->getRepository(Room::class)->findAll();
        $form = $this->createForm(RoomType::class, null, ['action' => $this->generateUrl('rooms_create')]);

        return $this->render(
            'room/index.html.twig',
            [
                'rooms' => $rooms,
                'form'  => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="rooms_see", methods={"GET"})
     */
    public function see(Room $room)
    {
        $message = new Message($room);
        $form =
            $this->createForm(
                MessageType::class,
                $message,
                [
                    'action' => $this->generateUrl(
                        'messages_create',
                        ['id' => $room->getId()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ]
            );


        return $this->render('room/see.html.twig', ['room' => $room, 'form' => $form->createView()]);
    }

    /**
     * @Route("", name="rooms_create", methods={"POST"})
     */
    public function create(Request $request)
    {
        $room = new Room('Undefined');
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($room);
            $this->entityManager->flush();

            return $this->redirectToRoute('rooms_see', ['id' => $room->getId()]);
        }

        return $this->render('room/_form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, requirements={"id": "\d+"}, name="room_delete")
     */
    public function delete(Room $room)
    {
        $this->entityManager->remove($room);
        $this->entityManager->flush();

        return $this->redirectToRoute('rooms');
    }

    /**
     * @Route("/{id}/messages", name="messages_create", methods={"GET", "POST"})
     */
    public function sendMessage(Request $request, Room $room)
    {
        $message = new Message($room);

        $form =
            $this->createForm(
                MessageType::class,
                $message,
                [
                    'action' => $this->generateUrl(
                        'messages_create',
                        ['id' => $room->getId()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ]
            );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->persist($message);
                $this->entityManager->flush();

                return $this->redirectToRoute('rooms_see', ['id' => $room->getId()]);
            }

            return new Response(
                $this->renderView('message/_form.html.twig', ['form' => $form->createView(), 'room' => $room]),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->render(
            'message/_form.html.twig',
            ['form' => $form->createView(), 'room' => $room]
        );
    }
}
