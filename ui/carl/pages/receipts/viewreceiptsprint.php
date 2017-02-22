<form action="index.php" method = 'get'>
	<fieldset class = 'reags_fieldset'>
		<div id="uman"></div>
		<br/>
		<br/>
		<table>
			<tr>
				<td>
						<select id = ""class = 'reags_field viewreceiptyr'>
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
						<input class = 'viewreceiptprintbtn' type = 'button' name = 'submit' value = 'Print' />
				</td>
			</tr>
		</table>
	</fieldset>																	
</form>
<div id="viewreceiptspanel"></div>
<script type="text/javascript" src="ui/carl/scripts/receipts/receipts.js"></script>