<div class="awebl-webcam-view">
	<h1>{{$data.nick}}</h1>

	{{* player section begin *}}
	<div>
		<div id="chatPlayer" class="player" style="position: relative; height: 600px"></div>
		<script>
			window.__liveApiChat__ = {
				onRequestClose: function() {
					$('#chatPlayer').remove();
				},
				onRequestGetCredits: function() {
					{{if $smarty.session.user_id>0 && $smarty.session.awebl_user_status.purchaseUrl}}
						window.location = '?mode=async&action=init_payment';
					{{else}}
						$('#login').click();
					{{/if}}
				},
				onRequestLoginOrSignup: function() {
					{{if $smarty.session.user_id>0 && $smarty.session.awebl_user_status.purchaseUrl}}
						window.location = '?mode=async&action=init_payment';
					{{else}}
						$('#login').click();
					{{/if}}
				},
				onRequestPaymentRedirect: function(url) {
					if (url) {
						window.location = url;
					}
				},
				onSwitchToPerformer: function(nick) {

				}
			};
		</script>
		<script src="{{$data.chatScriptUrl|replace:"[--CONTAINER_ID--]":"chatPlayer"|smarty:nodefaults}}"></script>
	</div>
	{{* player section end *}}

	{{* info section begin *}}
	<h2>Webcam info</h2>
	<div>
		<p>
			<label>Webcam ID</label>
			<span>{{$data.id}}</span>
		</p>
		<p>
			<label>Status</label>
			<span>{{if $data.status==0}}Offline{{elseif $data.status==1}}Online{{elseif $data.status==2}}Private{{/if}}</span>
		</p>
		<p>
			<label>BIO</label>
			<span>{{$data.bio}}</span>
		</p>
		<p>
			<label>Rating</label>
			<span>{{$data.averageRating}}</span>
		</p>
		<p>
			<label>Ethnicity</label>
			<span>{{$data.ethnicity}}</span>
		</p>
		<p>
			<label>Region</label>
			<span>{{$data.region}}</span>
		</p>
		<p>
			<label>Age</label>
			<span>{{$data.personAge}}</span>
		</p>
		<p>
			<label>Price</label>
			<span>{{$data.price}}</span>
		</p>
		<p>
			<label>Sexual Preferences</label>
			<span>{{$data.sexualPreferences}}</span>
		</p>
		{{if count($data.persons)>0}}
			<p>
				<label>Breast Size</label>
				<span>{{$data.persons[0].breastSize}}</span>
			</p>
			<p>
				<label>Build</label>
				<span>{{$data.persons[0].build}}</span>
			</p>
			<p>
				<label>Eye Color</label>
				<span>{{$data.persons[0].eyeColor}}</span>
			</p>
			<p>
				<label>Hair Color</label>
				<span>{{$data.persons[0].hairColor}}</span>
			</p>
			<p>
				<label>Hair Length</label>
				<span>{{$data.persons[0].hairLength}}</span>
			</p>
			{{if $data.persons[0].penisSize!='N/A'}}
				<p>
					<label>Penis Size</label>
					<span>{{$data.persons[0].penisSize}}</span>
				</p>
			{{/if}}
		{{/if}}
		<p>
			<label>Has story</label>
			<span>{{if $data.hasStory==1}}Yes{{else}}No{{/if}}</span>
		</p>
		<p>
			<label>Has VIP show</label>
			<span>{{if $data.hasVipShow==1}}Yes{{else}}No{{/if}}</span>
		</p>
		<p>
			<label>Video calls allowed</label>
			<span>{{if $data.isVideoCallEnabled==1}}Yes{{else}}No{{/if}}</span>
		</p>
		<p>
			<label>Vibrator active</label>
			<span>{{if $data.isVibratorActive==1}}Yes{{else}}No{{/if}}</span>
		</p>
		<p>
			<label>Profile picture 285x160</label>
			<span>{{$data.profilePictures.285x160}}</span>
		</p>
		<p>
			<label>Profile picture 358x201</label>
			<span>{{$data.profilePictures.358x201}}</span>
		</p>
		<p>
			<label>Profile picture 445x250</label>
			<span>{{$data.profilePictures.445x250}}</span>
		</p>
		<p>
			<label>Profile picture 224x168</label>
			<span>{{$data.profilePictures.224x168}}</span>
		</p>
		<p>
			<label>Profile picture 460x345</label>
			<span>{{$data.profilePictures.460x345}}</span>
		</p>
		<p>
			<label>Profile picture 1024x768</label>
			<span>{{$data.profilePictures.1024x768}}</span>
		</p>
		{{if count($data.appearance)>0}}
			<p>
				<label>Appearance</label>
				<span>
					{{foreach name="data" item="item" from=$data.appearance}}
						{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
		{{/if}}
		{{if count($data.language)>0}}
			<p>
				<label>Languages</label>
				<span>
					{{foreach name="data" item="item" from=$data.language}}
						{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
		{{/if}}
		{{if count($data.willingness)>0}}
			<p>
				<label>Willingness</label>
				<span>
					{{foreach name="data" item="item" from=$data.willingness}}
						{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
		{{/if}}
		{{if count($data.specialLocation)>0}}
			<p>
				<label>Special locations</label>
				<span>
					{{foreach name="data" item="item" from=$data.specialLocation}}
						{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
		{{/if}}
	</div>
	{{* info section end *}}
</div>