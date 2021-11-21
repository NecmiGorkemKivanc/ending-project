<?php
namespace App\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categories;
use App\Entity\Product;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Doctrine\DBAL\Statement;
use Doctrine\DBAL\Connection;

class CategoryController extends AbstractController
{

    /**
     * @Route("/category/new", name="category_new")
     */
    public function new(Request $request): Response
    {
      
      $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'buraya erişemezsin');  
      $connection = $this->getDoctrine()->getManager()->getConnection(); 
     $categories=$this->getDoctrine()->getManager()->getRepository(Categories::class)->findAll();       
     $categories = new Categories();
        $form = $this->createFormBuilder($categories)
        ->add('Title', TextType::class)
        ->add('description', TextType::class)
        ->add('parent', EntityType::class, [
        
          'class'=>Categories::class,
          'choice_label'=>'title',
          'required' => false,
        
        
         ] )           
        ->add('save', SubmitType::class, ['label' => 'Create Category'])
      
            ->getForm();
           
            $form->handleRequest($request); 
            if ($form->isSubmitted())
             {
                        
                          $categories = $form->getData();
             
                
                 $entityManager = $this->getDoctrine()->getManager();
                 $entityManager->persist($categories);
                 $entityManager->flush();
                 
                 echo "Category created successfully";
             
            
             
        }
       
            return $this->render('Category/create.html.twig', [
                'form' => $form->createView(),
            ]);
            
           


}
/**
 * @Route ("/category/update/{id}", name="update_category")
 * Method ({"GET", "POST"})
 */
public function update(Request $request, $id){
  $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'buraya erişemezsin');

  $categories = $this->getDoctrine()->getRepository(Categories::class)->find($id);

  $form = $this->createFormBuilder($categories)
               ->add('title', TextType::class, array('attr' =>array('class' => 'form-control')))
               ->add('description', TextType::class, array('required' =>false,
                  'attr' =>array('class' =>'form-control')))
                  ->add('parent', EntityType::class, [
             
                    'class'=>Categories::class,
                    'choice_label'=>'title',
                    'required' => false
                    
                   ] )
                  ->add('save', SubmitType::class, array(
                    'label' =>'Update',
                    'attr' =>array('class'=>'btn btn-primary mt-3')
                  ))
                  ->getForm();
   
     $form->handleRequest($request);
   
     if($form->isSubmitted() && $form->isValid()){
   
       $entityManager = $this->getDoctrine()->getManager();
       $entityManager->flush();
   
      return new response('Category Successfully updated');
     }
   
     return $this->render('category/update.html.twig',array(
       'form'=>$form->createView()
     ));
   }
       /**
 * @Route ("/category/delete/{id}")
 */
public function delete(Request $request, $id){
  $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'buraya erişemezsin');
  $categories = $this->getDoctrine()->getRepository(Categories::class)->find($id);
  $form = $this->createFormBuilder($categories)
  ->add('title', TextType::class, array('attr' =>array('class' => 'form-control')))
  ->add('description', TextType::class, array('required' =>false,
     'attr' =>array('class' =>'form-control')))
     ->add('DELETE CATEGORY', SubmitType::class)
    ->getForm();

    $form->handleRequest($request);
    if($form->isSubmitted()
    )
    {

      $entityManager = $this->getDoctrine()->getManager();
    
    $entityManager->remove($categories);
    $entityManager->flush();
    
    return new response('Category Successfully Deleted');
    }
    return $this->render('category/delete.html.twig',array(
        'form'=>$form->createView()
      ));
    
}
 /**
* @Route ("/category/detail/{id}")
*/

public function categoryDetail(Request $request, $id)
{
 

   $categories = $this->getDoctrine()->getRepository(Categories::class)->find($id);
   return $this->render('category/detail.html.twig', [
       'title'=>$categories->getTitle(),
       'description'=>$categories->getDescription(),
       
    ] );
}
    
    /**
 * @Route ("/category/all", name="all_categories")
 */

public function showAllCategories()
{
  
   $connection = $this->getDoctrine()->getManager()->getConnection(); 
 
     $query="SELECT id,title,parent_id FROM categories ORDER BY parent_id ASC"; 
     // $list değişkeninde sırayla tümkategoriler bulunuyor. 
    $list=array(); 
     
     $stmt=$connection->prepare($query); 
     $row=$stmt->execute()->fetchAll(); 
      $list=$row['id']=$row; 
 
   // Şimdi sırayla eklenmişleri hiyerarşilenmiş bir biçimde $tree değişkenine vereceğiz. 
    $tree = array(); 
 
    // Her bir kategoriyi tek tek döndür... 
    foreach ($list as $id => $item) 
     { 
        
        if ($item['parent_id'] == null){ 
           
            // Eğer kategori id set edilmiş ise birincil düzey yap... 
             $kontrol=$id; 
            
         }else{ 
           
           // Eğer kategori birincil düzey ise... (yani alt kategorileri almıyoruz!) 
             $kontrol=null; 
        } 
 
         if ($id == $kontrol) 
         { 
          
            // $tree değişekeninde birincil düzey olarak ekledik. 
            $tree[$item['id']] = $item; 
            
            // Bu kategoriyi kaydettiğimiz için de (yani işimiz bitti!) $list dizisinden kaldırıyoruz. 
            unset($list[$id]); 
 
            // Ve şimdi can alıcı nokta! Bu ana kategorinin alt kategorisi var mı diye alt kategorilerine bakıyoruz... 
             
             
         $this->findSubcats($list, $tree[$item['id']]);  
            
        } 
         
    } 
    


  $repository = $this->getDoctrine()->getRepository(Categories::class);
  $categories = $repository->findAll();
  // foreach($tree as $key){
  //   foreach ($key as $value){
     
  //   }
  // }
  // var_dump($value);exit;
   return $this->render('category/allcategories.html.twig', [
    'categories'=>$categories,
    'tree'=>$tree
  ] );
}

 public  function findSubcats(&$list, &$selected)
{
    /*  Kategori_List() fonksiyonu ile beraber çalışır.
     *  Alt kategorileri arayan yardımcı fonksiyonumuz.
     *  &$list: Veritabanından çektiğimiz ham kategorileri içeriyor.
     *  &$selected: Üzerinde işlem yapılacak (varsa alt kategorisi eklenecek) kategoriyi içeriyor.
     */

    // Her bir kategoriyi tek tek döndür...
    foreach ($list as $id => $item)
    {
        // Eğer babasının kimliğiyle kendi kimliği aynıysa... (yani alt kategori ise!)
        if ($item['parent_id'] == $selected['id'])
        {
            // Seçimin "sub_cats"ına alt kategorisini ekle.
            $selected['sub_cats'][$item['id']] = $item;

            // Babasını bulduğuna göre artık $list'eden kaldırabiliriz.
            unset($list[$id]);

            // Alt kategorinin de çocuğu olabilme ihtimali için aynı işlemleri ona da yapıyoruz...
           $this->findSubcats($list, $selected['sub_cats'][$item['id']]);
        }
    }
}

  
}
?>