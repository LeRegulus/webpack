<?php

namespace App\Controller;

use App\Entity\Subscriber;
use App\Form\SubscriberType;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\AST\Subselect;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{

    public function __construct(SubscriberRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {   
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $cover = $form->get('photo')->getData();
            if($cover){
                $coverFilename = md5(uniqid()).'.'.$cover->guessExtension();
                $cover->move(
                        $this->getParameter('photo_directory'),
                        $coverFilename
                );
                $subscriber->setPhoto($coverFilename);
            }
            $this->em->persist($subscriber);
            $this->em->flush();
            $this->addFlash(type:'success', message:'Subscriber ajouté avec succés!');
            return $this->redirectToRoute('app_subcriber');
        }
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/subscriber', name: 'app_subcriber')]
    public function subscriber(){

        return $this->render('home/subscriber.html.twig');
    }
}
