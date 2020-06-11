{$volumes = ormModel::getInstance('chaptersModel')->getChaptersForAdmin($params.id)}

<div id="volumes_data{$params.id}">
    {use file="volumes_data.tpl"}
</div>


