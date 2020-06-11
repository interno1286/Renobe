<h4>Interkassa поступления</h4>
<br/><br/>
{foreach $incomes.data as $income}
    <div class="income_item">
        <h4>{$income.coAmount} {$income.currencyCodeChar}</h4>
        <div class="float-right">created {$income.created|date_format:'%d.%m.%Y %H:%M'}</div>
        <br/>
        <div class="float-right">processed {$income.processed|date_format:'%d.%m.%Y %H:%M'}</div>
        <br/>
    </div>
    <hr>
{/foreach}
