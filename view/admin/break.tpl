{extends 'admin/' . $template}{*break*}

{block 'title'}Break{/block}
{block 'content'}
    {assign storage [1, 2, 3, 4, 5]}
    {foreach:state $storage as $item}
        {if $this->foreach->state->last}
            {$this->foreach->state->key}
            {break}
        {/if}
    {/foreach}
{/block}