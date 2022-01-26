<?php

namespace App\Command;


use App\Entity\Comment;
use App\Entity\FileManaged;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'database:clear',
    description: 'Clean up database',
)]
class DatabaseClearCommand extends Command
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Which table should be cleared')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1 == 'all') {
            $this->truncateEntities([
                Comment::class,
                FileManaged::class,
                Post::class
            ]);
        }

        if ($arg1 == 'comment') {
            $this->truncateEntities([
                Comment::class
            ]);
        }

        if ($arg1 == 'fileManaged') {
            $this->truncateEntities([
                FileManaged::class
            ]);
        }

        if ($arg1 == 'post') {
            $this->truncateEntities([
                Post::class
            ]);
        }


        $io->success('You have already clean up database table '.$arg1.' .');

        return Command::SUCCESS;
    }

    private function truncateEntities(array  $entities)
    {
        $connection = $this->entityManager->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        if ($databasePlatform->supportsForeignKeyConstraints()){
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        }
        foreach ($entities as $entity){
            $query = $databasePlatform->getTruncateTableSQL(
                $this->entityManager->getClassMetadata($entity)->getTableName()
            );
            $connection->executeQuery($query);
        }
        if ($databasePlatform->supportsForeignKeyConstraints()){
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
