<?php

namespace App\Controller;

use App\Entity\ContactoEmergencia;
use App\Entity\Paciente;
use App\Entity\PersonData;
use App\Entity\TipoDocumento;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PacienteController extends AbstractController
{
    /**
     * @Route("/paciente/buscar/{idPaciente}", name="buscarPaciente")
     */
    public function buscarPaciente($idPaciente = null)
    {
        $em = $this->getDoctrine()->getManager();
        $paciente = $em->getRepository(Paciente::class)->find($idPaciente);

        $jsPaciente = array(
            "id"=>$paciente->getIdPaciente(), 
            "nombre"=>$paciente->getTipoDocumento()->getNombre(), 
            "estado"=>($paciente->getEstado()) ? 'Activo' : 'No activo', 
            "telefono"=>$paciente->getTipoDocumento()->getTelefono()
        );

        return new JsonResponse($jsPaciente);
    }

    /**
     * @Route("/paciente/eliminar/{idPaciente}", name="eliminarPaciente")
     */
    public function eliminarPaciente($idPaciente = null)
    {
        $em = $this->getDoctrine()->getManager();
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
        
        $jsPaciente = array(
            "id"=>$paciente->getIdPaciente(), 
            "nombre"=>$paciente->getTipoDocumento()->getNombre(), 
            "estado"=>($paciente->getEstado()) ? 'Activo' : 'No activo', 
            "telefono"=>$paciente->getTipoDocumento()->getTelefono()
        );
        return new JsonResponse($jsPaciente);

    }

    /**
     * 
     * @Route("/paciente/guardar", name="guardarPaciente", methods={"POST"})
     */
    public function agregarPaciente(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

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

        $jsPaciente = array(
            "id"=>$paciente->getIdPaciente(), 
            "nombre"=>$paciente->getTipoDocumento()->getNombre(), 
            "estado"=>($paciente->getEstado()) ? 'Activo' : 'No activo', 
            "telefono"=>$paciente->getTipoDocumento()->getTelefono()
        );
        return new JsonResponse($jsPaciente);
    }

    /**
     * @Route("/paciente/editar", name="editarPaciente", methods={"POST"})
     */
    public function editarPaciente(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paciente = $em->getRepository(Paciente::class)->find($request->get('idPaciente'));
        $personData = $em->getRepository(PersonData::class)->buscarPersonData($paciente->getTipoDocumento()->getTipoDocumento()->getId(), $paciente->getTipoDocumento()->getDocumento());
        $datosPersona = $this->editarDatosPersona($paciente, $personData, $request);
        $personaContacto = $this->editarContactoPersona($paciente, $request);
        $datosPaciente = $this->editarDatosPaciente($paciente, $request);
        
        $em->flush();
        
        $jsPaciente = array(
            "id"=>$paciente->getIdPaciente(), 
            "nombre"=>$paciente->getTipoDocumento()->getNombre(), 
            "estado"=>($paciente->getEstado()) ? 'Activo' : 'No activo', 
            "telefono"=>$paciente->getTipoDocumento()->getTelefono()
        );
        return new JsonResponse($jsPaciente);
    }
    
    public function editarDatosPersona($paciente, $personData, $request){

        $em = $this->getDoctrine()->getManager();

        $personDataEditar = [
            'documento' => $paciente->getTipoDocumento()->getDocumento(),
            'tipoDocumento' => $paciente->getTipoDocumento()->getTipoDocumento()->getId()
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

    public function editarContactoPersona($paciente, $request){
        $em = $this->getDoctrine()->getManager();
        $personaContacto = $em->getRepository(ContactoEmergencia::class)->find($paciente->getPersonaContacto()->getId());
        $request->get('nombreContacto') != "" ? $personaContacto->setNombre($request->get('nombreContacto')) : false;
        $request->get('telefonoContacto') != "" ? $personaContacto->setTelefono($request->get('telefonoContacto')) : false;

        return $personaContacto;
    }

    public function editarDatosPaciente($paciente, $request){
        $em = $this->getDoctrine()->getManager();
        $request->get('numSeguridadSocial') != "" ? $paciente->setNumSeguroSocial($request->get('numSeguridadSocial')) : false;
        $request->get('estado') != "" ? $paciente->setEstado($request->get('estado')) : false;

        return $paciente;
    }
    
}
