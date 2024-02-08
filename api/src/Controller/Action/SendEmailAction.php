<?php

namespace App\Controller\Action;

use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SendEmailAction extends AbstractController
{
    private MailerService $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    #[Route('/send-email', name: 'send-email', methods: ['GET'])]
    public function testMailer(): Response
    {
        // Options pour l'e-mail de test
        $options = [
            'emailTo' => 'louisantoine920@gmail.com',
            'lastnameTo' => 'NomDuDestinataire',
            'firstnameTo' => 'PrenomDuDestinataire',
            'validationToken' => 'TokenDeValidation',
        ];

        $templateId = 3;

        try {
            $this->mailerService->sendEmail($options, $templateId);

            return new Response('E-mail envoyé');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
        }
    }
}