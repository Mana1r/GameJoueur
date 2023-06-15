<?php

namespace App\Controller;
use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game;
use App\Entity\Joueur;
use App\Controller\JoueurController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class GameController extends AbstractController
{   private $requestStack;

  
    public function __construct(RequestStack $requestStack)
    {       

        $this->requestStack = $requestStack;
    }
    
    #[Route('/game/{id}', name: 'game_show')]
    public function show($id ,  Request $request)
    { $game = $this->getDoctrine() ->getRepository(Game::class) ->find($id);
        
        $publicPath = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/uploads/games/';

        $em=$this->getDoctrine()->getManager();
        $listJoueur =$em->getRepository(Joueur::class)
            ->findBy(['Game'=>$game]);
        if (!$game) { throw $this->createNotFoundException( 'No Game found for id '.$id );
        }
        return $this->render('game/show.html.twig', ['publicPath' => $publicPath  , 
            'game' => $game,
            'joueur' =>$listJoueur,
                 ]);
    }

    #[Route('/games', name: 'find_allGames')]
    public function findAll(Request $request) 
    {    $entityManager = $this->getDoctrine()->getManager();
         $repo= $entityManager->getRepository(Game::class);
         $games = $repo->findAll();
         $publicPath = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath().'/uploads/games/';

     return $this->render('game/all.html.twig', [ 'publicPath'=>$publicPath,'games' =>$games ]);
     }



     #[Route('/ajouterGame', name: 'AjouterGame')]
     public function ajouter(Request $request)
     { $publicPath ="uploads/games/";
       $game = new Game();


         $form = $this -> createForm("App\Form\GameType",$game);
         $form->handleRequest($request);   
         if($form->isSubmitted())
         {
         /*
         * @var UploadedFile $image
         */

   $image = $form->get('Image')->getData();

 $em=$this->getDoctrine()->getManager();
 if($image){
     $imageName = $game->getId().'.'. $image->guessExtension();
     $image->move($publicPath,$imageName);
     $game->setImage($imageName);
 }
 $em->persist($game);
 $em->flush();
return $this->redirectToRoute('find_allGames');

}

 return $this->render('game/ajouter.html.twig',

 ['titre'=>'Add','f'=> $form->createView()]);
}

#[Route('/delete/{id}', name: 'delete_game')]
public function delete(Request $request,$id):Response
{

$g=$this->getDoctrine()->getRepository(Game::class)->find($id);
if(!$g){
    throw $this->createNotFoundException('Game not found'.$id);

}
$entityManager=$this->getDoctrine()->getManager();
$listJoueur =$entityManager->getRepository(Joueur::class)->findBy(['Game'=>$g]);
foreach ($listJoueur as $joueur) {
$entityManager->remove($joueur);
    }
$entityManager->remove($g);
$entityManager->flush();

return $this->redirectToRoute('find_allGames');

}
#[Route('/updateGame/{id}', name: 'update_game', methods: ['GET', 'POST'])]
public function edit(Request $request, $id)
{ 
    $publicPath ="uploads/games/";
    $game = new Game();
   $game = $this->getDoctrine()->getRepository(Game::class)->find($id);
if (!$game) {
throw $this->createNotFoundException(
'No Game found for id '.$id
);
}   
if ($game->getImage()){
$imagePath = $this->getParameter('kernel.project_dir') . "\\public\\uploads\\games\\" . $game->getImage();

dump( '****************************************************************', $game->getImage(), $imagePath);
$game->setImage(
    new File($imagePath));}
$form = $this ->createForm("App\Form\GameType",$game);
$i = $form->get('Image');
  dump($i, 'image');
$form->handleRequest($request);   
if($form->isSubmitted())
 {
 /*
  * @var UploadedFile $image
  */

  $image = $form->get('Image')->getData();
  
$em=$this->getDoctrine()->getManager();
if($image){
    $imageName = $game->getId().'.'. $image->guessExtension();
    $image->move($publicPath,$imageName);
    $game->setImage($imageName);
}
$em->persist($game);
$em->flush();
return $this->redirectToRoute('find_allGames');
}
return $this->render('game/ajouter.html.twig',
['titre'=>'Update','f' => $form->createView()] );
}
}
 
    
    