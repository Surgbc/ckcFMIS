<form action="index.php" method = 'get'>
	<fieldset class = 'reags_fieldset'>
		<div id="uman"></div>
		<br/>
		<br/>
		<table>
			<tr>
				<td>
						<select id = ""class = 'reags_field viewreceiptyr'>
							<option>2014</option>
							<option>2015</option>
							<option>2016</option>
							<option>2017</option>
							<option>2018</option>
							<option>2019</option>
							<option>2020</option>
						</select>
				</td>
				<td>
						<select id = ""class = 'reags_field viewreceiptmt'>
							<option>Jan</option>
							<option>Feb</option>
							<option>Mar</option>
							<option>Apr</option>
							<option>May</option>
							<option>Jun</option>
							<option>Jul</option>
							<option>Aug</option>
							<option>Sep</option>
							<option>Oct</option>
							<option>Nov</option>
							<option>Dec</option>
						</select>
				</td>
				<td>
						<input id = ""class = 'reags_field viewreceiptdt' type = 'text'placeholder = 'Date'  />
						<input id = ""class = 'reags_field viewreceiptstart' type = 'text'placeholder = 'Receipt number'  />
						<input class = 'viewreceiptbtn' type = 'button' name = 'submit' value = 'Go' />
						<input class = 'viewreceiptback' type = 'button' value = '<<' />
						<input class = 'viewreceiptfoward' type = 'button'value = '>>' />
				</td>
			</tr>
			<td>
				<input class = 'viewreceiptdelete' type = 'button'value = 'Delete Receipt' />
				<input class = 'viewreceiptupdate' type = 'button'value = 'Upload Receipts' />
			</tr>
		</table>
	</fieldset>																	
</form>
<div id="viewreceiptspanel"></div>
<script type="text/javascript" src="ui/carl/scripts/receipts/receipts.js"></script>