<div class="member-profile-view">
	<h1>{{$data.title}}</h1>

	{{* info section begin *}}
	<h2>Member info</h2>
	<div>
		<p>
			<label>Member ID</label>
			<span>{{$data.user_id}}</span>
		</p>
		<p>
			<label>Display name</label>
			<span>{{$data.display_name}}</span>
		</p>
		<p>
			<label>Status message</label>
			<span>{{$data.status_message}}</span>
		</p>
		<p>
			<label>Country</label>
			<span>{{$data.country}}</span>
		</p>
		<p>
			<label>City</label>
			<span>{{$data.city}}</span>
		</p>
		<p>
			<label>Gender</label>
			<span>{{if $data.gender_id==1}}Male{{elseif $data.gender_id==2}}Female{{elseif $data.gender_id==3}}Couple{{elseif $data.gender_id==4}}Transsexual{{/if}}</span>
		</p>
		<p>
			<label>Relationship status</label>
			<span>{{if $data.relationship_status_id==1}}Single{{elseif $data.relationship_status_id==2}}Married{{elseif $data.relationship_status_id==3}}Open{{elseif $data.relationship_status_id==4}}Divorced{{elseif $data.relationship_status_id==5}}Widowed{{/if}}</span>
		</p>
		<p>
			<label>Sexual orientation</label>
			<span>{{if $data.orientation_id==1}}Not sure{{elseif $data.orientation_id==2}}Straight{{elseif $data.orientation_id==3}}Gay{{elseif $data.orientation_id==4}}Lesbian{{elseif $data.orientation_id==5}}Bisexual{{/if}}</span>
		</p>
		<p>
			<label>Website</label>
			<span>{{$data.website}}</span>
		</p>
		<p>
			<label>Education</label>
			<span>{{$data.education}}</span>
		</p>
		<p>
			<label>Occupation</label>
			<span>{{$data.occupation}}</span>
		</p>
		<p>
			<label>About me</label>
			<span>{{$data.about_me}}</span>
		</p>
		<p>
			<label>Interests</label>
			<span>{{$data.interests}}</span>
		</p>
		<p>
			<label>Favourite movies</label>
			<span>{{$data.favourite_movies}}</span>
		</p>
		<p>
			<label>Favourite music</label>
			<span>{{$data.favourite_music}}</span>
		</p>
		<p>
			<label>Favourite books</label>
			<span>{{$data.favourite_books}}</span>
		</p>
		<p>
			<label>Birth date</label>
			<span>{{if $data.birth_date!='0000-00-00'}}{{$data.birth_date|date_format:"%d %B, %Y"}}{{/if}}</span>
		</p>
		<p>
			<label>Age</label>
			<span>{{if $data.age.value>0}}{{$data.age.value}}{{/if}}</span>
		</p>
		<p>
			<label>Registration</label>
			<span>{{$data.added_date|date_format:"%d %B, %Y"}}</span>
		</p>
		<p>
			<label>Last login</label>
			<span>{{if $data.last_login_date!='0000-00-00'}}{{$data.last_login_date|date_format:"%d %B, %Y"}}{{/if}}</span>
		</p>
		<p>
			<label>Avatar</label>
			<span>{{if $data.avatar_url}}{{$data.avatar_url}}{{/if}}</span>
		</p>
		<p>
			<label>Cover</label>
			<span>{{if $data.cover_url}}{{$data.cover_url}}{{/if}}</span>
		</p>
		<p>
			<label>Rank</label>
			<span>{{$data.activity_rank}} (was {{$data.activity_last_rank}})</span>
		</p>
		<p>
			<label>Stats</label>
			<span>{{$data.profile_viewed}} views, {{$data.video_viewed}} user videos views, {{$data.album_viewed}} user albums views, {{$data.subscribers_count}} subscribers, {{$data.friends_count}} friends</span>
		</p>
		<p>
			<label>Activity</label>
			<span>{{$data.video_watched}} videos watched, {{$data.album_watched}} albums viewed, {{$data.comments_total_count}} comments posted, {{$data.ratings_total_count}} ratings submitted, {{$data.logins_count}} times logged in</span>
		</p>
		<p>
			<label>Content</label>
			<span>
				{{$data.public_videos_count}} public videos, {{$data.private_videos_count}} private videos, {{$data.premium_videos_count}} premium videos, {{$data.total_videos_count}} total videos, {{$data.favourite_videos_count}} favourite videos,
				{{$data.public_albums_count}} public albums, {{$data.private_albums_count}} private albums, {{$data.premium_albums_count}} premium albums, {{$data.total_albums_count}} total albums, {{$data.favourite_albums_count}} favourite albums
			</span>
		</p>
		<p>
			<label>Custom 1</label>
			<span>{{$data.custom1}}</span>
		</p>
		{{if is_array($next_user)}}
			<p>
				<label>Next member</label>
				<span>
					{{if $next_user.user_id}}{{$next_user.display_name}}{{/if}}
				</span>
			</p>
		{{/if}}
		{{if is_array($previous_user)}}
			<p>
				<label>Previous member</label>
				<span>
					{{if $previous_user.user_id}}{{$previous_user.display_name}}{{/if}}
				</span>
			</p>
		{{/if}}
	</div>
	{{* info section end *}}

	{{* subscribe section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Subscription management</h2>
		{{if $data.is_subscribed==1}}
			<a href="#unsubscribe" data-id="{{$data.user_id}}" data-unsubscribe-to="user">Unsubscribe</a>
		{{else}}
			<a href="#subscribe" data-id="{{$data.user_id}}" data-subscribe-to="user">Subscribe</a>
		{{/if}}
	{{/if}}
	{{* subscribe section end *}}

	{{* message section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Conversation</h2>
		<form method="post" data-form="ajax">
			<div class="generic-error hidden"></div>

			<div class="row">
				<label for="send_message">Message text</label>
				<textarea id="send_message" name="message" rows="4"></textarea>
				<div class="field-error"></div>
			</div>

			<div class="buttons">
				<input type="hidden" name="action" value="send_message_complete"/>
				<input type="submit" class="submit" value="Send"/>
			</div>
		</form>
	{{/if}}
	{{* message section end *}}

	{{* friends section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Friendship management</h2>
		{{if $data.is_friend==1}}
			<form method="post" data-form="ajax" data-confirm="Please confirm deleting {{$data.display_name}} from your friends list.">
				<div class="generic-error hidden"></div>

				<div class="info-message">
					{{$data.display_name}} is in your friends list.
				</div>

				<div class="buttons">
					<input type="hidden" name="action" value="remove_from_friends"/>
					<input type="submit" class="submit" value="Delete"/>
				</div>
			</form>
		{{elseif $data.is_friend_invitation_sent==1}}
			<form method="post" data-form="ajax">
				<div class="info-message">
					Friend invitation has been sent to {{$data.display_name}}.
				</div>
			</form>
		{{elseif $data.is_friend_invitation_received==1}}
			<form method="post" data-form="ajax">
				<div class="generic-error hidden"></div>

				<div class="info-message">
					{{$data.display_name}} wants to be your friend.
				</div>

				<div class="buttons">
					<input type="hidden" name="action" value="confirm_add_to_friends"/>
					<input type="submit" class="submit" name="confirm" value="Confirm"/>
					<input type="submit" class="submit" name="reject" value="Deny"/>
				</div>
			</form>
		{{else}}
			<form method="post" data-form="ajax">
				<div class="generic-error hidden"></div>

				<div class="row">
					<label for="add_to_friends_message">Invite {{$data.display_name}} to be your friend</label>
					<textarea id="add_to_friends_message" name="message" rows="4"></textarea>
					<div class="field-error"></div>
				</div>

				<div class="buttons">
					<input type="hidden" name="action" value="add_to_friends_complete"/>
					<input type="submit" class="submit" value="Invite"/>
				</div>
			</form>
		{{/if}}
	{{/if}}
	{{* friends section end *}}

	{{* blacklist section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Blacklist management</h2>
		{{if $data.is_ignored==1}}
			<form method="post" data-form="ajax" data-confirm="Please confirm removing {{$data.display_name}} from your blacklist.">
				<div class="generic-error hidden"></div>

				<div class="info-message">
					{{$data.display_name}} is in your blacklist.
				</div>

				<div class="buttons">
					<input type="hidden" name="action" value="remove_from_ignores"/>
					<input type="submit" class="submit" value="Delete"/>
				</div>
			</form>
		{{else}}
			<form method="post" data-form="ajax" data-confirm="Please confirm adding {{$data.display_name}} to your blacklist.">
				<div class="generic-error hidden"></div>

				<div class="buttons">
					<input type="hidden" name="action" value="ignore_user"/>
					<input type="submit" class="submit" value="Add to blacklist"/>
				</div>
			</form>
		{{/if}}
	{{/if}}
	{{* blacklist section end *}}
</div>
