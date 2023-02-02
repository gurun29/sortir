<?php

namespace App\Controller;

use App\Entity\ImagesParticipant;
use App\Entity\Participant;
use App\Form\AdminImportUserCsvType;
use App\Form\AdminImportUserType;
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
    public function importUserCSV(
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

        if ($ImportUserCSVForm->isSubmitted() && $ImportUserCSVForm->isValid() ) {
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

    /**
     * @Route("/importUser", name="importUser")
     */
    public function importUser (
        EntityManagerInterface $entityManager,
        Request $request,
        CampusRepository $campusRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $monProfil = $this->getUser();
        $newParticipant = new Participant();

        $nouveauParticipantForm = $this->createForm(AdminImportUserType::class,$newParticipant);
        $nouveauParticipantForm->handleRequest($request);

        $image = $nouveauParticipantForm->get('images')->getData();

        if ($nouveauParticipantForm->isSubmitted() && $nouveauParticipantForm->isValid()
            //&& $nouveauParticipantForm->get('mdp')->getData() === $nouveauParticipantForm->get('mdp2')->getData()
            //&& ($monProfil <> $nouveauParticipantForm || $nouveauParticipantForm->get('mdp')->getData()<>"" || $image)
           )
        {
            //dd($monProfil);

                $mdphash=$nouveauParticipantForm->get('plainPassword')->getData();
            $newParticipant->setPassword(
                    $passwordHasher->hashPassword(
                        $newParticipant,
                        $mdphash
                    )
                );

            If ($image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );



                    // On crée l'image dans la base de données
                    $img = new ImagesParticipant();
                    $img->setName($fichier);
                    $newParticipant->setImagesParticipant($img);
                }

                //$test = $monProfil->getRoles();
                $test2 = array("ROLE_USER");
//                $newParticipant->setRoles(array(["ROLE_USER"]));
                $newParticipant->setRoles($test2);
                //dump($test);
                //dump($test2);
                //dump($monProfil);
                //dd($nouveauParticipantForm);
                //$testmdp = $nouveauParticipantForm->get('plainPassword');
                //$monProfil->setPassword($testmdp);

                //dump($testmdp);
                //dd($monProfil);
            //dd($monProfil);
            //dump($monProfil);
            //dd($monProfilCopy);

                $entityManager->persist($newParticipant);

                $entityManager->flush();
                $this->addFlash('sucess','profil ajouté');

            }



        return $this->render('admin/AdminCreerParticipant.tml.twig', [
            'importUserForm' => $nouveauParticipantForm ->createView()
        ]);

    }

}
