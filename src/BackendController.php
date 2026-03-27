<?php
declare(strict_types=1);

namespace Plugins\MagixMultiText\src;

use App\Backend\Controller\BaseController;
use Plugins\MagixMultiText\db\MultiTextAdminDb;
use Magepattern\Component\HTTP\Request;
use Magepattern\Component\Tool\SmartyTool;
use Magepattern\Component\Tool\FormTool;

class BackendController extends BaseController
{
    public function run(): void
    {
        // 🟢 CORRECTION 1 : On donne un namespace unique au dossier pour éviter les conflits
        SmartyTool::addTemplateDir('multitext', ROOT_DIR . 'plugins' . DS . 'MagixMultiText' . DS . 'views' . DS . 'admin');

        $action = $_GET['action'] ?? null;

        if ($action && method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->jsonResponse(false, 'Action invalide.');
        }
    }

    public function loadList(): void
    {
        if (ob_get_length()) ob_clean();

        $module = $_GET['module'] ?? '';
        $idModule = (int)($_GET['id_module'] ?? 0);
        $idLang = (int)($this->defaultLang['id_lang'] ?? 1);

        if (empty($module) || $idModule === 0) {
            echo '<div class="alert alert-warning">Paramètres manquants pour charger les textes.</div>';
            return;
        }

        $db = new MultiTextAdminDb();
        $texts = $db->getTextsByModule($module, $idModule, $idLang);

        // 🟢 CORRECTION 2 : On injecte les traductions complètes pour le JS
        foreach ($texts as &$text) {
            $fullData = $db->getTextById((int)$text['id_textmulti']);
            $text['content'] = $fullData['content'] ?? [];
        }

        $columns = [
            'title_textmulti' => [
                'title' => 'Titre',
                'type'  => 'text',
                'class' => 'fw-bold text-dark'
            ],
            'published_textmulti' => [
                'title' => 'Statut',
                'type'  => 'status',
                'class' => 'text-center',
                'width' => '120px'
            ]
        ];

        $this->view->assign([
            'multitext_items' => $texts,
            'ajax_columns'    => $columns,
            'module'          => $module,
            'id_module'       => $idModule,
            'hashtoken'       => $this->session->getToken(),
            'langs'           => $db->fetchLanguages() // On passe les langues pour le dropdown
        ]);

        // 🟢 N'oubliez pas d'utiliser le nouveau préfixe ici !
        $this->view->display('ajax/manager.tpl');
    }

    public function save(): void
    {
        if (ob_get_length()) ob_clean();

        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée ou jeton invalide.');
        }

        $idText   = (int)($_POST['id_textmulti'] ?? 0);
        $itemType = FormTool::simpleClean($_POST['module_textmulti'] ?? '');
        $itemId   = (int)($_POST['id_module'] ?? 0);

        if (empty($itemType) || $itemId === 0) {
            $this->jsonResponse(false, 'Les références du module sont obligatoires.');
        }

        $db = new MultiTextAdminDb();

        try {
            // 🟢 CORRECTION 3 : Traitement séparé Structure / Contenu Multilingue
            if ($idText === 0) {
                // Remplacez 'insertTextStructure' par le nom exact de votre méthode DB
                $idText = $db->insertTextStructure([
                    'module_textmulti' => $itemType,
                    'id_module'        => $itemId
                ]);

                if (!$idText) {
                    $this->jsonResponse(false, 'Erreur lors de la création de la structure.');
                }
            }

            // Sauvegarde des traductions
            if (isset($_POST['title_textmulti']) && is_array($_POST['title_textmulti'])) {
                foreach ($_POST['title_textmulti'] as $idLang => $title) {
                    $cleanTitle = FormTool::simpleClean($title);

                    if (!empty($cleanTitle)) {
                        // Remplacez 'saveTextContent' par le nom exact de votre méthode DB
                        $db->saveTextContent($idText, (int)$idLang, [
                            'title_textmulti'     => $cleanTitle,
                            'desc_textmulti'      => $_POST['desc_textmulti'][$idLang] ?? '',
                            'published_textmulti' => isset($_POST['published_textmulti'][$idLang]) ? 1 : 0
                        ]);
                    }
                }
            }

            $this->jsonResponse(true, 'Texte enregistré avec succès.');

        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Erreur serveur : ' . $e->getMessage());
        }
    }

    public function delete(): void
    {
        if (ob_get_length()) ob_clean();

        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée.');
        }

        $idText = (int)($_POST['id_textmulti'] ?? 0);

        if ($idText > 0) {
            $db = new MultiTextAdminDb();
            if ($db->deleteText($idText)) {
                $this->jsonResponse(true, 'Texte supprimé avec succès.');
            }
        }
        $this->jsonResponse(false, 'Impossible de supprimer ce texte.');
    }

    public function reorder(): void
    {
        if (ob_get_length()) ob_clean();

        $token = Request::isPost('hashtoken') ? $_POST['hashtoken'] : '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée.');
        }

        $orderedIds = $_POST['ids'] ?? [];

        if (!empty($orderedIds) && is_array($orderedIds)) {
            $db = new MultiTextAdminDb();
            if ($db->updateOrder($orderedIds)) {
                $this->jsonResponse(true, 'Ordre mis à jour.');
            }
        }
        $this->jsonResponse(false, 'Erreur lors du tri.');
    }
}