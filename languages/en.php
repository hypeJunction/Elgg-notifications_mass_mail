<?php

return [
	'notifications:mass_mail:add' => 'Send message',
	'notifications:mass_mail:recipient_count' => 'Message will be sent to %s recipients',
	'notifications:mass_mail:dynamic_fields' => 'Subject and Message fields accept the following dynamic properties:
		{{recipient.name}} - Recipient name
		{{recipient.getURL}} - Recipient URL
		{{recipient.email}} - Recipient email

		{{sender.name}} - Sender name
		{{sender.getURL}} - Sender URL
		{{sender.email}} - Sender email
		
		{{site.name}} - Site name
		{{site.getURL}} - Site URL
		
		For group messages:
		{{target.name}} - Group name
		{{target.getURL}} - Group profile URL
		{{target.description}} - Group description

		Entity metadata can be accessed for each entity as follows {{recipient.first_name}}
	',
	'notifications:mass_mail:subject' => 'Subject',
	'notifications:mass_mail:message' => 'Message',
	'notifications:mass_mail:method' => 'Delivery method',
	'notifications:mass_mail:method:_preferred' => 'Recipient\'s preference',

	'notifications:mass_mail:send' => 'Send',
	'notifications:mass_mail:resend' => 'Resend',

	'notifications:mass_mail:missing_field' => 'Required field is missing',

	'notifications:mass_mail:groups_mass_mail' => 'Enable mass mailing for group admins',
	'notifications:mass_mail' => 'Mass mail',
	'notifications:mass_mail:groups' => 'Message members',

	'notifications:mass_mail:send:success' => 'Message has been queued and will be send out shortly',
	'notifications:mass_mail:send:error' => 'Message could not be queued',
	
];