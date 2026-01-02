<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:generate-vapid-keys {--show : Display keys instead of updating .env}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate VAPID keys for web push notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating VAPID keys for web push notifications...');

        try {
            $keys = VAPID::createVapidKeys();

            if ($this->option('show')) {
                $this->info('VAPID Keys Generated:');
                $this->line('');
                $this->info('Public Key: ' . $keys['publicKey']);
                $this->info('Private Key: ' . $keys['privateKey']);
                $this->line('');
                $this->comment('Add these to your .env file:');
                $this->comment('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
                $this->comment('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
            } else {
                $envPath = base_path('.env');

                if (!file_exists($envPath)) {
                    $this->error('.env file not found. Please create it first.');
                    return 1;
                }

                $envContent = file_get_contents($envPath);

                // Update or add VAPID keys
                $envContent = $this->updateEnvValue($envContent, 'VAPID_PUBLIC_KEY', $keys['publicKey']);
                $envContent = $this->updateEnvValue($envContent, 'VAPID_PRIVATE_KEY', $keys['privateKey']);

                file_put_contents($envPath, $envContent);

                $this->info('✅ VAPID keys generated and added to .env file!');
                $this->comment('Restart your application for changes to take effect.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to generate VAPID keys: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Update or add an environment variable in the .env content.
     */
    private function updateEnvValue(string $envContent, string $key, string $value): string
    {
        $pattern = "/^{$key}=.*$/m";

        if (preg_match($pattern, $envContent)) {
            // Key exists, update it
            $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
        } else {
            // Key doesn't exist, add it
            $envContent .= "\n{$key}={$value}";
        }

        return $envContent;
    }
}
