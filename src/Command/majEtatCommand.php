<?php
namespace App\Command;


use App\Controller\SortieController;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Services\GestionDate;
use Doctrine\Bundle\DoctrineBundle\Orm\ManagerRegistryAwareEntityManagerProvider;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Doctrine\Persistence\ManagerRegistry;

//#[AsCommand(name: 'app:create-user')]
//use App\Controller\ParticipantController;
//require_once('Controller\P');
class majEtatCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:majEtatCommand';
//    SortieRepository $sortieRepository
    // the command description shown when running "php bin/console list"
    //protected static $defaultDescription = 'Modifie les états selon les dates.';
    private GestionDate $gestionDate;

    public function __construct( GestionDate $gestionDate)
{

    parent::__construct();

    $this->gestionDate = $gestionDate;

}


    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        $this->gestionDate->modifEtatCloturee();


        $output->writeln($input);
        $output->writeln('modifEtatCloturee ok!');

        return Command::SUCCESS;
    }


}