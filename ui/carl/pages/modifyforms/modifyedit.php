<div id = "submenus">
			<form class = 'reags_form_login' method = 'get' action ='#'>
									
										<!-------------------- BEGIN ASSOCIATES PERSONAL DETAILS FIELDSET --------------------------->
										<fieldset class = 'reags_fieldset'>
											<legend class = 'reags_legend'>Edit Details for Other Users</legend>
											<div id="uman"></div>
											<br/>
											<br/>
											
											<!-------------------- BEGIN MEMBERS PERSONAL DETAILS FORMATTING TABLE --------------------------->
											<table>
												<tr>
													<td>
														<label for = 'Name'>Users:</label>
													</td>
													<td>
															<table id ="selectusers" class="reags_field">
															<!--<tr><td class = "selectuserstd" uid="iud1" dep="Deaconry">Brian</td></tr>
															-->
															</table>
													</td>
													<td>
														<ul>
														<li>Department</li>
														<li>
														<input id = "login_userdep"class = 'reags_field' type = 'text' name = 'login_userdep' placeholder = '' disabled="true" required/>
														</li>
														</ul>
													</td>
												</tr>												
											</table><!-------- END MEMBERS PERSONAL DETAILS FORMATTING TABLE ---------->
										</fieldset><!-------- END MEMBERS PERSONAL DETAILS FIELDSET ---------->
										
										<!-------------------- BEGIN MEMBERS INSTITUTIONAL DETAILS FIELDSET --------------------------->
										<fieldset class = 'reags_fieldset'>
											<input class = 'reags_submit_login modifyeditsubmitdelete' type = 'button' value = 'Delete' />
											<input class = 'reags_submit_login modifyeditsubmitpromote' type = 'button' value = 'Add Admin' />
											<input class = 'reags_submit_login modifyeditsubmitdemote' type = 'button' value = 'Remove Admin' />
											<input class = 'reags_submit_login' type = 'button' value = '&nbsp;' />
											<input class = 'reags_submit_login modifycancel' type = 'button' value = 'Cancel' />
											<input class = 'reags_submit_login ' type = 'button' value = '&nbsp;' />
										</fieldset>																				
									</form><!-------------------- END MEMBERS REGISTRATION FORM --------------------------------->
</div>
<script type="text/javascript" src="ui/carl/scripts/latemodifyscripts.js"></script>
<script type="text/javascript" src="ui/carl/scripts/latemodifyeditscripts.js"></script>