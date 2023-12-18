<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class OCRController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/ocr/upload", name="ocr_upload")
     */
    public function uploadForm()
    {
        return $this->render('ocr/upload.html.twig');
    }

    /**
     * @Route("/ocr/process-file", name="process_file", methods={"POST"})
     */
    public function processFile(Request $request)
    {
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            return $this->redirectToRoute('ocr_upload');
        }

        $processedData = $this->processUploadedFile($uploadedFile);

        return $this->render('ocr/show.html.twig', [
            'processed_data' => $processedData,
        ]);
    }

    private function processUploadedFile(UploadedFile $file): array
    {
        $pdfFilePath = $file->getPathname();
        $tiffFilePath = $this->convertPdfToTiff($pdfFilePath);
        $text = $this->runTesseract($tiffFilePath);
        unlink($tiffFilePath);

        return $this->runPythonScript($text);
    }

    private function convertPdfToTiff(string $pdfFilePath): string
    {
        $tempDir = sys_get_temp_dir();
        $tiffFilePath = $tempDir . '/' . uniqid('converted_', true) . '.tiff';

        $process = new Process(['gs', '-sDEVICE=tiff24nc', '-r300', '-o', $tiffFilePath, $pdfFilePath]);
        $process->mustRun();

        return $tiffFilePath;
    }

    private function runTesseract(string $imageFilePath): string
    {
        $process = new Process(['tesseract', $imageFilePath, '-']);
        $process->setTimeout(120); // Définir un délai plus long, par exemple 120 secondes

        $process->mustRun();

        return $process->getOutput();
    }

    private function runPythonScript(string $tesseractOutput): array
    {
        $scriptPath = $this->getParameter('kernel.project_dir') . '/scripts/process_data.py';
        $pythonExecutable = $this->getParameter('kernel.project_dir') . '/venv/bin/python';

        $command = [$pythonExecutable, $scriptPath, $tesseractOutput];
        $process = new Process($command);

        // Ajoutez un var_dump pour voir la commande exacte
        var_dump($command);

        try {
            // Ajoutez un var_dump pour voir la sortie brute
            var_dump($process->mustRun()->getOutput());

            $decodedOutput = json_decode($process->getOutput(), true);

            // Ajoutez un var_dump pour voir la sortie JSON décodée
            var_dump($decodedOutput);

            return $decodedOutput;
        } catch (ProcessFailedException $exception) {
         
            throw new \RuntimeException("Erreur lors de l'exécution du script Python. " . $exception->getMessage());
        }
    }
}
