{if $enable_grades ==1 or $enable_comments==1}
<div id="block_comments">
	<div class="tabs">
		<h3 class="page-product-heading">{$title}</h3>
		{*Affichage des 3 derniers commentaires :*}

		{l s='Displaying 3 last comments' mod='jonitro'}

        {foreach from=$comments item=comment}
			<p>
				<strong>Commentaire #{$comment.id_jonitro_comment}:</strong>
                {$comment.comment}<br>
				<strong>Note:</strong> {$comment.grade}/5<br>
			</p><br>
        {/foreach}
	<br>
	<a href="{url entity='module' name='jonitro' controller='j' params = $params}">
		{l s='See all comments' mod='jonitro'}
	</a>

		<form method="post">

			{if $enable_grades==1}

			<div class="form-group">
				<label>{l s='Grade' mod='jonitro'} :</label>
				<select class="form-control" name="grade">
					<option>{l s='Choose a grade' mod='jonitro'}...</option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
				</select>
			</div>
			{/if}

			{if $enable_comments==1}
			<div class="form-group">
				<label>{l s='Comments' mod='jonitro'} :</label>
				<textarea class="form-control" name="comment"></textarea>
			</div>
			{/if}
			<input type="submit" value="{l s='Send' mod='jonitro'}" class="btn-primary" name="submit_form_customer">

		</form>
	</div>
</div>
{/if}
