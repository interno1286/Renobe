{$data = ormModel::getInstance('public','translators')->getRow("id=`$params.id`")}
<label>Дневной (кол-во символов)</label>
<input type="text" class="form-control" placeholder="100000" id="day_limit" value="{$data.day_limit}"/>


<label>Месячный (кол-во символов)</label>
<input type="text" class="form-control" placeholder="100000" id="month_limit" value="{$data.month_limit}" />