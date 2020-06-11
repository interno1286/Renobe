{$data=ormModel::getInstance('newsModel')->getEventData($params.id)}

<h1>{$data.header}</h1>

{$data.text}