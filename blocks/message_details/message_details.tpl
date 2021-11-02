<div class="message-details">
	<h1>Message from {{$data.user_from_name}}</h1>

	<p>
		<label>Sent date</label>
		<span>{{$data.added_date|date_format:"%d %B, %Y"}}</span>
	</p>
	<p>
		{{if $data.type_id==1}}
			{{$data.user_from_name}} wants to be your friend.
			{{if $data.message}}
				<blockquote>{{$data.message|replace:"\n":"<br/>"}}</blockquote>
			{{/if}}
		{{elseif $data.type_id==2}}
			{{$data.user_from_name}} has declined your invitation.
		{{elseif $data.type_id==3}}
			{{$data.user_from_name}} is not your friend any more.
		{{elseif $data.type_id==4}}
			{{$data.user_from_name}} has confirmed your invitation and is now your friend.
		{{else}}
			{{$data.message|replace:"\n":"<br/>"}}
		{{/if}}
	</p>

	{{* sender info section begin *}}
	<h2>Sender info</h2>
	<div>
		<p>
			<label>Sender ID</label>
			<span>{{$data.user_from_id}}</span>
		</p>
		<p>
			<label>Sender display name</label>
			<span>{{$data.user_from_name}}</span>
		</p>
		<p>
			<label>Sender avatar</label>
			<span>{{if $data.user_from_avatar_url}}{{$data.user_from_avatar_url}}{{/if}}</span>
		</p>
	</div>
	{{* sender info section end *}}

	{{* reply section begin *}}
	{{if $data.user_from_id!=$smarty.session.user_id}}
		<h2>Reply</h2>
		<form method="post" data-form="ajax">
			<div class="generic-error hidden"></div>

			<div class="row">
				<label for="message_details_message">Message text</label>
				<textarea id="message_details_message" name="message" rows="4"></textarea>
				<div class="field-error"></div>
			</div>

			<div class="buttons">
				<input type="hidden" name="action" value="send_message"/>
				<input type="hidden" name="reply_to_user_id" value="{{$data.user_from_id}}"/>
				<input type="submit" class="submit" value="Send"/>
			</div>
		</form>
	{{/if}}
	{{* reply section end *}}
</div>