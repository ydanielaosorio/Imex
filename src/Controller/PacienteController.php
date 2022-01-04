<?php

namespace App\Controller;

use App\Entity\ContactoEmergencia;
use App\Entity\Paciente;
use App\Entity\PersonData;
use App\Entity\TipoDocumento;
use App\Utilities\UtilityEditar;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class PacienteController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/paciente/buscar/{idPaciente}")
     * @Rest\View(serializerGroups={"paciente"}, serializerEnableMaxDepthChecks=true)
     */
    public function buscarPaciente($idPaciente = null, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $paciente = $em->getRepository(Paciente::class)->find($idPaciente);
        return $paciente;
    }


    /**
    * @Rest\Delete(path="/paciente/eliminar/{idPaciente}")
    * @Rest\View(serializerGroups={"paciente"}, serializerEnableMaxDepthChecks=true)
    */
    public function eliminarPacienteAction($idPaciente = null, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $paciente = $em->getRepository(Paciente::class)->find($idPaciente);
        $personDataEliminar = [
            'documento' => $paciente->getTipoDocumento()->getDocumento(),
            'tipoDocumento' => $paciente->getTipoDocumento()->getTipoDocumento()->getId()
        ];
        $contactoEmergencia = $em->getRepository(ContactoEmergencia::class)->find($paciente->getPersonaContacto()->getId());
        $em->remove($contactoEmergencia);
        $em->remove($paciente);
        $em->flush();
        $eliminarPersonData = $em->getRepository(PersonData::class)->eliminarPersonData($personDataEliminar);
       
        return $paciente;

    }

    
    /**
    * @Rest\Post(path="/paciente/guardar")
    * @Rest\View(serializerGroups={"paciente"}, serializerEnableMaxDepthChecks=true)
    */
    public function agregarPacienteAction(Request $request, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $paciente = $em->getRepository(Paciente::class)->find(22);


        $tipoDocumento = $em->getRepository(TipoDocumento::class)->find($request->get('tipoDocumento'));
        $personData = new PersonData(
            $tipoDocumento, 
            $request->get('documento'),
            $request->get('nombre'), 
            $request->get('telefono'),
            $request->get('sexo'), 
            $request->get('correo'),
            $request->get('direccion'),
            new \DateTime($request->get('fechaNacimiento'))
        );
        $personaContacto = new ContactoEmergencia($request->get('nombreContacto'), $request->get('telefonoContacto'));
        $paciente = new Paciente($request->get('estado'), $request->get('numSeguridadSocial'), $personData, $personaContacto);
        
        $em->persist($personData);
        $em->persist($personaContacto);
        $em->persist($paciente);
        $em->flush();

        return $paciente;
        
    }


    /**
    * @Rest\Patch(path="/paciente/editar")
    * @Rest\View(serializerGroups={"paciente"}, serializerEnableMaxDepthChecks=true)
    */
    public function editarPacienteAction(Request $request,
     ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $paciente = $em->getRepository(Paciente::class)->find($request->get('idPaciente'));
        $personData = $em->getRepository(PersonData::class)->buscarPersonData($paciente->getTipoDocumento()->getTipoDocumento()->getId(), $paciente->getTipoDocumento()->getDocumento());
        
        $utiity = new UtilityEditar($doctrine);

        $datosPersona = $utiity->editarDatosPersona($paciente, $personData, $request);
        $personaContacto = $utiity->editarContactoPersona($paciente, $request);
        $datosPaciente = $utiity->editarDatosPaciente($paciente, $request);
        
        return $paciente;
    }
    
}
