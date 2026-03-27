<?php
declare(strict_types=1);

namespace Plugins\MagixMultiText\db;

use App\Backend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class MultiTextAdminDb extends BaseDb
{
    /**
     * Récupère tous les textes d'un module et ID spécifique pour la liste (dans une langue donnée)
     */
    public function getTextsByModule(string $module, int $idModule, int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select([
            't.id_textmulti',
            't.module_textmulti',
            't.id_module',
            't.order_textmulti',
            'tc.title_textmulti',
            'tc.desc_textmulti',
            'tc.published_textmulti'
        ])
            ->from('mc_plug_textmulti', 't')
            ->join('mc_plug_textmulti_content', 'tc', 't.id_textmulti = tc.id_textmulti')
            ->where('t.module_textmulti = :module AND t.id_module = :id_module AND tc.id_lang = :id_lang', [
                'module'    => $module,
                'id_module' => $idModule,
                'id_lang'   => $idLang
            ])
            ->orderBy('t.order_textmulti', 'ASC');

        return $this->executeAll($qb) ?: [];
    }

    /**
     * 🟢 NOUVEAU : Récupère un texte spécifique avec TOUTES ses traductions (pour le formulaire d'édition)
     */
    public function getTextById(int $idText): array|false
    {
        // 1. Structure
        $qb = new QueryBuilder();
        $qb->select('*')->from('mc_plug_textmulti')->where('id_textmulti = :id', ['id' => $idText]);
        $text = $this->executeRow($qb);

        if (!$text) return false;

        // 2. Traductions
        $qbContent = new QueryBuilder();
        $qbContent->select('*')->from('mc_plug_textmulti_content')->where('id_textmulti = :id', ['id' => $idText]);
        $contents = $this->executeAll($qbContent);

        $text['content'] = [];
        if ($contents) {
            foreach ($contents as $c) {
                $text['content'][$c['id_lang']] = $c;
            }
        }

        return $text;
    }

    /**
     * 🟢 NOUVEAU : Insère uniquement la structure de base et retourne l'ID
     */
    public function insertTextStructure(array $data): int|false
    {
        // On calcule automatiquement le prochain 'order_textmulti'
        $qbCount = new QueryBuilder();
        $qbCount->select(['COUNT(id_textmulti) as total'])
            ->from('mc_plug_textmulti')
            ->where('module_textmulti = :module AND id_module = :id', [
                'module' => $data['module_textmulti'],
                'id'     => $data['id_module']
            ]);

        $countResult = $this->executeRow($qbCount);
        $data['order_textmulti'] = (int)($countResult['total'] ?? 0);

        $qb = new QueryBuilder();
        $qb->insert('mc_plug_textmulti', $data);

        return $this->executeInsert($qb) ? (int)$this->getLastInsertId() : false;
    }

    /**
     * 🟢 NOUVEAU : Sauvegarde ou met à jour le contenu traduit d'un texte
     */
    public function saveTextContent(int $idText, int $idLang, array $data): bool
    {
        $qbCheck = new QueryBuilder();
        $qbCheck->select(['id_textmulti'])->from('mc_plug_textmulti_content')
            ->where('id_textmulti = :text AND id_lang = :lang', ['text' => $idText, 'lang' => $idLang]);

        $exists = $this->executeRow($qbCheck);
        $qb = new QueryBuilder();

        if ($exists) {
            $qb->update('mc_plug_textmulti_content', $data)
                ->where('id_textmulti = :text AND id_lang = :lang', ['text' => $idText, 'lang' => $idLang]);
            return $this->executeUpdate($qb);
        } else {
            $data['id_textmulti'] = $idText;
            $data['id_lang']      = $idLang;
            $qb->insert('mc_plug_textmulti_content', $data);
            return $this->executeInsert($qb);
        }
    }

    /**
     * Supprime un texte et son contenu (double requête sécurisée)
     */
    public function deleteText(int $idText): bool
    {
        // Suppression du contenu d'abord pour éviter les erreurs de clés étrangères
        $qb2 = new QueryBuilder();
        $qb2->delete('mc_plug_textmulti_content')->where('id_textmulti = :id', ['id' => $idText]);
        $res2 = $this->executeDelete($qb2);

        // Suppression de la structure
        $qb1 = new QueryBuilder();
        $qb1->delete('mc_plug_textmulti')->where('id_textmulti = :id', ['id' => $idText]);
        $res1 = $this->executeDelete($qb1);

        return $res1 && $res2;
    }

    /**
     * Met à jour l'ordre des textes (Drag & Drop)
     */
    public function updateOrder(array $orderedIds): bool
    {
        $success = true;
        foreach ($orderedIds as $index => $id) {
            $qb = new QueryBuilder();
            // L'index commence à 0, on peut ajouter +1 si vous préférez que l'ordre commence à 1
            $qb->update('mc_plug_textmulti', ['order_textmulti' => $index + 1])
                ->where('id_textmulti = :id', ['id' => (int)$id]);

            if (!$this->executeUpdate($qb)) {
                $success = false;
            }
        }
        return $success;
    }
}