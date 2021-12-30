<?php

namespace App\Controller;

use App\Entity\Paciente;
use App\Entity\PersonData;
use App\Entity\TipoDocumento;
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
     * @Route("/usuario", name="usuario")
     */
    public function index(): Response
    {
        return $this->render('usuario/index.html.twig', [
            'controller_name' => 'UsuarioController',
        ]);
    }

    /**
     * @Route("/usuario/buscar/{idUsuario}", name="buscar")
     */
    public function buscarUsuario($idUsuario = null, UserPasswordEncoderInterface $passwordEncoder)
    {

        $em = $this->getDoctrine()->getManager();
        // $usuario = $em->getRepository(User::class)->find($idUsuario);
        $roles = [];
        $rol1 = $em->getRepository(Rol::class)->find(1);
        array_push($roles, $rol1);
        $rol2 = $em->getRepository(Rol::class)->find(2);
        array_push($roles, $rol2);
        $tipoDocumento = $em->getRepository(TipoDocumento::class)->find(1);

        $personData = $personData = new PersonData(
            $tipoDocumento, 
            3333331,
            'Felipe Jaramillo', 
            5555555,
            0, 
            '@gmal',
            'itagui',
            new \DateTime()
        );

        $user = new User('akuji3', $personData);
        $user->setPassword($passwordEncoder->encodePassword($user, '140994'));
        $user->addRole($rol1);
        $user->addRole($rol2);
        // $em->persist($personData);
        // $em->persist($user);
        // $em->flush();
        var_dump($user->getRoles()[0]);
        return $this->render('usuario/index.html.twig', [
            'controller_name' => 'UsuarioController',
        ]);
    }

    /**
     * 
     * @Route("/usuario/guardar", name="guardarUsuario")
     */
    public function agregarPaciente(Request $request, UserPasswordEncoderInterface $passwordEncoder)
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

        for($i=0; $i<count($idRoles); $i++){
            $rol = $em->getRepository(Rol::class)->find($idRoles[$i]);
            $rol != null ? $user->addRole($rol) : false;
        }

        $em->persist($personData);
        $em->persist($user);
        $em->flush();

        // $jsPaciente = array(
        //     "id"=>$paciente->getIdPaciente(), 
        //     "nombre"=>$paciente->getTipoDocumento()->getNombre(), 
        //     "estado"=>($paciente->getEstado()) ? 'Activo' : 'No activo', 
        //     "telefono"=>$paciente->getTipoDocumento()->getTelefono()
        // );
        return new JsonResponse('holi');
    }
}
