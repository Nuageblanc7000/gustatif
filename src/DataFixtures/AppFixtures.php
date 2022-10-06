<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\City;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Comment;
use App\Entity\Origine;
use App\Entity\Category;
use App\Entity\Plat;
use App\Entity\Restaurant;

use Faker\Factory as fake;
use App\Service\FileUpload;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppFixtures extends Fixture
{
    public function __construct(public ParameterBagInterface $parameterBag, public UserPasswordHasherInterface $hasher, public FileUpload $fileUpload)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = fake::create('fr_FR');

        //on regarde que les dossiers restaurant/plats/avatar soit vide avant de refaire des fixtures
        if(file_exists($this->parameterBag->get('resto_images')))
        {
            $scanResto = scandir($this->parameterBag->get('resto_images'));
           $restoFiles = array_splice($scanResto, 2, count($scanResto));
            foreach ($restoFiles as  $value) {
             unlink($this->parameterBag->get('resto_images').'/'.$value);
            }
        }else{
            mkdir($this->parameterBag->get('resto_images'));
        }
        if(file_exists($this->parameterBag->get('resto_images')))
        {
            $scanPlats = scandir($this->parameterBag->get('resto_plats'));
           $restoFiles = array_splice($scanPlats, 2, count($scanPlats));
            foreach ($restoFiles as  $value) {
             unlink($this->parameterBag->get('resto_plats').'/'.$value);
            }
        }else{
            mkdir($this->parameterBag->get('resto_plats'));
        }
        if(file_exists($this->parameterBag->get('resto_images')))
        {
            $scanPlats = scandir($this->parameterBag->get('avatar_user'));
           $restoFiles = array_splice($scanPlats, 2, count($scanPlats));
            foreach ($restoFiles as  $value) {
             unlink($this->parameterBag->get('avatar_user').'/'.$value);
            }
        }else{
            mkdir($this->parameterBag->get('avatar_user'));
        }

        //création d'un fichier json avec les villes pour pouvoir faires des fixtures plus facilement.
        $cities = json_decode(file_get_contents($this->parameterBag->get('json_path_fixtures')), true);

        //on scan le dossier pour créer les images aléatoires.
        $scanImages = scandir($this->parameterBag->get('faker_resto'));
        $arrayImages = array_splice($scanImages, 2, count($scanImages));
        $scanPlats = scandir($this->parameterBag->get('faker_plats'));
        $arrayPlats = array_splice($scanPlats, 2, count($scanPlats));
        $names = [
            "En-cas burrito",
            "Chez les Mariachis",
            "Le Mexique de France",
            "Cuisine à la Tequila",
            "Le goût mexicain",
            "Fajitas aztèques",
            "El cactus mexicano",
            "Au cha l'heureux",
            "Resto Dix Vins",
            "Le coin d'Islande",
            "The best place",
            "A tasty break",
            "Just baked",
            "Fresh meal",
            "Enjoy",
            "A food choice",
            "FaceFood Africa",
            "acos El Quetzal",
            "Irldandia",
            "Veggie Party",
            "Fishy meal",
            "L'instant espagnol",
            "Bières et tapas",
            "La buena tortilla",
            "Festin d'Espagne",
            "Paëlla maison",
            "La saveur ibérique",
            "Solo de tortillas",
            "Red corrida",
            "Only Spaghetti",
            "Pizza per tutti",
            "Sapor'Italia",
            "Il Gorgonzola",
            "Pasta Pizza",
            "L'assiette d'Italie",
            "Torre di Pizza",
            "Morceau d'Italie",
            "Restaurant et Bar Vador",
            "La Bouche Des Goûts",
            "La brasserie marine",
            "La soif de goûts",
            "La cuisine du chef",
            "L’arrêt gustatif",
            "La pause déjeuner",
            "A la vôtre!",
            "Veggivore",
            "Le Muff faim",
            "Au dé lisse",
            "La salade Suisse",
            "Plats purs",
            "Ô bio",
            "Pomme du verger",
            "L'assiette anti-stress",
            "Ukra Délice",
            "4 saisons",
            "Natural",
            "A la Grec",
            "Breakfast Party",
            "Lunchester Food",
            "Bigben Lunch"
        ];
        foreach ($cities as $city) {
            $cityEntity = new City();
            $cityEntity->setCode($city['code'])
                ->setLocalite($city['localite'])
                ->setLongitude($city['longitude'])
                ->setLatitude($city['latitude'])
                ->setCoordonnees($city['Coordonnees'])
                ->setGeom($city['geom']);
            $arrayCities[] = $cityEntity;
            $manager->persist($cityEntity);
        }
        //créations des origines
        $tabOrgineFr =
            [
                'Italienne',
                'Belge',
                'Africaine',
                'Anglaise',
                'Française',
                'Irlandaise',
                'Islandaise',
                'Bulgare',
                'Suisse',
                'Ukrainienne',
                'Grecque'
            ];
        $entityOrigines = [];
        for ($i = 0; $i < count($tabOrgineFr); $i++) {
            $Origine = new Origine();
            $Origine->setName($tabOrgineFr[$i]);
            $Origine->setImage(strtolower($tabOrgineFr[$i]) . '.svg');
            $manager->persist($Origine);
            $entityOrigines[] = $Origine;
        }
        $tabCategory = ['fast-food', 'restaurant'];
        $categories = [];
        for ($c = 0; $c < count($tabCategory); $c++) {
            $category = new Category();
            $category->setName($tabCategory[$c]);
            $manager->persist($category);
            $categories[] = $category;
        }
        //création du user admin
        $admin = new User();
        $admin->setEmail('wetterene.remy@gmail.com')
            ->setPseudo('rw-admin')
            ->setIsAcountVerified(1);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin7382'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        //création du user pour les gens qui delete
        $anonymous = new User();
        $anonymous->setEmail('anonyme.remy@gmail.com')
            ->setPseudo('user-delete')
            ->setIsAcountVerified(1);
        $anonymous->setPassword($this->hasher->hashPassword($anonymous, 'admin7382'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($anonymous);
        
        //création des users 

        $users = [];
        $scanAvatarFixtures = scandir($this->parameterBag->get('faker_avatars'));
        $arrayAvatarsFixtures = array_splice($scanAvatarFixtures,2,count($scanAvatarFixtures));
        $counter = 1;
        for ($u = 0; $u <= 40; $u++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setCity($faker->randomElement($arrayCities))
                ->setIsAcountVerified(1)
                ->setPassword($this->hasher->hashPassword($user, 'test'))
                ->setPseudo($faker->userName)
                ->setRoles($u % 2 ? ['ROLE_RESTAURATEUR'] : [])
                ->setIsResto($u % 2 ? 1 : 0);$randomAvatar = $faker->randomElement($arrayAvatarsFixtures);
            $fileAvatar = new UploadedFile($this->parameterBag->get('faker_avatars'). '/' . $randomAvatar ,'avatar');
            $fileAvatarName = uniqid('avatar',true).'.'.$fileAvatar->guessExtension();
            copy($this->parameterBag->get('faker_avatars') . '/' . $randomAvatar, $this->parameterBag->get('avatar_user') . '/' . $fileAvatarName);
            $user->setAvatar($fileAvatarName);
            
            $manager->persist($user);
            $users[] = $user;

            if ($user->isIsResto()) {

                $restaurant = new Restaurant();
                $restaurant->addCategory($faker->randomElement($categories))
                    ->addOrigine($faker->randomElement($entityOrigines))
                    ->setAdress($faker->unique()->streetName())
                    ->setCity($faker->randomElement($arrayCities))
                    ->setDescription($faker->text(250))
                    ->setPhone($faker->phoneNumber())
                    ->setUser($user);
                $restaurant->setName($names[$u]);

                for ($img = 0; $img < rand(2,4); $img++) {
                    $image = new Image();
                    $randomImage = $faker->randomElement($arrayImages);
                    $file = new UploadedFile($this->parameterBag->get('faker_resto') . '/' . $randomImage, 'image');
                    $extension = $file->guessExtension();
                    $filename = uniqid() . '.' . $extension;
                    copy($this->parameterBag->get('faker_resto') . '/' . $randomImage, $this->parameterBag->get('resto_images') . '/' . $filename);
                    $image->setPath($filename);
                    $manager->persist($image);
                    $restaurant->addImage($image);
                    if($img === 0){
                        $restaurant->setCover($image->getPath());
                    }
                }
                for ($plat = 0; $plat < rand(1, 4); $plat++) {
                    $platResto = new Plat();
                    $randomPlat = $faker->randomElement($arrayPlats);
                    $file = new UploadedFile($this->parameterBag->get('faker_plats') . '/' . $randomPlat, 'plat');
                    $extension = $file->guessExtension();
                    $filePlatName = uniqid("plat",true) . '.' . $extension;
                    copy($this->parameterBag->get('faker_plats') . '/' . $randomPlat, $this->parameterBag->get('resto_plats') . '/' . $filePlatName);
                    $platResto->setImage($filePlatName);
                    $platResto->setName($faker->text(rand(10,25)));
                    $manager->persist($platResto);
                    $restaurant->addPlat($platResto);
                }
                $manager->persist($restaurant);

                $schedule = $restaurant->getSchedule();
                foreach ($schedule->getTimetables() as $key => $timeline) {
                    if ($key !== rand(1, count($schedule->getTimetables()))) {
                        $dateOpen = new DateTime('' . rand(
                            1, 6) . ':0');
                        $dateClose = new DateTime('' . rand(9,14) . ':0');
                        $dateOpenpm = new DateTime('' . rand(
                            15,19) . ':0');
                        $dateClosepm = new DateTime('' . rand(20,23) . ':0');
                        $timeline->setOpen($dateOpen)
                            ->setClose($dateClose)
                            ->setOpenpm($dateOpenpm)
                            ->setClosepm($dateClosepm)
                            ;
                        $manager->persist($timeline);
                    }
                }
                $manager->persist($schedule);

                for ($com = 0; $com <= rand(0, 8); $com++) {

                    $comment = new Comment();
                    $arrayNotRestaurateur = [];
               
                    foreach ($users as  $use) {
                        if ($restaurant->getUser() !== $use && $comment->getAuthor() !== $use) {
                            $arrayNotRestaurateur[] = $use;
                           
                            $comment->setAuthor($faker->randomElement($arrayNotRestaurateur));
                            $comment->setResto($restaurant);
                            $comment->setDateCom(new \DateTimeImmutable());
                            $comment->setDescription($faker->text(rand(20, 200)));
                            $comment->setRating(rand(0, 5));
                            $manager->persist($comment);
                        }
                    }
                }
            }
        }

        $manager->flush();
    }
}
