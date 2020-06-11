{$data=ormModel::init('paragraphModel')->getFailedParInfo($params.id)}

<h2>{$data.novella_name|default:$data.novella_original_name}</h2>

Том: {$data.volume_name}<br />
Глава: <a target="_blank" href="/chapter/{tools_string::translit($data.chapter_name)}/{$data.chapter_id}">{$data.chapter_name}</a>
<br />

<label>Текст параграфа</label>
<textarea class="form-control" style="height: 100px;">{$data.text_original|default:$data.text_en}</textarea>


<label>Перевод</label>
<textarea class="form-control" style="height: 100px;" id="translate"></textarea>