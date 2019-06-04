<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {

    	$prenom = ["Eric" => 52, "Fabien" => 32, "Anne" => 25];
    	return $this->render('home/index.html.twig', [
    		'controller_name' => 'HomeController',
    		'title' => 'Bienvenue dans ce blog',
    		'age' => '17',
    		'tableau' => $prenom,
    	]);
    }


}