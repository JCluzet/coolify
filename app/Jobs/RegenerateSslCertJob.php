<?php

namespace App\Jobs;

use App\Helpers\SSLHelper;
use App\Models\SslCertificate;
use App\Models\Team;
use App\Notifications\SslExpirationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RegenerateSslCertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected ?Team $team = null,
        protected ?int $server_id = null,
        protected bool $force_regeneration = false,
    ) {}

    public function handle()
    {
        $query = SslCertificate::query();

        if ($this->server_id) {
            $query->where('server_id', $this->server_id);
        }

        if (! $this->force_regeneration) {
            $query->where('valid_until', '<=', now()->addDays(14));
        }

        $certificates = $query->get();

        if ($certificates->isEmpty()) {
            return;
        }

        $regenerated = collect();

        foreach ($certificates as $certificate) {
            try {
                SSLHelper::generateSslCertificate(
                    commonName: $certificate->common_name,
                    subjectAlternativeNames: $certificate->subject_alternative_names,
                    resourceType: $certificate->resource_type,
                    resourceId: $certificate->resource_id,
                    serverId: $certificate->server_id,
                    validityDays: 365
                );
                $regenerated->push($certificate);
            } catch (\Exception $e) {
                Log::error('Failed to regenerate SSL certificate: '.$e->getMessage());
            }
        }

        if ($regenerated->isNotEmpty()) {
            $this->team?->notify(new SslExpirationNotification($regenerated));
        }
    }
}
