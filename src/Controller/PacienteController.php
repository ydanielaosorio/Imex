<?php

namespace App\Controller;


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
     * @Route("/paciente", name="paciente")
     */
    public function index(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        // $tipoDocumento = new TipoDocumento();
        // $tipoDocumento->setNombre('Registro civil');
        $id = 1;
        $documento = 1038413897;

        $tipoDocumento = $em->getRepository(TipoDocumento::class)->find($id);

        //$personData = $em->getRepository(PersonData::class)->encontrarPaciente($documento, $tipoDocumento);

        $paciente = new Paciente();

        $paciente = $em->getRepository(Paciente::class)->findAll();

        // $personData = new PersonData();

        // $personData->setTipoDocumento($tipoDocumento);
        // $personData->setNombre('Cindy Tatiana Osorio Gomez');
        // $personData->setDocumento(1001724951);
        // $personData->setTelefono(3155286357);
        // $personData->setCorreo('lindacindytati@gmail.com');
        // $personData->setSexo(1);
        // $personData->setDireccion('Cll 28A #37-33');
        // $personData->setFechaNacimiento(new \DateTime('2002/10/26'));

        // $paciente->setTipoDocumento($personData);
        // $paciente->setEstado(1);

        // $em->persist($personData);
        // $em->persist($paciente);
        // $em->flush();
        var_dump($paciente);

        return new Response('hola');
        // return $this->json([
        //     'message' => 'mensaje',
        //     'path' => 'src/Controller/PacienteController.php',
        // ]);
        // return $this->render('paciente/index.html.twig', [
        //     'controller_name' => 'PacienteController',
        // ]);
    }

    /**
     * @Route("/paciente/buscar/{idPaciente}", name="paciente")
     */
    public function buscarPaciente($idPaciente = null)
    {
        $em = $this->getDoctrine()->getManager();

        $paciente = $em->getRepository(Paciente::class)->find($idPaciente);

        var_dump($paciente);

        // return new JsonResponse($paciente);
    }
}
