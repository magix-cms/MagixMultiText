<?php
declare(strict_types=1);

namespace Plugins\MagixMultiText\db;

use App\Frontend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class MultiTextFrontDb extends BaseDb
{
    /**
     * Récupère les textes publiés pour un module et un ID précis
     *
     * @param string $module Le nom du module (ex: 'pages', 'product')
     * @param int $idModule L'ID de l'élément (ex: 4 pour la page 4)
     * @param int $idLang L'ID de la langue courante
     * @return array La liste des textes formatés
     */
    public function getPublishedTexts(string $module, int $idModule, int $idLang): array
    {
        $cache = $this->getSqlCache();
        $qb = new QueryBuilder();

        $qb->select([
            't.id_textmulti',
            'tc.title_textmulti',
            'tc.desc_textmulti'
        ])
            ->from('mc_plug_textmulti', 't')
            ->join('mc_plug_textmulti_content', 'tc', 't.id_textmulti = tc.id_textmulti')
            ->where('t.module_textmulti = :module', ['module' => $module])
            ->where('t.id_module = :id_module', ['id_module' => $idModule])
            ->where('tc.id_lang = :id_lang', ['id_lang' => $idLang])
            ->where('tc.published_textmulti = 1') //  Sécurité : Uniquement les textes publiés
            ->orderBy('t.order_textmulti', 'ASC'); // Tri respectant le Drag&Drop

        // Génération de la clé avec un TAG spécifique au plugin
        $cacheKey = $cache->generateKey($qb->getSql(), $qb->getParams(), 'magixmultitext');

        // Vérification du cache
        $data = $cache->get($cacheKey);
        if ($data !== null) {
            return $data;
        }

        // Si non trouvé, on exécute
        $results = $this->executeAll($qb) ?: [];

        // Mise en cache pour 24h
        $cache->set($cacheKey, $results, 86400);

        return $results;
    }
}