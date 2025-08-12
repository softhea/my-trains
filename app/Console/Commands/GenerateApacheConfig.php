<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateApacheConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apache:config {domain=my-trains.local} {--port=80}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Apache virtual host configuration for Laravel project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        $port = $this->option('port');
        $projectPath = base_path();
        $publicPath = public_path();

        $config = $this->generateVirtualHostConfig($domain, $port, $projectPath, $publicPath);
        
        // Save to a file in the project root
        $configFile = base_path("apache-{$domain}.conf");
        file_put_contents($configFile, $config);
        
        $this->info("Apache virtual host configuration generated successfully!");
        $this->line("Configuration saved to: {$configFile}");
        $this->line("");
        $this->warn("Next steps:");
        $this->line("1. Copy this file to your Laragon Apache sites directory");
        $this->line("2. Add '{$domain}' to your hosts file (127.0.0.1 {$domain})");
        $this->line("3. Restart Apache in Laragon");
        $this->line("4. Access your site at http://{$domain}");
        
        return 0;
    }

    private function generateVirtualHostConfig($domain, $port, $projectPath, $publicPath)
    {
        return <<<EOL
<VirtualHost *:{$port}>
    ServerName {$domain}
    DocumentRoot "{$publicPath}"
    
    <Directory "{$publicPath}">
        AllowOverride All
        Require all granted
        DirectoryIndex index.php
    </Directory>
    
    <Directory "{$projectPath}">
        AllowOverride None
        Require all denied
    </Directory>
    
    # Enable rewrite module for Laravel
    RewriteEngine On
    
    # Handle Laravel's pretty URLs
    <Directory "{$publicPath}">
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
    
    # Security headers
    Header always set X-Frame-Options DENY
    Header always set X-Content-Type-Options nosniff
    
    # Logging
    ErrorLog "logs/{$domain}-error.log"
    CustomLog "logs/{$domain}-access.log" combined
    
    # PHP configuration for Laravel
    <IfModule mod_php.c>
        php_value upload_max_filesize 8M
        php_value post_max_size 10M
        php_value memory_limit 128M
        php_value max_execution_time 300
        php_value max_input_time 300
        php_value date.timezone "UTC"
    </IfModule>
</VirtualHost>

# Additional configuration for SSL (optional)
# <VirtualHost *:443>
#     ServerName {$domain}
#     DocumentRoot "{$publicPath}"
#     
#     SSLEngine on
#     SSLCertificateFile "path/to/your/certificate.crt"
#     SSLCertificateKeyFile "path/to/your/private.key"
#     
#     <Directory "{$publicPath}">
#         AllowOverride All
#         Require all granted
#         DirectoryIndex index.php
#     </Directory>
# </VirtualHost>
EOL;
    }
}
