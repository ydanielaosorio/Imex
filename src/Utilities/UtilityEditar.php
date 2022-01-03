<?php

namespace App\Utilities;

use App\Entity\ContactoEmergencia;
use App\Entity\Paciente;
use App\Entity\PersonData;
use App\Entity\TipoDocumento;   
use App\Entity\Rol;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/* Clase para hacer ediciones */
class UtilityEditar
{
    private $doctrine;

    /*constructor para iniciliazar doctrine*/
    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }

    /*Editar los datos personales de una persona*/
    public function editarDatosPersona($persona, $personData, $request){

        $em = $this->doctrine->getManager();

        $personDataEditar = [
            'documento' => $persona->getTipoDocumento()->getDocumento(),
            'tipoDocumento' => $persona->getTipoDocumento()->getTipoDocumento()->getId()
        ];
        $request->get('nombre') != "" ? $personDataEditar['nombre'] = $request->get('nombre') : $personDataEditar['nombre'] = $personData->getNombre();
        $request->get('telefono') != "" ? $personDataEditar['telefono'] = $request->get('telefono') : $personDataEditar['telefono'] = $personData->getTelefono();
        $request->get('correo') != "" ? $personDataEditar['correo'] = $request->get('correo') : $personDataEditar['correo'] = $personData->getCorreo();
        $request->get('sexo') != "" ? $$personDataEditar['sexo'] = $request->get('sexo') : $personDataEditar['sexo'] = $personData->getSexo();
        $request->get('direccion') != "" ? $personDataEditar['direccion'] = $request->get('direccion') : $personDataEditar['direccion'] = $personData->getDireccion();
        $request->get('fechaNacimiento') != "" ? $personDataEditar['fechaNacimiento'] = $request->get('fechaNacimiento') : $personDataEditar['fechaNacimiento'] = $personData->getFechaNacimiento();
        $personData = $em->getRepository(PersonData::class)->editarPersonData($personDataEditar);

        return $personData;
    }

    /*Editar el contacto de emergencia de un paciente*/
    public function editarContactoPersona($persona, $request){
        $em = $this->doctrine->getManager();
        $personaContacto = $em->getRepository(ContactoEmergencia::class)->find($persona->getPersonaContacto()->getId());
        $request->get('nombreContacto') != "" ? $personaContacto->setNombre($request->get('nombreContacto')) : false;
        $request->get('telefonoContacto') != "" ? $personaContacto->setTelefono($request->get('telefonoContacto')) : false;
        $em->flush();

        return $personaContacto;
    }

    /*Editar los datos de un paciente*/
    public function editarDatosPaciente($persona, $request){
        $em = $this->doctrine->getManager();
        $request->get('numSeguridadSocial') != "" ?$persona->setNumSeguroSocial($request->get('numSeguridadSocial')) : false;
        $request->get('estado') != "" ? $persona->setEstado($request->get('estado')) : false;
        $em->flush();

        return $persona;
    }

    /*Editar los datos del usuario*/
    public function editarDatosUsuario($user, $request, $passwordEncoder){
        $em = $this->doctrine->getManager();
        $request->get('username') != "" ? $user->setUsername(($request->get('username'))) : false;
        $request->get('password') != "" ? $user->setPassword($passwordEncoder->encodePassword($user, $request->get('password'))) : false;

        $idRoles = $request->get('roles');
        
        for($i=0; $i<count($idRoles); $i++){
            $rol = $em->getRepository(Rol::class)->find($idRoles[$i]);
            $rol != null ? $user->addRole($rol) : false;
        }

        $em->flush();

        return $user;
    }

}