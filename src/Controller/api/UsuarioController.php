<?php

namespace App\Controller\api;

use App\Entity\PersonData;
use App\Entity\TipoDocumento;
use App\Utilities\UtilityEditar;
use App\Entity\Rol;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/usuario/buscar/{idUsuario}")
     * @Rest\View(serializerGroups={"user"}, serializerEnableMaxDepthChecks=true)
     */
    public function buscarUsuarioAction($idUsuario = null, ManagerRegistry $doctrine)
    {

        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($idUsuario);

        return $user;
    }

    
    /**
     * @Rest\Post(path="/usuario/guardar")
     * @Rest\View(serializerGroups={"user"}, serializerEnableMaxDepthChecks=true)
     */
    public function agregarUsuarioAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $tipoDocumento = $em->getRepository(TipoDocumento::class)->find($request->get('tipoDocumento'));
        $personData = $personData = new PersonData(
            $tipoDocumento, 
            $request->get('documento'),
            $request->get('nombre'), 
            $request->get('telefono'),
            $request->get('sexo'), 
            $request->get('correo'),
            $request->get('direccion'),
            new \DateTime($request->get('fechaNacimiento'))
        );
        $user = new User(
            $request->get('username'),
            $personData,
        );
        $user->setPassword($passwordEncoder->encodePassword($user, $request->get('password')));
        $idRoles = $request->get('roles');

        for($i=0; $i<count($idRoles); $i++){
            $rol = $em->getRepository(Rol::class)->find($idRoles[$i]);
            $rol != null ? $user->addRole($rol) : false;
        }

        $em->persist($personData);
        $em->persist($user);
        $em->flush();
      
        return $user;
    }

    /**
     * @Rest\Delete(path="/usuario/eliminar/{idUser}")
     * @Rest\View(serializerGroups={"user"}, serializerEnableMaxDepthChecks=true)
     */
    public function eliminarUsuarioAction($idUser = null, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($idUser);
        $personDataEliminar = [
            'documento' => $user->getTipoDocumento()->getDocumento(),
            'tipoDocumento' => $user->getTipoDocumento()->getTipoDocumento()->getId()
        ];
        
        $em->remove($user);
        $em->flush();
        $eliminarPersonData = $em->getRepository(PersonData::class)->eliminarPersonData($personDataEliminar);
 
        return $user;

    }
    
   
    /**
     * @Rest\Patch(path="/usuario/editar")
     * @Rest\View(serializerGroups={"user"}, serializerEnableMaxDepthChecks=true)
     */
    public function editarUsuarioAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($request->get('idUsuario'));
        $personData = $em->getRepository(PersonData::class)->buscarPersonData($user->getTipoDocumento()->getTipoDocumento()->getId(), $user->getTipoDocumento()->getDocumento());
        
        $utility = new UtilityEditar($doctrine);

        $datosPersona = $utility->editarDatosPersona($user, $personData, $request);

        $datosUser = $utility->editarDatosUsuario($user, $request, $passwordEncoder);

        return $user;
    }
}
