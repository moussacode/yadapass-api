<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CarteEtudianteCreee extends Notification 
{
    use Queueable;

    public $etudiant;
    public $carte;

    public function __construct($etudiant, $carte)
    {
        $this->etudiant = $etudiant;
        $this->carte = $carte;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
       $pdf = $this->carte->generatePdf();
    
    return (new MailMessage)
        ->subject('🎓 Votre carte étudiante ESTM')
        ->greeting('Bonjour ' . $this->etudiant->nom)
        ->line('Veuillez trouver ci-joint votre carte étudiante.')
        ->line('Votre matricule est : ' . $this->etudiant->matricule)
        ->attachData($pdf, 'carte_etudiante.pdf', [
            'mime' => 'application/pdf',
        ]);
            
    }
}
