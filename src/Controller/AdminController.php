<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\AdminImportUserCsvType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/importUserCSV", name="importUserCSV")
     */
    public function importUser(
        EntityManagerInterface $em,
        Request $request,
        CampusRepository $campusRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $monProfil = $this->getUser();
        $newParticipant = new Participant();

        $ImportUserCSVForm = $this->createForm(AdminImportUserCsvType::class,$newParticipant);
        $ImportUserCSVForm->handleRequest($request);

        if ($ImportUserCSVForm->isSubmitted() ) {
//            dump($ImportUserCSVForm);
            $file = $ImportUserCSVForm->get('csv');
            $file->getData();
//            dump($file->getData());
            // Open the file
            //dd($file->getData()->getPathname());

            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $datas = $serializer->decode(file_get_contents($file->getData()->getPathname()), 'csv', [CsvEncoder::DELIMITER_KEY => ';']);
//            dump($datas);

            foreach ($datas as $data) {
                $newParticipant = new Participant();
//                dump($data);
                foreach ($data as $key => $value) {
//                    dump($key);
//                    dump($value);
                    if ($key==='nom') $newParticipant->setNom($value);
                    if ($key==='prenom') $newParticipant->setPrenom($value);
                    if ($key==='telephone') $newParticipant->setTelephone($value);
                    if ($key==='mail') $newParticipant->setMail($value);
                    if ($key==='mot_passe') {
                        $mdphash = $value;
                        $newParticipant->setPassword(
                            $passwordHasher->hashPassword(
                                $newParticipant,
                                $mdphash
                            )
                        );
                        //$newParticipant->setPassword($value);
                    }
                    if ($key==='administrateur') $newParticipant->setAdministrateur($value);
                    if ($key==='actif') $newParticipant->setActif($value);
                    if ($key==='pseudo') $newParticipant->setPseudo($value);
                    if ($key==='roles') $newParticipant->setRoles(array($value) );


                    if ($key==='est_rattache_a_id') {
                        //$campus = $campusRepository->findOneBy(array('id'=>$value));
                        $campus = $campusRepository->findOneBy(array('nom'=>$value));
                        $newParticipant->setEstRattacheA($campus); //TODO Find Campus?
                    }
                }

                //dd($newParticipant);
                $em->persist($newParticipant);

            }
//dd();
            $em->flush();


//            if (($handle = fopen($file->getData()->getPathname(), "r")) !== false) {
//
//
//                // Read and process the lines.
//                // Skip the first line if the file includes a header
//                while (($data = fgetcsv($handle)) !== false) {
//
//
//
//                    // Do the processing: Map line to entity, validate if needed
//                    //$entity = new Participant();
//                    // Assign fields
//                    dump($entity);
//                    dump($data[0]);
//                    $entity->setNom($data[0]);
//                    dd($entity);
//
//                    $em->persist($entity);
//                }
//                fclose($handle);
//                $em->flush();
//            }
        }

        return $this->render('admin/AdminImport.tml.twig', [
            'importUserCSVForm' => $ImportUserCSVForm ->createView()
        ]);

    }
}
