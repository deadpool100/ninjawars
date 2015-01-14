
<!-- message tabs css section in the main css file -->

<section class='message-tabs' id='tabs'>

	<ul>
		<li class='{if $current == 'status'}current{/if} first'>
			<a href='events.php'>Status</a>
		</li>
		<li class='{if $current == 'messages'}current{/if}'>
			<a href='messages.php'>Messages</a>
		</li>
		{if $has_clan}
		<li class='{if $current == 'clan'}current{/if}'>
			<a href='messages.php?type=1'>Clan-Chat</a>
		</li>
		{/if}
	</ul>

</section>
