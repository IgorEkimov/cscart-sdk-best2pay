<div class="control-group">
	<label for="sector_id" class="control-label cm-required">Sector ID:</label>
	<div class="controls">
		<input type="text" name="payment_data[processor_params][sector_id]" id="sector_id" value="{$processor_params.sector_id}" class="input-text"  size="60" />
		<p class="muted description">{__("best2pay.sector_description")}</p>
	</div>
</div>
<div class="control-group">
	<label for="password" class="control-label cm-required">{__("Password")}:</label>
	<div class="controls">
		<input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text"  size="60" />
		<p class="muted description">{__("best2pay.password_description")}</p>
	</div>
</div>
<div class="control-group">
	<label for="payment_type" class="control-label">{__("best2pay.payment_method")}:</label>
	<div class="controls">
		<select name="payment_data[processor_params][payment_type]" id="payment_type" style="width: auto">
			<option value="purchase" {if $processor_params.payment_type == "purchase"}selected="selected"{/if}>{__("best2pay.one_stage")}</option>
			<option value="authorize" {if $processor_params.payment_type == "authorize"}selected="selected"{/if}>{__("best2pay.two_steps")} *</option>
			<option value="purchaseWithInstallment" {if $processor_params.payment_type == "purchaseWithInstallment"}selected="selected"{/if}>{__("best2pay.halva")}</option>
			<option value="authorizeWithInstallment" {if $processor_params.payment_type == "authorizeWithInstallment"}selected="selected"{/if}>{__("best2pay.halva_two_steps")} *</option>
			<option value="purchaseSBP" {if $processor_params.payment_type == "purchaseSBP"}selected="selected"{/if}>{__("best2pay.sbp")}</option>
			<option value="loan" {if $processor_params.payment_type == "loan"}selected="selected"{/if}>{__("best2pay.loan")}</option>
		</select>
		<p class="muted description">* {__("best2pay.two_steps_description")}</p>
	</div>
</div>
<div class="control-group">
	<label for="modal_payform" class="control-label">{__("best2pay.modal_payform")}:</label>
	<div class="controls">
		<select name="payment_data[processor_params][modal_payform]" id="modal_payform">
			<option value="0" {if $processor_params.modal_payform == "0"}selected="selected"{/if}>{__("No")}</option>
			<option value="1" {if $processor_params.modal_payform == "1"}selected="selected"{/if}>{__("Yes")}</option>
		</select>
		<p class="muted description">{__("best2pay.modal_payform_description")}</p>
	</div>
</div>
<div class="control-group">
	<label for="test_mode" class="control-label">{__("best2pay.test_mode")}:</label>
	<div class="controls">
		<select name="payment_data[processor_params][test_mode]" id="test_mode">
			<option value="1" {if $processor_params.test_mode == "1"}selected="selected"{/if}>{__("Yes")}</option>
			<option value="0" {if $processor_params.test_mode == "0"}selected="selected"{/if}>{__("No")}</option>
		</select>
		<p class="muted description">{__("best2pay.test_mode_description")}</p>
	</div>
</div>
<div class="control-group">
	<label for="tax" class="control-label">{__("best2pay.tax")}:</label>
	<div class="controls">
		<select name="payment_data[processor_params][tax]" id="tax">
			<option value="1" {if $processor_params.tax == "1"}selected="selected"{/if}>{__("best2pay.vat1")}</option>
			<option value="2" {if $processor_params.tax == "2"}selected="selected"{/if}>{__("best2pay.vat2")}</option>
			<option value="3" {if $processor_params.tax == "3"}selected="selected"{/if}>{__("best2pay.vat3")}</option>
			<option value="4" {if $processor_params.tax == "4"}selected="selected"{/if}>{__("best2pay.vat4")}</option>
			<option value="5" {if $processor_params.tax == "5"}selected="selected"{/if}>{__("best2pay.vat5")}</option>
			<option value="6" {if !$processor_params.tax || $processor_params.tax == "6"}selected="selected"{/if}>{__("best2pay.vat6")}</option>
		</select>
		<p class="muted description"></p>
	</div>
</div>
<div class="control-group">
	<label for="hash_algo" class="control-label">{__("best2pay.hash_algo")}:</label>
	<div class="controls">
		<select name="payment_data[processor_params][hash_algo]" id="hash_algo">
			<option value="0" {if $processor_params.hash_algo == "0"}selected="selected"{/if}>MD5</option>
			<option value="1" {if $processor_params.hash_algo == "1"}selected="selected"{/if}>SHA256</option>
		</select>
		<p class="muted description"></p>
	</div>
</div>

{$order_statuses=$smarty.const.STATUSES_ORDER|fn_get_statuses:$statuses:$get_additional_statuses:true}

{include file="common/subheader.tpl" title="{__("best2pay.custom_orders_statuses_title")}" target="#custom_orders_statuses"}

<div id="custom_orders_statuses">
	<div class="control-group">
		<label for="order_completed" class="control-label">{__("best2pay.order_completed")}:</label>
		<div class="controls">
			<select name="payment_data[processor_params][order_completed]" id="order_completed">
				<option value="">--</option>
					{foreach from=$order_statuses key=key item=status}
						<option value="{$key}" {if $processor_params.order_completed == $key}selected="selected"{/if}>{$status.description}</option>
					{/foreach}
			</select>
			<p class="muted description"></p>
		</div>
	</div>
	<div class="control-group">
		<label for="order_authorized" class="control-label">{__("best2pay.order_authorized")}:</label>
		<div class="controls">
			<select name="payment_data[processor_params][order_authorized]" id="order_authorized">
				<option value="">--</option>
					{foreach from=$order_statuses key=key item=status}
						<option value="{$key}" {if $processor_params.order_authorized == $key}selected="selected"{/if}>{$status.description}</option>
					{/foreach}
			</select>
			<p class="muted description"></p>
		</div>
	</div>
	<div class="control-group">
		<label for="order_canceled" class="control-label">{__("best2pay.order_canceled")}:</label>
		<div class="controls">
			<select name="payment_data[processor_params][order_canceled]" id="order_canceled">
				<option value="">--</option>
					{foreach from=$order_statuses key=key item=status}
						<option value="{$key}" {if $processor_params.order_canceled == $key}selected="selected"{/if}>{$status.description}</option>
					{/foreach}
			</select>
			<p class="muted description"></p>
			<p class="muted description"></p>
		</div>
	</div>
	<div class="control-group">
		<label for="order_loan" class="control-label">{__("best2pay.order_loan")}:</label>
		<div class="controls">
			<select name="payment_data[processor_params][order_loan]" id="order_loan">
				<option value="">--</option>
				{foreach from=$order_statuses key=key item=status}
					<option value="{$key}" {if $processor_params.order_loan == $key}selected="selected"{/if}>{$status.description}</option>
				{/foreach}
			</select>
			<p class="muted description"></p>
		</div>
	</div>
</div>

<div class="form-field">
	<label for="notify_url">{__("best2pay.notify_url")}:</label>
	<input style="width: 500px" type="text" id="notify_url" value="{$config.current_location}/index.php?dispatch=payment_notification.notify&payment=best2pay" class="input-text" readonly="readonly"/>
	<p class="muted description">{__("best2pay.notify_url_description")}</p>
</div>