<?php

// src/Controller/PdfController.php

namespace App\Controller;

use App\Service\FactureExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    private $factureExtractor;

    public function __construct(FactureExtractor $factureExtractor)
    {
        $this->factureExtractor = $factureExtractor;
    }

    /**
     * @Route("/upload-pdf", name="upload_pdf")
     */
    public function uploadPdf(Request $request): Response
    {
        // Récupération du fichier PDF soumis via le formulaire
        $file = $request->files->get('pdf_file');

        // Vérifier si un fichier a été soumis
        if (!$file) {
            // Rendre le formulaire initial si aucun fichier n'a été soumis
            return $this->render('pdf/upload.html.twig');
        }

        // Vérifier si le fichier est bien un fichier PDF
        if ($file->getMimeType() !== 'application/pdf') {
            // Gérer le cas où un fichier non PDF est soumis
            return new Response('Veuillez soumettre un fichier PDF valide.');
        }

        // Chemin où sauvegarder le fichier temporairement
        $filePath = $file->getPathname();

        // Conversion du PDF en texte brut
        $text = $this->convertPdfToText($filePath);

        // Utilisation de la classe FactureExtractor pour extraire le numéro de facture
    $results = $this->factureExtractor->extractInformation($text);


       // Rendu du texte brut et des résultats de la comparaison en HTML
    return $this->render('pdf/show.html.twig', [
        'html' => nl2br($text),
        'results' => $results,
    ]);
    }

    /**
     * Convertit un fichier PDF en texte brut.
     *
     * @param string $filePath Chemin du fichier PDF
     *
     * @return string Texte brut extrait du PDF
     */
    private function convertPdfToText(string $filePath): string
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filePath);

        return $pdf->getText();
    }
}
