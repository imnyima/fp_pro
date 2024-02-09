<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PUNTO 14 DEL CUADERNO PÁGINA 27 (INSERT)

namespace App\Controller;

// LA SIGUIENTE LÍNEA SE AÑADE AUTOMÁTICAMENTE AL PONER ALUMNOS(); MÁS ABAJO
use App\Entity\Alumnos;
use App\Entity\Docentes;
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
            $alumno->setNif($fila["nif"]);
            $alumno->setNombre($fila["nombre"]);

            // TRANSFORMAMOS LA SIGUIENTE LÍNEA EN DATETIME
            $fecha = new DateTime($fila["fechanac"]);
            $alumno->setFechanac($fecha);

            // SEGUIMOS SETEANDO
            $alumno->setPagado($fila["pagado"]);
            $alumno->setImporte($fila["importe"]);

            // PARA CLAVE FORÁNEA DE DOCENTES:
            $docente = new Docentes();
            $repoDocentes = $entityManager->getRepository(Docentes::class);
            $valorNifDocente = $fila["docentes_nif"];
            $docente = $repoDocentes->findOneBy(["nif" => $valorNifDocente]);
            $alumno->setDocentesNif($docente);
            // LO DE SETDOCENTESNIF SE PUEDE MIRAR EN DOCENTES.PHP CÓMO ESTÁ ESCRITO

            $entityManager->persist($alumno);
            $entityManager->flush();
        }
        return new Response("<h1>¡TODO CORRECTO! - Alumnos insertados</h1>");
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

