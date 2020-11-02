<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin
            ->setEmail('admin@test.com')
            ->setRoles([ 'ROLE_ADMIN' ])
            ->setPassword($this->passwordEncoder->encodePassword(
                $admin,
                'admin'
            ))
        ;
        $manager->persist($admin);

        $moderator = new User();
        $moderator
            ->setEmail('moderator@test.com')
            ->setRoles([ 'ROLE_MODERATOR' ])
            ->setPassword($this->passwordEncoder->encodePassword(
                $moderator,
                'moderator'
            ))
        ;
        $manager->persist($moderator);

        $user = new User();
        $user
            ->setEmail('user@test.com')
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'user'
            ))
        ;
        $manager->persist($user);

        $manager->flush();
    }
}
