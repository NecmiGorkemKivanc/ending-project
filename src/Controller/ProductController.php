<?php 
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categories;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class ProductController extends AbstractController


{
   // this method works for add new product.
    /**
     * @Route("/product/new", name="product_new")
     */

    public function new(Request $request): Response
    {
      $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'buraya erişemezsin');
                
      $categories=$this->getDoctrine()->getManager()->getRepository(Categories::class)->findAll();
      
     
    
       
        $product = new Product();
        $form = $this->createFormBuilder($product)
            
            ->add('Title', TextType::class)
            ->add('description', TextType::class)
            ->add('amount', TextType::class)
            ->add('price', TextType::class)
            ->add('image', TextType::class )
            ->add('category', EntityType::class, [
   
              'class' => Categories::class,
              
           
              'choice_label' => function ($categories) {
                return $categories->getTitle();
              },
              'required'=>"true",
             'multiple'=>true,
             'expanded'=>true
              ])
            ->add('save', SubmitType::class, ['label' => 'Create Product'])
            ->getForm();
                  
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
         {
                      $product = $form->getData();
                     
             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($product);    
             
             $entityManager->flush();
            
             
             echo "Product created successfully";
         
        }
    
        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
        ]);
}

//this method works for update products
 /**
 * @Route ("/product/update/{id}", name="update_product")
 * Method ({"GET", "POST"})
 */
public function update(Request $request, $id){

  $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'buraya erişemezsin');
    $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
  
    $form = $this->createFormBuilder($product)
                 ->add('title', TextType::class, array('attr' =>array('class' => 'form-control')))
                 ->add('description', TextType::class, array('required' =>false,
                    'attr' =>array('class' =>'form-control')))
                   ->add('amount', TextType::class, array('attr' =>array('class' => 'form-control')))
                    ->add('price', TextType::class, array('attr' =>array('class' => 'form-control')))
                    ->add('image', TextType::class, array('attr' =>array('class' => 'form-control')))
                    ->add('category', EntityType::class, [
   
                      'class' => Categories::class,
                  
                   
                      'choice_label' => 'title',
                      'multiple'=>true,
                      'expanded'=>true,
                      ])

                 ->add('save', SubmitType::class, array(
                   'label' =>'Update',
                   'attr' =>array('class'=>'btn btn-primary mt-3')
                 ))
                 ->getForm();
  
    $form->handleRequest($request);
  
    if($form->isSubmitted() && $form->isValid()){
  
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->flush();
  
     return new response('Product Successfully updated');
    }
  
    return $this->render('product/update.html.twig',array(
      'form'=>$form->createView()
    ));
  }

  // this method works for delete products
     /**
 * @Route ("/product/delete/{id}", name="product_delete", methods={"GET","POST"})
 */
public function delete(Request $request, $id){
  
  
  
  $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'buraya erişemezsin');
    $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

    $form = $this->createFormBuilder($product)
    ->add('title', TextType::class, array('attr' =>array('class' => 'form-control')))
    ->add('description', TextType::class, array('required' =>false,
       'attr' =>array('class' =>'form-control')))
      ->add('amount', TextType::class, array('attr' =>array('class' => 'form-control')))
       ->add('price', TextType::class, array('attr' =>array('class' => 'form-control')))
       ->add('image', TextType::class, array( 'required'=>false,'attr' =>array('class' => 'form-control')))
       

    ->add('DELETE PRODUCT', SubmitType::class)
    ->getForm();

    $form->handleRequest($request);
    if($form->isSubmitted()
    )
    {
      $id=$request->query->get('id');
      
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($product);
    $entityManager->flush();
    
   return $this->redirectToRoute('product_list');
    }
    return $this->render('product/delete.html.twig',array(
        'form'=>$form->createView()
      ));
     
    
}

//this method works for show to you product detail.
 /**
 * @Route ("/product/detail/{id}", name="product_detail")
 */

 public function productDetail(Request $request, $id)
 {
  $this->denyAccessUnlessGranted('ROLE_USER',null,'buraya erişemezsin');
  $categories=$this->getDoctrine()->getManager()->getRepository(Categories::class)->findAll();

    $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
    return $this->render('product/detail.html.twig', [
        'title'=>$product->getTitle(),
        'description'=>$product->getDescription(),
        'amount'=>$product->getAmount(),
        'price'=>$product->getPrice(),
        'image'=>$product->getImage(),   
        
     ] );
 }

/**
 * @Route ("/product/all", name="product_all")
 */

public function showAllProduct(Request $request)
{
  $this->denyAccessUnlessGranted('ROLE_USER',null,'buraya erişemezsin');
  $repository = $this->getDoctrine()->getRepository(Product::class);
  $product = $repository->findAll();
  $form = $this->createFormBuilder($product)
  ->add('logout', SubmitType::class)
    ->getForm();
    $form->handleRequest($request);
    if($form->isSubmitted()
    )
    {
     
      return $this->redirectToRoute('app_logout');
    }
  return $this->render('product/allproducts.html.twig', 
     [
    'product'=>$product,
'form'=>$form->createView()
    
 ] );

}

}
?>