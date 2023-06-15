<?php

namespace App\Controller;
use App\Form\JoueurType;
use App\Entity\Game;
use App\Entity\Image;
use App\Entity\Joueur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JoueurController extends AbstractController
{  private $requestStack;
   
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
   
    #[Route('/joueur/{id}', name: 'show_player')]
    public function show($id, Request $request) 
    {    $joueur = $this->getDoctrine() ->getRepository(Joueur::class) ->find($id);
        $publicPath = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/uploads/players/';
            if (!$joueur) { throw $this->createNotFoundException( 'No player found for id '.$id );
         } 
     return $this->render('joueur/show.html.twig', ['publicPath'=>$publicPath, 'joueur' =>$joueur ]);
     }


     #[Route('/joueurs', name: 'find_all')]
     public function findAll(Request $request) 
     {    $entityManager = $this->getDoctrine()->getManager();
          $repo= $entityManager->getRepository(Joueur::class);
          $joueurs = $repo->findAll();
          $publicPath = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/uploads/players/';

      return $this->render('joueur/all.html.twig', [ 'publicPath'=>$publicPath,'joueurs' =>$joueurs ]);
      }
 

     #[Route('/ajouter', name: 'Ajouter')]
        public function ajouter(Request $request)
        { 
            $publicPath ="uploads/players/";
          $joueur = new Joueur();
   
          $selectedGame = $joueur->getGame();

          // Increase the nbrJoueur value of the associated Game entity
          if ($selectedGame) {
              $nbrJoueur = $selectedGame->getNbrJoueur();
              $selectedGame->setNbrJoueur($nbrJoueur + 1);
          }
            $form = $this -> createForm("App\Form\JoueurType",$joueur);
            $form->handleRequest($request);   
            if($form->isSubmitted())
            {
            /*
            * @var UploadedFile $image
            */

      $image = $form->get('Image')->getData();

    $em=$this->getDoctrine()->getManager();
    if($image){
        $imageName = $joueur->getId().'.'. $image->guessExtension();
        $image->move($publicPath,$imageName);
        $joueur->setImage($imageName);
    }
    $em->persist($joueur);
    $em->flush();
return $this->redirectToRoute('find_all');

}

    return $this->render('joueur/ajouter.html.twig',

    ['titre'=>'Add','f'=> $form->createView()]);
}
#[Route('/delete/{id}', name: 'delete_joueur')]
public function delete(Request $request,$id):Response
{

$j=$this->getDoctrine()->getRepository(Joueur::class)->find($id);
if(!$j){
    throw $this->createNotFoundException('Joueur not found'.$id);

}
$entityManager=$this->getDoctrine()->getManager();
$entityManager->remove($j);
$entityManager->flush();

return $this->redirectToRoute('find_all');

}
#[Route('/update/{id}', name: 'update_player', methods: ['GET', 'POST'])]

public function edit(Request $request, $id)
{ $publicPath ="uploads/players/";
    
    $joueur = new Joueur();
$joueur = $this->getDoctrine()
->getRepository(Joueur::class)
->find($id);
if (!$joueur) {
throw $this->createNotFoundException(
'No joueur found for id '.$id
);
}
if ($joueur->getImage()) {
$imagePath = $this->getParameter('kernel.project_dir') . "\\public\\uploads\\players\\" . $joueur->getImage();
$joueur->setImage(
    new File($imagePath));}
$form = $this -> createForm("App\Form\JoueurType",$joueur);
$form->handleRequest($request);   
if($form->isSubmitted())
{
 /*
  * @var UploadedFile $image
  */

  $image = $form->get('Image')->getData();

$em=$this->getDoctrine()->getManager();
if($image){
    $imageName = $joueur->getId().'.'. $image->guessExtension();
    $image->move($publicPath,$imageName);
    $joueur->setImage($imageName);
}
$em->persist($joueur);
$em->flush();
return $this->redirectToRoute('find_all');
}
return $this->render('joueur/ajouter.html.twig',
['titre'=>'Update','f' => $form->createView()] );
}

}
