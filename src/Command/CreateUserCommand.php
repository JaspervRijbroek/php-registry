<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
        protected static $defaultName = 'app:create-user';

        protected function configure()
        {
            parent::configure();

            $this
                ->setDescription('Creates a user')
                ->setHelp('Creates a user in the system')
                ->addArgument('email', InputArgument::REQUIRED)
                ->addArgument('password', InputArgument::REQUIRED)
                ->addOption('admin');
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $roles = ['ROLE_USER'];

            if($input->getOption('admin')) {
                $roles[] = 'ROLE_ADMIN';
            }

            $user = new User();
            $user->setEmail($input->getArgument('email'))
                ->setPassword($input->getArgument('password'))
                ->setRoles($roles);

            $output->writeln('User created');
            return 0;
        }
}