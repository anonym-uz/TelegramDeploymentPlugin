<?php

namespace App\Vito\Plugins\AnonymUz\TelegramDeploymentPlugin\Actions;

use App\DTOs\DynamicField;
use App\DTOs\DynamicForm;
use App\Models\Site;
use App\SiteFeatures\Action;
use Illuminate\Http\Request;

class DisableEnhancedNotifications extends Action
{
    public function __construct(public Site $site) {}

    public function name(): string
    {
        return 'Disable Enhanced Notifications';
    }

    public function active(): bool
    {
        return false;
    }

    public function form(): ?DynamicForm
    {
        return DynamicForm::make([
            DynamicField::make('warning_alert')
                ->alert()
                ->options(['type' => 'warning'])
                ->description('This will disable enhanced Telegram notifications and revert to standard notifications.'),
        ]);
    }

    public function handle(Request $request): void
    {
        // Update the site's type_data to disable enhanced notifications
        $typeData = $this->site->type_data ?? [];

        if (isset($typeData['enhanced_telegram_notifications'])) {
            $typeData['enhanced_telegram_notifications']['enabled'] = false;
        }

        $this->site->type_data = $typeData;
        $this->site->save();

        $request->session()->flash('success', 'Enhanced Telegram notifications disabled for this site');
        $request->session()->flash('info', 'Standard notifications will be used for future deployments');
    }
}