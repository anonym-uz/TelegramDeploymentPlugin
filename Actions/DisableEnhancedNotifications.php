<?php

namespace App\Vito\Plugins\AnonymUz\TelegramDeploymentPlugin\Actions;

use App\Models\Site;
use App\SiteFeatures\Action;

class DisableEnhancedNotifications extends Action
{
    public function __construct(public Site $site) {}

    public function name(): string
    {
        return 'disable-enhanced-telegram';
    }

    public function active(): bool
    {
        return false; // Actions don't have an active state
    }

    public function handle(array $input): void
    {
        $this->run($input);
    }

    public function run(array $input): void
    {
        // Update the site's type_data to disable enhanced notifications
        $typeData = $this->site->type_data ?? [];
        
        if (isset($typeData['enhanced_telegram_notifications'])) {
            $typeData['enhanced_telegram_notifications']['enabled'] = false;
        }
        
        $this->site->type_data = $typeData;
        $this->site->save();
        
        $this->addSuccessLog('Enhanced Telegram notifications disabled for site');
    }
}