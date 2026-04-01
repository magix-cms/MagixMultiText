<?php
declare(strict_types=1);

namespace Plugins\MagixMultiText;

use App\Component\Hook\HookManager;
use Magepattern\Component\Tool\SmartyTool;

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
        // ==========================================
        // 1. HOOKS BACKEND (Administration)
        // ==========================================
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
    }
}