<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PUNTO 14 DEL CUADERNO PÁGINA 27 (INSERT)

namespace App\Controller;

// LA SIGUIENTE LÍNEA SE AÑADE AUTOMÁTICAMENTE AL PONER ALUMNOS(); MÁS ABAJO
use App\Entity\Alumnos;
use App\Entity\Docentes;
use App\Repository\AlumnosRepository;
// LA SIGUIENTE LÍNEA SE AÑADE AUTOMÁTICAMENTE AL PONER DATETIME(); MÁS ABAJO
use DateTime;
// AÑADIMOS LA SIGUIENTE LÍNEA. SE CREA AUTOMÁTICAMENTE SI MÁS ABAJO (EN INSERTARALUMNOS) ESCRIBIMOS SOLO "ENTITYMANAGERINTERFACE"
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// SACAMOS LA RUTA FUERA Y AGREGAMOS GUIÓN BAJO:
#[Route('/alumnos', name: 'app_alumnos_')]
class AlumnosController extends AbstractController
{
    // AÑADIMOS ESTA RUTA PARA ÍNDICE:
    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        return $this->render('alumnos/index.html.twig', [
            'controller_name' => 'AlumnosController',
        ]);
    }

    // AÑADIMOS ESTA RUTA PARA AÑADIR ALUMNOS:
    #[Route('/insertar-alumnos', name: 'insertar_alumnos')]
    public function insertarAlumnos(EntityManagerInterface $entityManager): Response
    {
        // ENDPOINT: http://127.0.0.1:8000/alumnos/insertar-alumnos

        // CREAMOS ARRAYS DENTRO DE ARRAY
        $registros = array (
            "a1" => array (
                "nif" => "11112222C",
                "nombre" => "Juan Carlos Romero",
                "fechanac" => "2000-04-02",
                "pagado" => 0,
                "importe" => 4100.50,
                "docentes_nif" => "11223344G"
            ),

            "a2" => array (
                "nif" => "33334444J",
                "nombre" => "Jessica Picón",
                "fechanac" => "2000-04-02",
                "pagado" => 1,
                "importe" => 4100.50,
                "docentes_nif" => "11223344G"
            ),

            "a3" => array (
                "nif" => "55556666L",
                "nombre" => "Hugo Jiménez",
                "fechanac" => "2000-06-02",
                "pagado" => 1,
                "importe" => 4100.50,
                "docentes_nif" => "11223344G"
            )
        );

        // AÑADIMOS FOREACH
        foreach ($registros as $fila) {
            $alumno = new Alumnos();
            $alumno->setNif($fila['nif']);
            $alumno->setNombre($fila['nombre']);

            $fecha = new DateTime($fila['fechanac']);
            $alumno->setfechanac($fecha);

            $alumno->setPagado($fila['pagado']);
            $alumno->setImporte($fila['importe']);

            $docente = new Docentes();
            $repoDocentes = $entityManager->getRepository(Docentes::class);
            $valorNifDocente = $fila['docentes_nif'];
            $docente = $repoDocentes->findOneBy(["nif" => $valorNifDocente]);
            $alumno->setDocentesNif($docente);

            $entityManager->persist($alumno);
            $entityManager->flush();
        }

        return new Response("<h1>¡TODO CORRECTO! - Alumnos insertados</h1>");
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
#[Route('/consultar-alumnos-cobrados/{pagado}', name: 'consultar_alumnos_cobrados')]
    //public function consultarAlumnosCobrados(ManagerRegistry $doctrine, int $pagado): JsonResponse
    public function consultarAlumnosCobrados(AlumnosRepository $repoAlumnos, int $pagado): Response
    {
        // endpoint de ejemplo: http://127.0.0.1:8000/alumnos/consultar-alumnos-cobrados/1
        // $repoAlumnos = $doctrine->getRepository(Alumnos::class);
        $alumnos = $repoAlumnos->alumnosCobrados($pagado);

        $json = array();
        foreach ($alumnos as $alumno) {
            $json[] = array(
                "nif" => $alumno->getNif(),
                "nombre" => $alumno->getNombre(),
                "importe" => $alumno->getImporte(),
            );
        }

        // return new JsonResponse($json);


        return $this->render('alumnos/index.html.twig', [
            'controller_name' => 'AlumnosController',
            'alumnos' => $alumnos,
        ]);
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

#[Route('/actualizar-alumno/{nif}/{importe}', name: 'actualizar_alumno')]
    public function actualizarAlumno(EntityManagerInterface $gestorEntidades, String $nif, float $importe): Response
    {
        // Endpoint de ejempo: http://127.0.0.1:8000/alumnos/actualizar-alumno/11112222C/255.50
        $repoAlumnos = $gestorEntidades->getRepository(Alumnos::class);
        $alumno = $repoAlumnos->findOneBy(["nif" => $nif]);

        // Si no encuentra el alumno, manda un error...
        if (!$alumno) {
            throw $this->createNotFoundException(
                ('ERROR!!!! No se ha encontrado: ' . $nif)
            );
        }

        $alumno->setPagado(1);
        $alumno->setImporte($importe);
        $gestorEntidades->flush();

        // En el redirect, podemos pasar un conjunto de parámetros: Ej: ['pagado' => 1, 'otro' => "a"]
        return $this->redirectToRoute("app_alumnos_consultar_alumnos_cobrados", ['pagado' => 1]);
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    #[Route('/borrar-alumno/{nif}', name: 'borrar_alumno')]
    public function borrarAlumno(EntityManagerInterface $gestorEntidades, String $nif): Response
    {
        // Endpoint de ejempo: http://127.0.0.1:8000/alumnos/borrar-alumno/11112222C
        $alumno = $gestorEntidades->getRepository(Alumnos::class)->findOneBy(["nif" => $nif]);
        $gestorEntidades->remove($alumno);
        $gestorEntidades->flush();

        return $this->redirectToRoute("app_alumnos_consultar_alumnos_cobrados", ['pagado' => 1]);
    }
}

