<?php
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categories;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Subcategories;

class subCategoriesController extends AbstractController
{
/**
 * @Route ("/subcategory/new", name="category_all")
 */
public function new(Request $request): Response
{
  $this->getDoctrine()->getManager()->getRepository(Categories::class)->findAll();
  $this->getDoctrine()->getManager()->getRepository(Subcategories::class)->findAll();

  $subcategories = new Subcategories();
        $form = $this->createFormBuilder($subcategories)
        ->add('Title', TextType::class)
        ->add('categories', EntityType::class, [
          
          'class' => Categories::class,
          'choice_label'=>'title',
          
          ])
        ->add('save', SubmitType::class, ['label' => 'Create Category'])
        ->getForm();
                  
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
         {
                      $subcategories = $form->getData();
                     
             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($subcategories);    
             
             $entityManager->flush();
            
             
             echo "Subcategory created successfully";

}
return $this->render('subcategory/create.html.twig', [
  'form' => $form->createView(),
]);

}


}
?>