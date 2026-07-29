<?php

namespace App\AssistantRAR\Providers;

use App\AssistantRAR\Contracts\IAssistantService;
use App\AssistantRAR\Contracts\IConversationService;
use App\AssistantRAR\Contracts\IContextService;
use App\AssistantRAR\Contracts\IMemoryService;
use App\AssistantRAR\Contracts\IPromptBuilder;
use App\AssistantRAR\Contracts\IProviderManager;
use App\AssistantRAR\Contracts\IStreamingService;
use App\AssistantRAR\Contracts\IToolExecutor;
use App\AssistantRAR\Contracts\IToolRegistry;
use App\AssistantRAR\Services\AssistantService;
use App\AssistantRAR\Services\ConversationService;
use App\AssistantRAR\Services\ContextService;
use App\AssistantRAR\Services\MemoryService;
use App\AssistantRAR\Services\PromptBuilder;
use App\AssistantRAR\Services\ProviderManager;
use App\AssistantRAR\Services\StreamingService;
use App\AssistantRAR\Services\ToolExecutor;
use App\AssistantRAR\Services\ToolRegistry;
use Illuminate\Support\ServiceProvider;

class AssistantRARServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IToolRegistry::class, ToolRegistry::class);
        $this->app->singleton(IConversationService::class, ConversationService::class);
        $this->app->singleton(IContextService::class, ContextService::class);
        $this->app->singleton(IMemoryService::class, MemoryService::class);
        $this->app->singleton(IPromptBuilder::class, PromptBuilder::class);
        $this->app->singleton(IProviderManager::class, ProviderManager::class);
        $this->app->singleton(IToolExecutor::class, ToolExecutor::class);
        $this->app->singleton(IAssistantService::class, AssistantService::class);
        $this->app->singleton(IStreamingService::class, StreamingService::class);
    }

    public function boot(): void
    {
        $registry = $this->app->make(IToolRegistry::class);

        $tools = [
            \App\AssistantRAR\Tools\ProductTool::class,
            \App\AssistantRAR\Tools\ProductCreateTool::class,
            \App\AssistantRAR\Tools\ProductUpdateTool::class,
            \App\AssistantRAR\Tools\ProductDeleteTool::class,
            \App\AssistantRAR\Tools\ProductDuplicateTool::class,
            \App\AssistantRAR\Tools\ProductUpdatePriceTool::class,
            \App\AssistantRAR\Tools\ProductUpdateStockTool::class,
            \App\AssistantRAR\Tools\ProductChangeStatusTool::class,
            \App\AssistantRAR\Tools\CategorySearchTool::class,
            \App\AssistantRAR\Tools\CategoryCreateTool::class,
            \App\AssistantRAR\Tools\CategoryUpdateTool::class,
            \App\AssistantRAR\Tools\CategoryDeleteTool::class,
            \App\AssistantRAR\Tools\CategoryChangeStatusTool::class,
            \App\AssistantRAR\Tools\BrandSearchTool::class,
            \App\AssistantRAR\Tools\InventoryLowStockTool::class,
            \App\AssistantRAR\Tools\InventoryOutOfStockTool::class,
            \App\AssistantRAR\Tools\InventoryMovementsTool::class,
            \App\AssistantRAR\Tools\InventoryAdjustTool::class,
            \App\AssistantRAR\Tools\InventorySetMinimumStockTool::class,
            \App\AssistantRAR\Tools\OrderSearchTool::class,
            \App\AssistantRAR\Tools\OrderGetTool::class,
            \App\AssistantRAR\Tools\OrderUpdateStatusTool::class,
            \App\AssistantRAR\Tools\OrderTimelineTool::class,
            \App\AssistantRAR\Tools\UserSearchTool::class,
            \App\AssistantRAR\Tools\UserCreateWorkerTool::class,
            \App\AssistantRAR\Tools\UserUpdateTool::class,
            \App\AssistantRAR\Tools\UserChangeRoleTool::class,
            \App\AssistantRAR\Tools\UserBlockTool::class,
            \App\AssistantRAR\Tools\UserUnblockTool::class,
            \App\AssistantRAR\Tools\CartGetTool::class,
            \App\AssistantRAR\Tools\CartAddItemTool::class,
            \App\AssistantRAR\Tools\CartUpdateQuantityTool::class,
            \App\AssistantRAR\Tools\CartRemoveItemTool::class,
            \App\AssistantRAR\Tools\CartEstimateTotalsTool::class,
            \App\AssistantRAR\Tools\WishlistGetTool::class,
            \App\AssistantRAR\Tools\WishlistAddTool::class,
            \App\AssistantRAR\Tools\WishlistRemoveTool::class,
            \App\AssistantRAR\Tools\CouponSearchTool::class,
            \App\AssistantRAR\Tools\CouponCreateTool::class,
            \App\AssistantRAR\Tools\CouponUpdateTool::class,
            \App\AssistantRAR\Tools\CouponActivateTool::class,
            \App\AssistantRAR\Tools\CouponDeactivateTool::class,
            \App\AssistantRAR\Tools\CouponDeleteTool::class,
            \App\AssistantRAR\Tools\CouponValidateTool::class,
            \App\AssistantRAR\Tools\ReviewSearchTool::class,
            \App\AssistantRAR\Tools\ReviewApproveTool::class,
            \App\AssistantRAR\Tools\ReviewRejectTool::class,
            \App\AssistantRAR\Tools\ReviewDeleteTool::class,
            \App\AssistantRAR\Tools\ReviewSummaryTool::class,
            \App\AssistantRAR\Tools\BannerSearchTool::class,
            \App\AssistantRAR\Tools\BannerGetTool::class,
            \App\AssistantRAR\Tools\BannerCreateTool::class,
            \App\AssistantRAR\Tools\BannerUpdateTool::class,
            \App\AssistantRAR\Tools\BannerActivateTool::class,
            \App\AssistantRAR\Tools\BannerDeactivateTool::class,
            \App\AssistantRAR\Tools\BannerDeleteTool::class,
            \App\AssistantRAR\Tools\BannerReorderTool::class,
            \App\AssistantRAR\Tools\FaqSearchTool::class,
            \App\AssistantRAR\Tools\FaqCreateTool::class,
            \App\AssistantRAR\Tools\FaqUpdateTool::class,
            \App\AssistantRAR\Tools\FaqDeleteTool::class,
            \App\AssistantRAR\Tools\BenefitSearchTool::class,
            \App\AssistantRAR\Tools\BenefitCreateTool::class,
            \App\AssistantRAR\Tools\BenefitUpdateTool::class,
            \App\AssistantRAR\Tools\BenefitDeleteTool::class,
            \App\AssistantRAR\Tools\SupportConversationsTool::class,
            \App\AssistantRAR\Tools\SupportGetConversationTool::class,
            \App\AssistantRAR\Tools\SupportReplyTool::class,
            \App\AssistantRAR\Tools\SupportCloseTool::class,
            \App\AssistantRAR\Tools\SupportReopenTool::class,
            \App\AssistantRAR\Tools\NewsletterSubscribersTool::class,
            \App\AssistantRAR\Tools\NewsletterExportTool::class,
            \App\AssistantRAR\Tools\AddressListTool::class,
            \App\AssistantRAR\Tools\AddressCreateTool::class,
            \App\AssistantRAR\Tools\AddressUpdateTool::class,
            \App\AssistantRAR\Tools\AddressSetDefaultTool::class,
            \App\AssistantRAR\Tools\LoyaltyGetBalanceTool::class,
            \App\AssistantRAR\Tools\LoyaltyGetMovementsTool::class,
            \App\AssistantRAR\Tools\LoyaltyAdjustBalanceTool::class,
            \App\AssistantRAR\Tools\SettingGetPublicTool::class,
            \App\AssistantRAR\Tools\SettingGetAdminTool::class,
            \App\AssistantRAR\Tools\SettingUpdateTool::class,
            \App\AssistantRAR\Tools\VariantSearchTool::class,
            \App\AssistantRAR\Tools\VariantCreateTool::class,
            \App\AssistantRAR\Tools\VariantUpdateTool::class,
            \App\AssistantRAR\Tools\VariantUpdateStockTool::class,
            \App\AssistantRAR\Tools\VariantDeleteTool::class,
            \App\AssistantRAR\Tools\SystemTool::class,
            \App\AssistantRAR\Tools\ReportSalesTool::class,
            \App\AssistantRAR\Tools\ReportProductsTool::class,
            \App\AssistantRAR\Tools\ReportInventoryTool::class,
            \App\AssistantRAR\Tools\ReportCustomersTool::class,
            \App\AssistantRAR\Tools\ReportOrdersTool::class,
            \App\AssistantRAR\Tools\ReportExportCsvTool::class,
            \App\AssistantRAR\Tools\SystemTool::class,
        ];

        foreach ($tools as $toolClass) {
            $registry->register($toolClass);
        }
    }
}
