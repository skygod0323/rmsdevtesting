{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
{{if in_array('users|view',$smarty.session.permissions)}}
	<h1 data-children="users_main" class="lm_collapse">{{$lang.users.submenu_group_community}}</h1>
	<ul id="users_main">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='users.php'}}
			<li><span>{{$lang.users.submenu_option_users_list}}</span></li>
		{{else}}
			<li><a href="users.php">{{$lang.users.submenu_option_users_list}}</a></li>
		{{/if}}

		{{if in_array('users|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='users.php'}}
				<li><span>{{$lang.users.submenu_option_add_user}}</span></li>
			{{else}}
				<li><a href="users.php?action=add_new">{{$lang.users.submenu_option_add_user}}</a></li>
			{{/if}}
		{{/if}}
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='comments.php'}}
			<li><span>{{$lang.users.submenu_option_comments_list}}</span></li>
		{{else}}
			<li><a href="comments.php">{{$lang.users.submenu_option_comments_list}}</a></li>
		{{/if}}
		{{if $config.installation_type==4}}
			{{if $smarty.get.action!='change' && $page_name=='users_blogs.php'}}
				<li><span>{{$lang.users.submenu_option_blog_entries_list}}</span></li>
			{{else}}
				<li><a href="users_blogs.php">{{$lang.users.submenu_option_blog_entries_list}}</a></li>
			{{/if}}
		{{/if}}
		{{if in_array('users|emailings',$smarty.session.permissions)}}
			{{if $page_name=='emailing.php'}}
				<li><span>{{$lang.users.submenu_option_create_emailing}}</span></li>
			{{else}}
				<li><a href="emailing.php">{{$lang.users.submenu_option_create_emailing}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}

{{if in_array('feedbacks|view',$smarty.session.permissions)}}
	<h1 data-children="users_feedback" class="lm_collapse">{{$lang.users.submenu_group_feedback}}</h1>
	<ul id="users_feedback">
		{{if $smarty.get.action!='change' && $page_name=='feedbacks.php'}}
			<li><span>{{$lang.users.submenu_option_feedbacks}}</span></li>
		{{else}}
			<li><a href="feedbacks.php">{{$lang.users.submenu_option_feedbacks}}</a></li>
		{{/if}}
		{{if $smarty.get.action!='change' && $page_name=='flags_messages.php'}}
			<li><span>{{$lang.users.submenu_option_flags_messages}}</span></li>
		{{else}}
			<li><a href="flags_messages.php">{{$lang.users.submenu_option_flags_messages}}</a></li>
		{{/if}}
	</ul>
{{/if}}

{{if in_array('messages|view',$smarty.session.permissions)}}
	<h1 data-children="users_messages" class="lm_collapse">{{$lang.users.submenu_group_messages}}</h1>
	<ul id="users_messages">
		{{if in_array('messages|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='messages.php'}}
				<li><span>{{$lang.users.submenu_option_messages_list}}</span></li>
			{{else}}
				<li><a href="messages.php">{{$lang.users.submenu_option_messages_list}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('messages|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='messages.php'}}
				<li><span>{{$lang.users.submenu_option_add_message}}</span></li>
			{{else}}
				<li><a href="messages.php?action=add_new">{{$lang.users.submenu_option_add_message}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('playlists|view',$smarty.session.permissions)}}
	<h1 data-children="users_playlists" class="lm_collapse">{{$lang.users.submenu_group_playlists}}</h1>
	<ul id="users_playlists">
		{{if in_array('playlists|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='playlists.php'}}
				<li><span>{{$lang.users.submenu_option_playlists_list}}</span></li>
			{{else}}
				<li><a href="playlists.php">{{$lang.users.submenu_option_playlists_list}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('playlists|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='playlists.php'}}
				<li><span>{{$lang.users.submenu_option_add_playlist}}</span></li>
			{{else}}
				<li><a href="playlists.php?action=add_new">{{$lang.users.submenu_option_add_playlist}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('billing|view',$smarty.session.permissions)}}
	<h1 data-children="users_billing" class="lm_collapse">{{$lang.users.submenu_group_paid_access}}</h1>
	<ul id="users_billing">
		{{if $smarty.get.action!='change' && $smarty.get.action!='change_package' && $page_name=='card_bill_configurations.php'}}
			<li><span>{{$lang.users.submenu_option_card_billing}}</span></li>
		{{else}}
			<li><a href="card_bill_configurations.php">{{$lang.users.submenu_option_card_billing}}</a></li>
		{{/if}}

		{{query_kvs table="`$config.tables_prefix`sms_bill_providers" select="count" assign="sms_billings"}}
		{{if $sms_billings>0}}
			{{if $smarty.get.action!='change' && $smarty.get.action!='change_package' && $page_name=='sms_bill_configurations.php'}}
				<li><span>{{$lang.users.submenu_option_sms_billing}}</span></li>
			{{else}}
				<li><a href="sms_bill_configurations.php">{{$lang.users.submenu_option_sms_billing}}</a></li>
			{{/if}}
		{{/if}}

		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='bill_transactions.php'}}
			<li><span>{{$lang.users.submenu_option_billing_transactions}}</span></li>
		{{else}}
			<li><a href="bill_transactions.php">{{$lang.users.submenu_option_billing_transactions}}</a></li>
		{{/if}}

		{{if in_array('billing|edit_all',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='bill_transactions.php'}}
				<li><span>{{$lang.users.submenu_option_add_billing_transaction}}</span></li>
			{{else}}
				<li><a href="bill_transactions.php?action=add_new">{{$lang.users.submenu_option_add_billing_transaction}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('payouts|view',$smarty.session.permissions)}}
	<h1 data-children="users_payouts" class="lm_collapse">{{$lang.users.submenu_group_payouts}}</h1>
	<ul id="users_payouts">
		{{if $smarty.get.action!='change' && $smarty.get.action!='add_new' && $page_name=='payouts.php'}}
			<li><span>{{$lang.users.submenu_option_payouts_list}}</span></li>
		{{else}}
			<li><a href="payouts.php">{{$lang.users.submenu_option_payouts_list}}</a></li>
		{{/if}}

		{{if in_array('payouts|edit_all',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='payouts.php'}}
				<li><span>{{$lang.users.submenu_option_add_payout}}</span></li>
			{{else}}
				<li><a href="payouts.php?action=add_new">{{$lang.users.submenu_option_add_payout}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
</div>