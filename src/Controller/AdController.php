<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Repository\getRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {	/*  code pour appeler la requete du modele doctrine comme un select* avec un find all, on peut voir la liste dans le repository  */
    	/* $repo=$this->getDoctrine()->getRepository(Ad::class); */
    	$ads = $repo->findAll();
    	/* code pour afficher le tableau dans la barre du bas comme un printr */
    	dump($ads);
    	/* le controller envoit ensuite la variable $ads à la vue twig qu'on appellera {{ads}} */
    	return $this->render('ad/index.html.twig', [
    		'ads'=> $ads
    	]);
    }


/**
     * @Route("/ads/new", name="ads_create")
     *@IsGranted("ROLE_USER")
     */
public function create(Request $request, ObjectManager $manager)
{	

  $ad=new Ad();


  $form=$this->createForm(AnnonceType::class, $ad);

  $form->handleRequest($request);

  if ($form->isSubmitted() && $form->isValid()){
    $ad->setAuthor($this->getUser());
    
    if(!$ad->getSlug()) {
      $slugify=new Slugify();

      $ad->setSlug($slugify->slugify($ad->getTitle()));
    }
    

    foreach ($ad->getImages() as $image) {

      $image->setAd($ad);
      $manager->persist($image);


    }

    $manager->persist($ad);
    $manager->flush();

    $this->addFlash('success', "L'annonce {$ad->getTitle()} a été correctement enregistrée!"); 
// { } en php pour donner la priorité dexécution

    return $this->redirectToRoute('ads_show', ['slug'=> $ad->getSlug()]);

  }

  dump($ad);
  return $this->render('ad/new.html.twig', [
   'form'=> $form->createView()
 ]);
}




/**
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Cette annonce ne vous appartient pas.")
     */
public function edit(Request $request, ObjectManager $manager, Ad $ad)
{ 



  $form=$this->createForm(AnnonceType::class, $ad);

  //dump ($request);
  //dump ($request->request->get('annonce'));
  //dump ($request->request->get('annonce')['slug']);

  // si le slug est vide on le remplace par le titre formaté

  

  $slugify=new Slugify();
  $data=$request->request->get('annonce');
  $data['slug']=$slugify->slugify($request->request->get('annonce')['title']);
  $request->request->set('annonce',$data);
  

  // fin resolution pb du bug du champ slug vide

  $form->handleRequest($request);

  if ($form->isSubmitted() && $form->isValid()){


   if(!$ad->getSlug()) {
    $slugify=new Slugify();

    $ad->setSlug($slugify->slugify($ad->getTitle()));
  }


  foreach ($ad->getImages() as $image) {

    $image->setAd($ad);
    $manager->persist($image);


  }

  $manager->persist($ad);
  $manager->flush();

  $this->addFlash('success', "L'annonce {$ad->getTitle()} a été correctement modifiée!"); 
// { } en php pour donner la priorité dexécution

  return $this->redirectToRoute('ads_show', ['slug'=> $ad->getSlug()]);

}

dump($ad);
return $this->render('ad/edit.html.twig', [
 'form'=> $form->createView(),
 'ad'=>$ad
]);
}



 /**
     * @Route("/ads/{slug}", name="ads_show")
     */
 public function show(Ad $ad)
 {  


  dump($ad);

  return $this->render('ad/show.html.twig', [
    'ad'=> $ad
  ]);
}





      /**
     * @Route("/ads/{slug}/delete", name="ads_delete")
       * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Cette annonce ne vous appartient pas.")
     */
      public function delete(Ad $ad, ObjectManager $manager)
      {  

        $manager->remove($ad);
        $manager->flush();


        $this->addFlash('success', "L'annonce a été correctement supprimée!"); 

        return $this->redirectToRoute('account_index');


      }



    }

