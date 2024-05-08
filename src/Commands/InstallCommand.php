<?php

namespace Phpsa\FilamentHeadlessCms\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament-headless-cms:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install script for Filament Headless CMS.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('*********************************');
        $this->line('*     FILAMENT HEADLESS CMS     *');
        $this->line('*********************************');
        $this->newLine(2);
        $this->info('Thank you for choosing Filament Headless CMS!');
        $this->newLine();
        $this->info('Publishing assets...');
        $this->callSilent('vendor:publish', [
            '--tag' => 'filament-headless-cms-migrations',
        ]);
        $this->callSilent('vendor:publish', [
            '--tag' => 'tags-migrations',
        ]);

        if ($this->confirm('Do you want to run migrations now?', true)) {
            $this->call('migrate');
        }

        $this->newLine();
        if ($this->confirm('All done! Would you like to show some love by starring on GitHub?', true)) {
            if (PHP_OS_FAMILY === 'Darwin') {
                exec('open https://github.com/phpsa/filament-headless-cms');
            }
            if (PHP_OS_FAMILY === 'Linux') {
                exec('xdg-open https://github.com/phpsa/filament-headless-cms');
            }
            if (PHP_OS_FAMILY === 'Windows') {
                exec('start https://github.com/phpsa/filament-headless-cms');
            }

            $this->components->info('Thank you!');
        }

        return static::SUCCESS;
    }
}
