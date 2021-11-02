{{* variables supported to control this list *}}
{{assign var="list_tokens_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="list_tokens_title" value="Token transactions"}} {{* title *}}

<div id="{{$block_uid}}" class="list-members-tokens">
	<h{{$list_tokens_header_level|default:"2"}}>{{$list_tokens_title|default:"Token transactions"}}</h{{$list_tokens_header_level|default:"2"}}>
	{{if count($data)>0}}
		<table class="items" width="100%">
			<tr>
				<th>Action</th>
				<th>Tokens</th>
				<th>Info</th>
				<th>Date</th>
			</tr>
			{{foreach item="item" from=$data}}
				<tr>
					<td>
						{{if $item.flow_type=='purchase_video'}}
							Video purchased
						{{elseif $item.flow_type=='purchase_album'}}
							Album purchased
						{{elseif $item.flow_type=='purchase_dvd'}}
							Channel subscription purchased
						{{elseif $item.flow_type=='purchase_user'}}
							Member subscription purchased
						{{elseif $item.flow_type=='purchase_access_package'}}
							Premium access purchased
						{{elseif $item.flow_type=='purchase_tokens'}}
							Tokens purchased
						{{elseif $item.flow_type=='award_signup'}}
							Signup
						{{elseif $item.flow_type=='award_login'}}
							Memberzone login
						{{elseif $item.flow_type=='award_avatar'}}
							Avatar uploaded
						{{elseif $item.flow_type=='award_cover'}}
							Cover uploaded
						{{elseif $item.flow_type=='award_comment'}}
							Comment posted
						{{elseif $item.flow_type=='award_video_upload'}}
							Video uploaded
						{{elseif $item.flow_type=='award_album_upload'}}
							Album created
						{{elseif $item.flow_type=='award_post_upload'}}
							Post created
						{{elseif $item.flow_type=='award_video_sale'}}
							Video sold
						{{elseif $item.flow_type=='award_album_sale'}}
							Album sold
						{{elseif $item.flow_type=='award_referral'}}
							Referral revenue
						{{elseif $item.flow_type=='award_donation'}}
							Donation from user
						{{elseif $item.flow_type=='payout'}}
							Payout
						{{elseif $item.flow_type=='donation'}}
							Donation to user
						{{elseif $item.flow_type=='award_video_traffic'}}
							Video views
						{{elseif $item.flow_type=='award_album_traffic'}}
							Album views
						{{elseif $item.flow_type=='award_embed_traffic'}}
							Embed views
						{{elseif $item.flow_type=='award_user_sale'}}
							Subscription sold
						{{elseif $item.flow_type=='award_dvd_sale'}}
							Channel subscription sold
						{{/if}}
					</td>
					<td>{{$item.tokens}}</td>
					<td>
						{{if $item.object_info!=''}}
							{{$item.object_info}}
						{{elseif $item.object_id>0}}
							Object has been deleted
						{{/if}}
						{{if $item.notes!=''}}
							({{$item.notes}})
						{{/if}}
					</td>
					<td>{{$item.date|date_format:"%Y-%m-%d %H:%M:%S"}}</td>
				</tr>
			{{/foreach}}
		</table>

		{{* include pagination here *}}
	{{else}}
		<div class="text">
			There is no data in this list.
		</div>
	{{/if}}
</div>