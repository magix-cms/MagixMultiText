<div class="tab-pane fade" id="magix-multitext-pane" role="tabpanel" aria-labelledby="magix-multitext-tab" tabindex="0">
    <div class="card shadow-sm border-0 mt-3">
        <div class="card-body">

            {* ==========================================
               VUE 1 : LA ZONE AJAX (Pour la liste uniquement)
               ========================================== *}
            <div id="magix-multitext-app" data-module="{$multitext_module}" data-id="{$multitext_id_module}">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p>Chargement des textes...</p>
                </div>
            </div>

            {* ==========================================
               VUE 2 : LE FORMULAIRE STATIQUE MULTILINGUE
               ========================================== *}
            <div id="mt_view_form" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h5 class="mb-0 text-primary" id="mt_form_title">
                        <i class="bi bi-pencil-square me-2"></i>Ajouter un texte
                    </h5>
                    <div>
                        {* NOUVEAU : Le sélecteur de langue avec son préfixe *}
                        {if isset($langs)}{include file="components/dropdown-lang.tpl" prefix="mt_"}{/if}
                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="mtApp.showList()">
                            <i class="bi bi-arrow-left"></i> Retour à la liste
                        </button>
                    </div>
                </div>

                {* NOUVEAU : Le formulaire qui englobe tout *}
                <form id="mt_form_element">
                    <input type="hidden" id="mt_id_textmulti" name="id_textmulti" value="0">

                    <div class="tab-content">
                        {if isset($langs)}
                            {foreach $langs as $idLang => $iso}
                                <div class="tab-pane fade {if $iso@first}show active{/if}" id="mt_lang-{$idLang}" role="tabpanel">

                                    <div class="bg-light p-4 rounded border mb-4">
                                        <div class="row g-3">
                                            <div class="col-md-9">
                                                <label class="form-label fw-medium">Titre</label>
                                                <input type="text" class="form-control" name="title_textmulti[{$idLang}]">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-medium">Statut d'affichage</label>
                                                <div class="form-check form-switch fs-5 mt-1">
                                                    <input type="hidden" name="published_textmulti[{$idLang}]" value="0">
                                                    <input class="form-check-input" type="checkbox" name="published_textmulti[{$idLang}]" value="1" checked>
                                                    <label class="form-check-label fs-6 text-muted">En ligne</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-medium">Contenu complet :</label>
                                        <textarea class="form-control mceEditor"
                                                  name="desc_textmulti[{$idLang}]"
                                                  id="mt_desc_{$idLang}"
                                                  rows="10"></textarea>
                                    </div>

                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </form>

                <hr class="my-4">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2 px-4" onclick="mtApp.showList()">Annuler</button>
                    <button type="button" class="btn btn-success px-5" onclick="mtApp.save()">
                        <i class="bi bi-save me-2"></i> Enregistrer
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{block name="javascripts" append}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de l'App AJAX
            if (typeof MagixAjaxManager !== 'undefined') {
                window.mtApp = new MagixAjaxManager(
                    'magix-multitext-app',
                    'magix-multitext-tab',
                    'MagixMultiText',
                    'mt',
                    'textmulti'
                );
            }
        });
    </script>
{/block}