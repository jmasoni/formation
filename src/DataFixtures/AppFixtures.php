<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

	private $encoder;
	public function __construct(UserPasswordEncoderInterface $encoder){

		$this->encoder=$encoder;


	}


	public function load(ObjectManager $manager)
	{

		$adminRole=new Role();
		$adminRole->setTitle('ROLE_ADMIN');
		$manager->persist($adminRole);

		$adminUser= new User();
		$adminUser->setFirstName('Jerome')
		->setLastName('Masoni')
		->setEmail('jm@gmail.com')
		->setHash($this->encoder->encodePassword($adminUser, "password"))
		->setPicture("https://via.placeholder.com/64")
		->setIntroduction("C'est une introduction")
        ->setDescription("description")
        ->setSlug('jerome-masoni')
        ->addUserRole($adminRole);

        $manager->persist($adminUser);


		$slugify = new Slugify();
		$title= "Titre de l'annonce n°";
		//$slug=$slugify->slugify($title);

		for($h=1; $h <5 ; $h++){

			$user= new User();
			$user->setSlug($slugify->slugify("prenom$h-nom$h"))
			->setFirstName("prenom$h")
			->setLastName("nom$h")
			->setPicture("https://via.placeholder.com/64")
			->setEmail("test$h@test.fr")
			->setIntroduction("introduction$h")
			->setDescription("description$h");
			$encoded = $this->encoder->encodePassword($user, "pass");

			$user->setHash($encoded);

			$manager->persist($user);

			for ($i=1; $i <5 ; $i++) {
				$ad= new Ad();
				$ad->setTitle("$title$i$h")
				->setSlug($slugify->slugify($ad->getTitle()))
				->setCoverImage("https://via.placeholder.com/350")
				->setIntroduction("C'est une introduction")
				->setContent("<p>Je suis le contenu</p>")
				->setPrice(mt_rand(40,200))
				->setRooms(mt_rand(1,5))
				->setAuthor($user);

				for ($j=0; $j <mt_rand(2,5) ; $j++) {
					$image = new Image();
					$image ->setUrl("https://via.placeholder.com/350")
					-> setCaption("Legende de $j")
					-> setAd($ad);
					$manager->persist($image);
				}


				for ($j=1; $j <mt_rand(1,5) ; $j++) {

					$booking= new Booking();
					$booking->setCreatedAt(new \DateTime())
					->setStartDate(new \DateTime("+5 days"))
					->setEndDate(new \DateTime("+15 days"))
					->setAmount($ad->getPrice()*10)
					->setComment("Commentaire de la réservation $j")
					->setAd($ad)
					->setBooker($user);

					$manager->persist($booking);

				}


				$manager->persist($ad);
			}
		}

		$manager->flush();
	}
}
