<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
use App\Entity\Fruit;

#[AsCommand(
    name: 'fetch:fruits',
    description: 'This command will fetch fruits from remote URL and store in fruit table in database',
)]
class FetchFruitsCommand extends Command
{
    private $entityManager;

    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure(): void
    {
        // Add arguments and options
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->entityManager;
        $io = new SymfonyStyle($input, $output);
        
        $fruitsJson = file_get_contents('https://fruityvice.com/api/fruit/all');
        $fruits = json_decode($fruitsJson, TRUE);
        if ($fruits) {
            foreach ($fruits as $fruit) {
                $fruitObj = new Fruit();
                $fruitObj->setName($fruit['name']);
                $fruitObj->setFamily($fruit['family']);
                $fruitObj->setFruitOrder($fruit['order']);
                $fruitObj->setGenus($fruit['genus']);
                $fruitObj->setNutritions($fruit['nutritions']);
                $fruitObj->setIsFavourite(0);
                $em->persist($fruitObj);
            }
            $em->flush();

            // send mail to admin that fruits are added
            $email = (new TemplatedEmail())
                        ->from('notifications@fruitsninja.com')
                        ->to(new Address('admin@fruitsninja.com'))
                        ->subject('Fruits are added in database')

                        // path of the Twig template to render
                        ->htmlTemplate('emails/fruits-added.html.twig')
                    ;

            $this->mailer->send($email);

            $io->success('Fruits added successfully.');
        } else {
            $io->success('No fruits added');
        }

        return Command::SUCCESS;
    }
}
