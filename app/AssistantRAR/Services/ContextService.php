<?php

namespace App\AssistantRAR\Services;

use App\AssistantRAR\Contracts\IContextService;
use App\AssistantRAR\Contracts\IConversationService;
use App\AssistantRAR\Contracts\IMemoryService;
use App\AssistantRAR\Contracts\IToolRegistry;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ContextService implements IContextService
{
    public function __construct(
        private readonly IMemoryService $memory,
        private readonly IConversationService $conversation,
        private readonly IToolRegistry $registry,
    ) {}

    public function build(int $userId, ?string $currentRoute = null, ?int $resourceId = null, ?int $conversationId = null): array
    {
        $user = User::findOrFail($userId);

        $context = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_staff' => $user->isStaff(),
                'is_admin' => $user->isAdmin(),
                'is_trabajador' => $user->isTrabajador(),
                'active' => $user->active,
                'loyalty_points' => $user->loyalty_points,
            ],
            'permissions' => $this->getPermissionsForRole($user->role),
            'currentRoute' => $currentRoute,
            'resourceId' => $resourceId,
            'company' => $this->getCompanyInfo(),
            'locale' => config('app.locale'),
            'timestamp' => now()->toIso8601String(),
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i:s'),
            'timezone' => config('app.timezone'),
            'conversation_id' => $conversationId,
        ];

        if ($conversationId) {
            $context['history'] = array_map(fn ($m) => [
                'role' => $m['role'],
                'content' => $m['content'],
                'created_at' => $m['created_at'],
            ], $this->conversation->getHistory($conversationId, 20));
        }

        $context['memory'] = $this->memory->getAll($userId);

        $context['available_tools'] = $this->getAvailableTools($userId);

        return $context;
    }

    private function getPermissionsForRole(string $role): array
    {
        return match ($role) {
            'admin' => [
                'can_manage_products' => true,
                'can_manage_categories' => true,
                'can_manage_brands' => true,
                'can_manage_orders' => true,
                'can_manage_users' => true,
                'can_manage_settings' => true,
                'can_manage_banners' => true,
                'can_manage_coupons' => true,
                'can_manage_reviews' => true,
                'can_manage_newsletter' => true,
                'can_manage_reports' => true,
                'can_manage_inventory' => true,
                'can_manage_faq' => true,
                'can_manage_benefits' => true,
                'can_manage_loyalty' => true,
                'can_manage_promotions' => true,
                'can_manage_support' => true,
                'can_view_dashboard' => true,
                'can_export_data' => true,
            ],
            'trabajador' => [
                'can_manage_products' => true,
                'can_manage_categories' => true,
                'can_manage_brands' => true,
                'can_manage_orders' => true,
                'can_manage_users' => false,
                'can_manage_settings' => false,
                'can_manage_banners' => true,
                'can_manage_coupons' => false,
                'can_manage_reviews' => true,
                'can_manage_newsletter' => false,
                'can_manage_reports' => true,
                'can_manage_inventory' => true,
                'can_manage_faq' => true,
                'can_manage_benefits' => true,
                'can_manage_loyalty' => false,
                'can_manage_promotions' => false,
                'can_manage_support' => true,
                'can_view_dashboard' => true,
                'can_export_data' => true,
            ],
            default => [
                'can_manage_products' => false,
                'can_manage_categories' => false,
                'can_manage_brands' => false,
                'can_manage_orders' => false,
                'can_manage_users' => false,
                'can_manage_settings' => false,
                'can_manage_banners' => false,
                'can_manage_coupons' => false,
                'can_manage_reviews' => false,
                'can_manage_newsletter' => false,
                'can_manage_reports' => false,
                'can_manage_inventory' => false,
                'can_manage_faq' => false,
                'can_manage_benefits' => false,
                'can_manage_loyalty' => false,
                'can_manage_promotions' => false,
                'can_manage_support' => false,
                'can_view_dashboard' => false,
                'can_export_data' => false,
            ],
        };
    }

    private function getCompanyInfo(): array
    {
        return Cache::remember('assistant_company_info', 3600, function () {
            return [
                'name' => Setting::getValue('store_name', 'Negocios RaR'),
                'slogan' => Setting::getValue('store_slogan', 'Tu tienda online de confianza'),
                'email' => Setting::getValue('store_email', 'ventas@negociosrar.com'),
                'phone' => Setting::getValue('store_phone', '(01) 555-0100'),
                'address' => Setting::getValue('store_address', 'Lima, Perú'),
                'currency' => config('app.currency', 'PEN'),
                'currency_symbol' => 'S/',
            ];
        });
    }

    public function getAvailableTools(int $userId): array
    {
        $user = User::findOrFail($userId);
        return $this->registry->getForRole($user->role);
    }
}
