<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Entity\getHash;
use App\Entity\getNewPassword;
use App\Entity\getOldPassword;
use App\Entity\setHash;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\encodePassword;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{




	/**
     * @Route("/ads/register", name="account_register")
     */
	public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
	{	

		$user=new User();


		$form=$this->createForm(RegistrationType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			$password=$user->getHash();
			$encoded=$encoder->encodePassword($user,$password);
			$user->setHash($encoded);

			$slugify = new Slugify();

			$user->setSlug($slugify->slugify("{$user->getFirstName()}-{$user->getLastName()}"));



			$manager->persist($user);
			$manager->flush();

			$this->addFlash('success', "Votre compte a bien été créé!"); 
// { } en php pour donner la priorité dexécution

			return $this->redirectToRoute('account_login');

		}


		return $this->render('account/registration.html.twig', [
			'form'=> $form->createView()
		]);
	}







    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {

    	$error = $utils->getLastAuthenticationError();
    	return $this->render('account/login.html.twig', [
    		'error' => $error
    	]);
    }



	/**
     * @Route("/logout", name="account_logout")
     */
	public function logout()
	{

	}


	/**
     * @Route("/account/profile", name="account_profile")
     */
	public function profile(Request $request, ObjectManager $manager)
	{
		$user=$this->getUser();
		$form=$this->createForm(AccountType::class, $user);

		
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){


			$manager->persist($user);
			$manager->flush();

			$this->addFlash('success', "Votre compte a bien été modifié!"); 


			return $this->redirectToRoute('homepage');

		}

		return $this->render('account/profile.html.twig', [
			'form'=> $form->createView()
		]);

	}



	/**
     * @Route("/account/password-update", name="account_password")
     */
	public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
	{	

		$user=$this->getUser();
		//dump($user);
		$passwordUpdate= new PasswordUpdate();


		$form=$this->createForm(PasswordUpdateType::class, $passwordUpdate);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			if(password_verify($passwordUpdate->getOldPassword(), $user->getHash())){

				$this->addFlash('danger', "Votre ancien mot de passe est incorrect!"); }

				else{  

					$password=$passwordUpdate->getNewPassword();
					$encoded=$encoder->encodePassword($user,$password);
					$user->setHash($encoded);


					$manager->persist($user);
					$manager->flush();

					$this->addFlash('success', "Votre mot de passe a bien été modifié!"); 


					return $this->redirectToRoute('homepage');
				}

			}


			return $this->render('account/password.html.twig', [
				'form'=> $form->createView()
			]);
		}


	/**
     * @Route("/account/", name="account_index")
     */
public function myAccount()
{


return $this->render('user/index.html.twig', [
				'user'=> $this->getUser(), //utilisateur actuellement connecté
			]);

}



}



