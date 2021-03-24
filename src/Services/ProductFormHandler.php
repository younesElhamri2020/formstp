<?php


namespace App\Services;


use App\Repository\ProduitRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductFormHandler
{
    private $produitsRepository;
    private $paramBinterface;
    private $calcullprix;
    public function __construct(ProduitRepository $produitsRepository, CalculPrixTTC $calcullprix,ParameterBagInterface $paramBinterface )
    {
        $this->produitsRepository=$produitsRepository;
        $this->paramBinterface=$paramBinterface;
        $this->calcullprix=$calcullprix;
    }
        public function  handle(Request $request , $form){

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $produit = $form->getData();
                $image = $form['image']->getData();

                if ($image) {
                    $newFilename = uniqid().'.'.$image->guessExtension();
                    $image->move(
                        $this->paramBinterface->get('image_directory'),
                        $newFilename
                    );
                    $produit->setImage($newFilename);
                }
                $produit->setCreatedAt(new \DateTime());
                $checkTTC=$form->get('TTC')->getData();
                if($checkTTC==true)
                {
                    $produit->setPrix($this->calcullprix->calculerPrixTTC($produit->getPrix()));
                }
                $this->produitsRepository->store($produit);
               return true;
            }
            else{
                return false;}

   }
}