<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Categories;


class AdminController extends AbstractController
{
/**
     * @Route("/product/list", name="product_list")
     */
    public function ListProduct()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'buraya erişemezsin');
        $product = $this->getDoctrine()->getRepository(Product::class)->findAll();
        
       
        return $this->render('admin/productlist.html.twig',[
            'product'=>$product
        ]);
       
    }
    
    }
   