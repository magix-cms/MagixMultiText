{if isset($magix_multitext_data) && $magix_multitext_data.items|count > 0}
    <div class="magix-multitext-widget mt-5 mb-5" data-module="{$magix_multitext_data.module}">

        <div class="row">
            <div class="col-12">
                {foreach $magix_multitext_data.items as $textBlock}
                    <div class="multitext-block mb-4 p-4 bg-body-tertiary rounded shadow-sm">

                        {if !empty($textBlock.title_textmulti)}
                            <h3 class="multitext-title h4 mb-3 fw-bold text-dark">
                                {$textBlock.title_textmulti}
                            </h3>
                        {/if}

                        {if !empty($textBlock.desc_textmulti)}
                            <div class="multitext-content text-muted">
                                {* Ne pas utiliser strip_tags si vous avez autorisé TinyMCE *}
                                {$textBlock.desc_textmulti nofilter}
                            </div>
                        {/if}

                    </div>
                {/foreach}
            </div>
        </div>

    </div>
{/if}