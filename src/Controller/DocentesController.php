<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PUNTO 14 DEL CUADERNO PÁGINA 27 (INSERT)

namespace App\Controller;

// ES NECESARIO LA SIGUIENTE LÍNEA (se puede poner sola automáticamente al escribirlo más abajo): 
use App\Entity\Docentes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// AÑADIMOS LO SIGUIENTE PARA PODER HACER CRUD, USAR BBDD (se puede poner automáticamente al escribirlo más abajo): 
use Doctrine\ORM\EntityManagerInterface; // ESTE ES MÁS ESPECÍFICO, EL MANAGER REGISTRY ES MÁS GENÉRICO Y MÁS POTENTE.
// SE AÑADE JSONRESPONSE EN EL SELECT PARA LA SALIDA QUE SEA MEDIANTE JSON (se puede poner automáticamente):
use Symfony\Component\HttpFoundation\JsonResponse;

/*
 * RELACIÓN DE ENDPOINTS DEL PROYECTO:
    - http://127.0.0.1:8000/docentes/insertar-docentes/11223344G/Iván Rodríguez/47
    - http://127.0.0.1:8000/alumnos/insertar-alumnos
    - http://127.0.0.1:8000/docentes/consultar-docentes
*/

// SACAMOS ESTA RUTA AFUERA. QUEDA ASÍ. AGREGAMOS GUIÓN BAJO
#[Route('/docentes', name: 'app_docentes_')]
class DocentesController extends AbstractController
{
    // MODIFICAMOS LA SIGUIENTE RUTA Y LA QUE HABÍA ANTERIORMENTE LA SACAMOS FUERA (AHORA LA ANTERIOR SE LLAMA /DOCENTES)
    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        return $this->render('docentes/index.html.twig', [
            'controller_name' => 'DocentesController',
        ]);
    }

    // CREAMOS RUTA PARA INSERTAR DOCENTES MEDIANTE PARÁMETROS:
    #[Route('/insertar-docentes/{nif}/{nombre}/{edad}', name: 'insertar_docentes')]
    public function insertarDocentes(String $nif, String $nombre, int $edad, EntityManagerInterface $entityManager): Response
    {
        // ENDPOINT: http://127.0.0.1:8000/docentes/insertar-docentes/11223344G/Iván Rodríguez/47

        // AL PONER "DOCENTES", PULSAR INTRO A LA PRIMERA OPCIÓN (use App\Entity\Docentes) QUE SALE PARA QUE ARRIBA DEL TODO APAREZCA use App\Entity\Docentes;
        $docente = new Docentes();

        // ASIGNAMOS:
        $docente->setNif($nif);
        $docente->setNombre($nombre);
        $docente->setEdad($edad);

        $entityManager->persist($docente);
        $entityManager->flush();

        return new Response("<h1>Docente insertado</h1>");
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PUNTO 15 DEL CUADERNO PÁGINA 28 (SELECT)
// SELECT MEDIANTE JSON
    #[Route('/consultar-docentes', name: 'consultar_docentes')]
        public function consultarDocentes(EntityManagerInterface $entityManager): JsonResponse
        {
            // ENDPOINT: http://127.0.0.1:8000/docentes/consultar-docentes

            $docentes = $entityManager->getRepository(Docentes::class)->findAll();

            $json = array();
            foreach ($docentes as $docente) {
                $json[] = array(
                    "nif" => $docente->getNif(),
                    "nombre" => $docente->getNombre(),
                    "edad" => $docente->getEdad(),
                );
            }

            return new JsonResponse($json);

            # return new Response("" . var_dump($docentes)); // PARA QUE SALGAN TODOS LOS DATOS AMONTONADOS
        }
}

