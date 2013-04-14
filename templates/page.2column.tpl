{extends file='page.tpl'}
{block name='content'}
  <aside id="left_sb">
    {block name='left_column'}{/block}
  </aside>
  <div id="container" style="width: 904px;">
    <h1>{block name='page_title'}</h1>
    {block name='center_column'}{/block}
  </div>
{/block}