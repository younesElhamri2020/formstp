<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Forms\productType;
use App\Repository\ProduitRepository;

use App\Services\CalculPrixTTC;
use App\Services\ProductFormHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
  private $produitsRepository;
  private $session;
  private $paramBinterface;
  private $calcullprix;
  private $produitform;
    public function __construct(ProduitRepository $produitsRepository,SessionInterface $session ,ParameterBagInterface $paramBinterface, CalculPrixTTC $calcullprix, ProductFormHandler $produitform)
    {
        $this->produitsRepository=$produitsRepository;
        $this->session=$session;
        $this->paramBinterface=$paramBinterface;
        $this->produitform=$produitform;
        $this->calcullprix=$calcullprix;
    }

    /**
     * @Route("/produits", name="produits")
     */
    public function index(): Response
    {
        $produits=$this->produitsRepository->findAll();
        return $this->render('produit/index.html.twig', [
            'produits' =>  $produits
        ]);
    }


    /**
     * @Route("/produit/delete/{id}" ,name="produit.delete")
     */
    public function delete(Request $request, $id) {
        //find the object to delete
        $produit= $this->getDoctrine()->getRepository(Produit::class)->find($id);
        // delete object
        if ($this->isCsrfTokenValid('delete-item', $request->request->get('token'))) {
            $this->produitsRepository->deleteproduit($produit);

            $this->session->getFlashBag()->add(
                'success',
                'Enregistrement a été supprimmer'
            );
            return $this->redirectToRoute('produits');
        }

    }
    /**
     * @Route("/edit_produit/{id}", name="produit.edit", methods={"GET","POST"})
     */
    public function editProduit(Request $request,Produit $produit): Response
    {

        $form = $this->createForm(productType::class, $produit,[
            'required_ttc'=>true,

        ]);
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
                $produit->setPrix($produit->getPrix()+$produit->getPrix()*0.20);
            }

            $this->produitsRepository->edit_produit($produit);
            return $this->redirectToRoute('produits');}
        else {
            return $this->render('produit/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }



    /**
     * @Route("/produit/new" ,name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $produit = new Produit();

        $form = $this->createForm(productType::class, $produit);



        if($this->produitform->handle($request,$form)) {

            return $this->redirectToRoute('produits');
        }
        else {
            return $this->render('produit/new.html.twig', [
                'form' => $form->createView(),
            ]);
        }




    }

}
