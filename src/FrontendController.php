<?php
declare(strict_types=1);

namespace Plugins\MagixMultiText\src;

use Plugins\MagixMultiText\db\MultiTextFrontDb;
use Magepattern\Component\Tool\SmartyTool;
use App\Component\Db\PluginDb;

class FrontendController
{
    private static array $targetsCache = [];

    /**
     * Le routeur dynamique appelé par HookManager
     */
    public static function renderWidget(array $params = []): string
    {
        // 1. Initialisation du coupe-circuit
        if (empty(self::$targetsCache)) {
            $pluginDb = new PluginDb();
            self::$targetsCache = $pluginDb->getPluginTargets('MagixMultiText');
        }

        if (empty(self::$targetsCache)) return '';

        $hookName = $params['name'] ?? '';

        // 2. Vérifications conditionnelles par contexte
        if ($hookName === 'displayPageBottom') {
            if (empty(self::$targetsCache['pages'])) return '';
            return self::processRender($params, 'pages', 'id_pages');
        }
        if ($hookName === 'displayProductExtraContent') {
            if (empty(self::$targetsCache['product'])) return '';
            return self::processRender($params, 'product', 'id_product');
        }
        if ($hookName === 'displayCategoryBottom') {
            if (empty(self::$targetsCache['category'])) return '';
            return self::processRender($params, 'category', 'id_cat');
        }
        if ($hookName === 'displayNewsBottom') {
            if (empty(self::$targetsCache['news'])) return '';
            return self::processRender($params, 'news', 'id_news');
        }

        return '';
    }

    /**
     * Votre méthode métier (rendue privée)
     */
    private static function processRender(array $params, string $module, string $idKey): string
    {
        try {
            $view = SmartyTool::getInstance('front');

            $langData = $view->getTemplateVars('current_lang') ?: $view->getTemplateVars('lang') ?: ['id_lang' => 1];
            $idLang = (int)($langData['id_lang'] ?? 1);

            $id = (int)($params[$idKey] ?? 0);
            if ($id === 0) return '';

            $db = new MultiTextFrontDb();
            $items = $db->getPublishedTexts($module, $id, $idLang);

            if (empty($items)) return '';

            $template = ROOT_DIR . 'plugins' . DS . 'MagixMultiText' . DS . 'views' . DS . 'front' . DS . 'widget.tpl';

            if (!file_exists($template)) return '';

            return $view->fetch($template, [
                'magix_multitext_data' => [
                    'module' => $module,
                    'items'  => $items
                ]
            ]);

        } catch (\Throwable $e) {
            return "";
        }
    }
}