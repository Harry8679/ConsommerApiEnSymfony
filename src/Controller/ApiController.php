<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/liste-regions", name="liste_regions")
     */
    public function listeRegions(SerializerInterface $serializer): Response
    {
        $mesRegions = file_get_contents('https://geo.api.gouv.fr/regions');
        // Decodage
        /* $mesRegionsTab = $serializer->decode($mesRegions, 'json');

        // Denormalisation
        $mesRegionsObj = $serializer->denormalize($mesRegionsTab, 'App\Entity\Region[]');*/

        // Déserialisation
        $mesRegionsObj = $serializer->deserialize($mesRegions,  'App\Entity\Region[]', 'json');

        // dd($mesRegionsObj);

        return $this->render('api/index.html.twig', [
            'regions' => $mesRegionsObj
        ]);
    }

    /**
     * @Route("/liste-departements-par-region", name="liste_departement_pr_region")
     */
    public function listeDepartementsParRegion(Request $request, SerializerInterface $serializer)
    {
        // liste-departements-par-region
        // Je récupère la région sélectionnée dans le formulaire
        $codeRegion = $request->query->get('region');

        // Je récupère les régions
        $regions = file_get_contents('https://geo.api.gouv.fr/regions');
        $regions = $serializer->deserialize($regions,  'App\Entity\Region[]', 'json');

        // Je récupère la liste des départements
        if ($codeRegion == null || $codeRegion == "Toutes") {
            $departements = file_get_contents('https://geo.api.gouv.fr/departements');
        } else {
            $departements = file_get_contents('https://geo.api.gouv.fr/regions/'.$codeRegion.'/departements');
        }

        $departements = $serializer->decode($departements, 'json');

        return $this->render('api/liste_dep_region.html.twig', [
            'regions' => $regions,
            'departements' => $departements
        ]);
    }
}
