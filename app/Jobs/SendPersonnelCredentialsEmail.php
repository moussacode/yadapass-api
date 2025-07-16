<?php

namespace App\Jobs;

use App\Mail\PersonnelCredentialsMail;
use App\Models\PersonnelSecurite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPersonnelCredentialsEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $personnel;
    public $password;
    public $isReset;

    /**
     * Create a new job instance.
     */
    public function __construct(PersonnelSecurite $personnel, string $password, bool $isReset = false)
    {
        $this->personnel = $personnel;
        $this->password = $password;
        $this->isReset = $isReset;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->personnel->email)
                ->send(new PersonnelCredentialsMail($this->personnel, $this->password, $this->isReset));
            
            Log::info('Email envoyé avec succès', [
                'personnel_id' => $this->personnel->id,
                'email' => $this->personnel->email,
                'is_reset' => $this->isReset
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email', [
                'personnel_id' => $this->personnel->id,
                'email' => $this->personnel->email,
                'error' => $e->getMessage()
            ]);
            
            // Relancer l'exception pour que le job soit marqué comme échoué
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job d\'envoi d\'email échoué', [
            'personnel_id' => $this->personnel->id,
            'email' => $this->personnel->email,
            'exception' => $exception->getMessage()
        ]);
    }
}