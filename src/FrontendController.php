<?php
declare(strict_types=1);

namespace Plugins\MagixMultiText\src;

use Plugins\MagixMultiText\db\MultiTextFrontDb;
use Magepattern\Component\Tool\SmartyTool;

class FrontendController
{
    /**
     * Rendu dynamique et optimisé grâce aux paramètres passés par le Boot
     */
    public static function renderWidget(array $params, string $module, string $idKey): string
    {
        try {
            $view = SmartyTool::getInstance('front');

            // 1. Langue
            $langData = $view->getTemplateVars('current_lang') ?: $view->getTemplateVars('lang') ?: ['id_lang' => 1];
            $idLang = (int)($langData['id_lang'] ?? 1);

            // 2. Détection dynamique de l'ID via la clé passée par le Hook
            $id = (int)($params[$idKey] ?? 0);

            if ($id === 0) return '';

            // 3. Récupération DB
            $db = new MultiTextFrontDb();
            $items = $db->getPublishedTexts($module, $id, $idLang);

            if (empty($items)) return '';

            // 4. Affichage
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