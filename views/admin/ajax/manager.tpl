<input type="hidden" id="mt_hashtoken" value="{$hashtoken}">
<input type="hidden" id="mt_module" value="{$module}">
<input type="hidden" id="mt_id_module" value="{$id_module}">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 text-gray-800"><i class="bi bi-card-list me-2"></i>Textes supplémentaires</h5>
    <button type="button" class="btn btn-primary btn-sm" onclick="mtApp.addItem()">
        <i class="bi bi-plus-lg me-1"></i> Ajouter un texte
    </button>
</div>

{include file="components/ajax-table.tpl"
data=$multitext_items
id_key="id_textmulti"
columns=$ajax_columns
sortable=true
edit_action="mtApp.editItem"
delete_action="mtApp.deleteItem"
empty_msg="Aucun texte supplémentaire n'a été créé pour cet élément."}