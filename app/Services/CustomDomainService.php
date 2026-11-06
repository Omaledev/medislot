<?php

namespace App\Services;

use App\Models\Hospital;

class CustomDomainService
{
    /**
     * Normalize a raw domain input string.
     */
    public function normalizeDomain(string $domain): string
    {
        $domain = trim($domain);
        $domain = strtolower($domain);

        // Strip protocols if entered
        $domain = preg_replace('#^https?://#i', '', $domain);

        // Strip path, query params, and trailing slash
        $domain = explode('/', $domain)[0];
        $domain = explode('?', $domain)[0];

        // Strip port if present
        $domain = explode(':', $domain)[0];

        return trim($domain);
    }

    /**
     * Check if a domain string is valid and not reserved.
     */
    public function isValidDomain(string $domain): bool
    {
        $domain = $this->normalizeDomain($domain);

        if (empty($domain) || strlen($domain) > 253) {
            return false;
        }

        // Basic domain validation
        if (! filter_var('http://'.$domain, FILTER_VALIDATE_URL)) {
            return false;
        }

        $reserved = [
            'localhost',
            '127.0.0.1',
            'medislot.com',
            'medislot.test',
            'api.medislot.com',
            'admin.medislot.com',
            'app.medislot.com',
        ];

        return ! in_array($domain, $reserved, true);
    }

    /**
     * Verify DNS records for a hospital's custom domain.
     *
     * @return array{success: bool, message: string}
     */
    public function verify(Hospital $hospital): array
    {
        if (! $hospital->custom_domain) {
            return [
                'success' => false,
                'message' => 'No custom domain configured for this hospital.',
            ];
        }

        $domain = $this->normalizeDomain($hospital->custom_domain);
        $expectedCname = config('app.cname_target', env('CUSTOM_DOMAIN_CNAME_TARGET', 'domains.medislot.com'));

        // If in test environment or configured simulation
        if (app()->environment('testing', 'local') || env('SIMULATE_DOMAIN_VERIFICATION', false)) {
            $hospital->update([
                'custom_domain_status' => 'active',
                'custom_domain_verified_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => "Custom domain [{$domain}] verified successfully (local/testing verification).",
            ];
        }

        // Live DNS CNAME lookup
        $dnsRecords = @dns_get_record($domain, DNS_CNAME);

        if ($dnsRecords && is_array($dnsRecords)) {
            foreach ($dnsRecords as $record) {
                if (isset($record['target']) && strtolower(rtrim($record['target'], '.')) === strtolower(rtrim($expectedCname, '.'))) {
                    $hospital->update([
                        'custom_domain_status' => 'active',
                        'custom_domain_verified_at' => now(),
                    ]);

                    return [
                        'success' => true,
                        'message' => "DNS CNAME verified successfully! {$domain} is now active and routing to MediSlot.",
                    ];
                }
            }
        }

        $hospital->update([
            'custom_domain_status' => 'failed',
        ]);

        return [
            'success' => false,
            'message' => "DNS verification failed. Please ensure a CNAME record for '{$domain}' points to '{$expectedCname}'. DNS changes may take a few minutes to propagate.",
        ];
    }
}
