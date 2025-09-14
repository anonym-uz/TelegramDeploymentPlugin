<?php

namespace App\Vito\Plugins\AnonymUz\TelegramDeploymentPlugin\Actions;

use App\DTOs\DynamicField;
use App\DTOs\DynamicForm;
use App\Models\Site;
use App\SiteFeatures\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnableEnhancedNotifications extends Action
{
    public function __construct(public Site $site) {}

    public function name(): string
    {
        return 'Enable Enhanced Notifications';
    }

    public function active(): bool
    {
        return false;
    }

    public function form(): ?DynamicForm
    {
        return DynamicForm::make([
            DynamicField::make('info_alert')
                ->alert()
                ->options(['type' => 'info'])
                ->description('Configure enhanced Telegram notifications with detailed deployment information.'),
            DynamicField::make('include_commit_info')
                ->checkbox()
                ->label('Include Commit Information')
                ->description('Show commit hash, message, and author in notifications')
                ->default(true),
            DynamicField::make('include_duration')
                ->checkbox()
                ->label('Include Deployment Duration')
                ->description('Show how long the deployment took')
                ->default(true),
            DynamicField::make('include_server_info')
                ->checkbox()
                ->label('Include Server Information')
                ->description('Show server name and IP address')
                ->default(false),
            DynamicField::make('include_log_snippet')
                ->checkbox()
                ->label('Include Log Snippet')
                ->description('Show last few lines of deployment log')
                ->default(false),
            DynamicField::make('custom_success_emoji')
                ->text()
                ->label('Success Emoji')
                ->description('Emoji to use for successful deployments')
                ->default('✅'),
            DynamicField::make('custom_failure_emoji')
                ->text()
                ->label('Failure Emoji')
                ->description('Emoji to use for failed deployments')
                ->default('❌'),
        ]);
    }

    public function handle(Request $request): void
    {
        Validator::make($request->all(), [
            'include_commit_info' => 'boolean',
            'include_duration' => 'boolean',
            'include_server_info' => 'boolean',
            'include_log_snippet' => 'boolean',
            'custom_success_emoji' => 'nullable|string|max:10',
            'custom_failure_emoji' => 'nullable|string|max:10',
        ])->validate();

        // Store the configuration in the site's type_data
        $typeData = $this->site->type_data ?? [];
        $typeData['enhanced_telegram_notifications'] = [
            'enabled' => true,
            'include_commit_info' => $request->input('include_commit_info', true),
            'include_duration' => $request->input('include_duration', true),
            'include_server_info' => $request->input('include_server_info', false),
            'include_log_snippet' => $request->input('include_log_snippet', false),
            'custom_success_emoji' => $request->input('custom_success_emoji', '✅'),
            'custom_failure_emoji' => $request->input('custom_failure_emoji', '❌'),
        ];

        $this->site->type_data = $typeData;
        $this->site->save();

        $request->session()->flash('success', 'Enhanced Telegram notifications enabled for this site');
        $request->session()->flash('info', 'Notifications will include additional deployment details based on your configuration');
    }
}