<?php
declare(strict_types=1);

namespace Plugins\MagixMultiText;

use App\Component\Hook\HookManager;
use Magepattern\Component\Tool\SmartyTool;
use Plugins\MagixMultiText\src\FrontendController;

class Boot
{
    private array $targetModules = [
        'product'  => 'id_product',
        'pages'    => 'id_pages',
        'category' => 'id_cat',
        'news'     => 'id_news',
        'about'    => 'id_about'
    ];

    public function register(): void
    {
        // --- BACKEND (Identique à ton code actuel) ---
        foreach ($this->targetModules as $module => $idKey) {
            HookManager::register("{$module}_edit_tab", 'MagixMultiText', function(array $params) use ($module) {
                $smarty = SmartyTool::getInstance('admin');
                $smarty->assign('multitext_module', $module);
                $file = ROOT_DIR . 'plugins' . DS . 'MagixMultiText' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_button.tpl';
                return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
            });

            HookManager::register("{$module}_edit_content", 'MagixMultiText', function(array $params) use ($module, $idKey) {
                $smarty = SmartyTool::getInstance('admin');
                $idModule = $params[$idKey] ?? 0;
                $smarty->assign(['multitext_module' => $module, 'multitext_id_module' => $idModule]);
                $file = ROOT_DIR . 'plugins' . DS . 'MagixMultiText' . DS . 'views' . DS . 'admin' . DS . 'hooks' . DS . 'tab_content.tpl';
                return $smarty->templateExists($file) ? $smarty->fetch($file) : '';
            });
        }

        // ==========================================
        // 2. HOOKS FRONTEND (Côté public)
        // ==========================================
        HookManager::register('displayPageBottom', 'MagixMultiText', function(array $params) {
            return FrontendController::renderWidget($params, 'pages', 'id_pages');
        });

        HookManager::register('displayProductExtraContent', 'MagixMultiText', function(array $params) {
            return FrontendController::renderWidget($params, 'product', 'id_product');
        });

        HookManager::register('displayCategoryBottom', 'MagixMultiText', function(array $params) {
            return FrontendController::renderWidget($params, 'category', 'id_cat');
        });

        HookManager::register('displayNewsBottom', 'MagixMultiText', function(array $params) {
            return FrontendController::renderWidget($params, 'news', 'id_news');
        });
        /*
         * Exemple si hooh statique positionnable manuellement
         *
         * <div class="product-extras">
            {hook name="mon_hook_texte"}
            {hook name="mon_hook_faq"}
        </div>

        // MagixMultiText
        HookManager::register('mon_hook_texte', 'MagixMultiText', ...);

        // MagixFaqMulti
        HookManager::register('mon_hook_faq', 'MagixFaqMulti', ...);*/
    }
}