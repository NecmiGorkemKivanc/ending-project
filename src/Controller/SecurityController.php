<?php 

namespace App\Controller;
use App\Entity\User;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
class SecurityController extends AbstractController
{
    
 /**
     * @Route("/make_admin/{id}")
     */
    public function makeAdmin(Request $request, $id) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
 
 $entityManager->persist($user);
 $entityManager->$this->setRole('ROLE_ADMIN');
 $entityManager->flush();
 
 return new response('user role changed to admin succesfully');
    }
    

}

?>