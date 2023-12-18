<?php

// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Fournisseur;
use App\Entity\Facture;
use App\Entity\Secteur;
use App\Entity\FournisseurHasSecteur;
use App\Entity\StationEssence;
use App\Entity\UserHasFacture;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $faker = Factory::create();

        // for ($i = 0; $i < 3; $i++) {
        //     $fournisseur = new Fournisseur();
        //     // configurez les propriétés du fournisseur
        //     $manager->persist($fournisseur);
        // }
        // $manager->flush(); // Assurez-vous de flush les fournisseurs pour les sauvegarder dans la base de données

        // // Utilisez les fournisseurs existants pour créer les factures
        // for ($i = 0; $i < 5; $i++) {
        //     $facture = new Facture();
        //     // configurez les propriétés de la facture

        //     // Récupérez un fournisseur existant depuis la base de données
        //     $fournisseur = $manager->getRepository(Fournisseur::class)->findOneBy([]);

        //     // Associez le fournisseur à la facture
        //     $facture->setFournisseur($fournisseur);

        //     $manager->persist($facture);
        // }

        // for ($i = 0; $i < 5; $i++) {
        //     $facture = new Facture();
        //     $facture->setDateFacture($faker->dateTimeBetween('-1 year', 'now'));
        //     $facture->setNumeroFacture($faker->ean13);
        //     $facture->setCodeProduit($faker->ean13);
        //     $facture->setQuantite($faker->numberBetween(1, 10));
        //     $facture->setPrixUnitaire($faker->randomFloat(2, 1, 100));
        //     $facture->setMontantHT($faker->randomFloat(2, 1, 1000));
        //     $facture->setRemise($faker->randomFloat(2, 0, 100));
        //     $facture->setTVA($faker->randomFloat(2, 1, 20));
        //     $facture->setMontantTTC($faker->randomFloat(2, 1, 1000));

            
        // }

        $manager->flush();
    }
}
