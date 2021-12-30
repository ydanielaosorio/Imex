<?php

namespace App\Controller;

use App\Entity\Paciente;
use App\Entity\PersonData;
use App\Entity\TipoDocumento;
use App\Utilities\UtilityEditar;
use App\Entity\Rol;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioController extends AbstractController
{
    /**
     * @Route("/usuario/buscar/{idUsuario}", name="buscar")
     */
    public function buscarUsuario($idUsuario = null)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($idUsuario);

        $roles = $user->getRoles();
        $nombreRoles = [];

        for($i=0; $i<count($roles); $i++){
            array_push($nombreRoles, $roles[$i]->getNombre());
        }
        
        $jsUsuario = array(
            "id" => $user->getId(),
            "nombre" => $user->getTipoDocumento()->getNombre(),
            "telefono" => $user->getTipoDocumento()->getTelefono(),
            "roles" => $roles
        );        
        return new JsonResponse($jsUsuario);
    }

    /**
     * 
     * @Route("/usuario/guardar", name="guardarUsuario", methods={"POST"})
     */
    public function agregarUsuario(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
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
        $nombreRoles = [];

        for($i=0; $i<count($idRoles); $i++){
            $rol = $em->getRepository(Rol::class)->find($idRoles[$i]);
            $rol != null ? $user->addRole($rol) : false;
            array_push($nombreRoles, $rol->getNombre());
        }

        $em->persist($personData);
        $em->persist($user);
        $em->flush();

        $jsUsuario = array(
            "id" => $user->getId(),
            "nombre" => $user->getTipoDocumento()->getNombre(),
            "telefono" => $user->getTipoDocumento()->getTelefono(),
            "roles" =>  $nombreRoles
        );        
        return new JsonResponse($jsUsuario);
    }

    /**
     * @Route("/usuario/eliminar/{idUser}", name="eliminarUsuario", methods={"DELETE"})
     */
    public function eliminarUsuario($idUser = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($idUser);
        $personDataEliminar = [
            'documento' => $user->getTipoDocumento()->getDocumento(),
            'tipoDocumento' => $user->getTipoDocumento()->getTipoDocumento()->getId()
        ];
        
        $em->remove($user);
        $em->flush();
        $eliminarPersonData = $em->getRepository(PersonData::class)->eliminarPersonData($personDataEliminar);

        $roles = $user->getRoles();
        $nombreRoles = [];

        for($i=0; $i<count($roles); $i++){
            array_push($nombreRoles, $roles[$i]->getNombre());
        }
        
        $jsUsuario = array(
            "id" => $user->getId(),
            "nombre" => $user->getTipoDocumento()->getNombre(),
            "telefono" => $user->getTipoDocumento()->getTelefono(),
            "roles" =>  $nombreRoles
        );        
        return new JsonResponse($jsUsuario);

    }
    
    /**
     * @Route("/usuario/editar", name="editarUsuario", methods={"POST"})
     */
    public function editarUsuario(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($request->get('idUsuario'));
        $personData = $em->getRepository(PersonData::class)->buscarPersonData($user->getTipoDocumento()->getTipoDocumento()->getId(), $user->getTipoDocumento()->getDocumento());
        
        $utility = new UtilityEditar($this->getDoctrine());

        $datosPersona = $utility->editarDatosPersona($user, $personData, $request);

        $datosUser = $utility->editarDAtosUsuario($user, $request, $passwordEncoder);

        $roles = $user->getRoles();
        $nombreRoles = [];

        for($i=0; $i<count($roles); $i++){
            array_push($nombreRoles, $roles[$i]->getNombre());
        }
        
        $jsUsuario = array(
            "id" => $user->getId(),
            "nombre" => $user->getTipoDocumento()->getNombre(),
            "telefono" => $user->getTipoDocumento()->getTelefono(),
            "roles" =>  $nombreRoles
        ); 

        return new JsonResponse($jsUsuario);
    }
    
    
}
