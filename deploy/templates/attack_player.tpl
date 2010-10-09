<h1>Village</h1>

<div id='attack-player-page'>

    <h3>Locations</h3>
    <ul style='margin: .5em auto;text-align:center;font-size:1.3em;'>
{foreach name="looploc" from=$locations item="loc" key="idx"}
      <li style='padding-left:8px'>
      	<a href='{$loc.url|escape}'>
	{if isset($loc.tile_image)}
	    <img src='/images/{$loc.tile_image}' alt='' style='max-width:100px;max-height:100px'>
	{/if}
	{if isset($loc.image)}
          <img src='/images/{$loc.image|escape:'url'|escape}' alt='' style='width:8px;height:8px'>
	{/if}
          {$loc.name|escape}
      	</a>
      </li>
{/foreach}
    </ul>
  
  <hr>
  
  <h3>Attack a citizen:</h3>
  <ul id='npc-list' style='margin: .5em auto;text-align:center;font-size:1.3em;'>
{foreach name="person" from=$npcs key="idx" item="npc"}
      <li><a href='{$npc.url|escape}' target='main'><img alt='' src='images/characters/{$npc.image|escape:'url'|escape}' style='width:25px;height:46px'> {$npc.name|escape}</a></li>
{/foreach}
  </ul>
      
  <hr>

<!--
  <p>
    To attack a ninja, use the <a href="list_all_players.php?hide=dead" target='main'>player list</a> or search for a ninja below.
  </p>

  <form id="player_search" action="list_all_players.php" method="get" name="player_search">
    <div>
      Search by Ninja Name or Rank
      <input id="searched" type="text" maxlength="50" name="searched" class="textField">
      <input id="hide" type="hidden" name="hide" value="dead">
      <button type="submit" value="Search for Ninja" class="formButton">Search for Ninja</button>
    </div>
  </form>

-->


{if $show_ad eq 3}
<!-- This particular ad is here mainly to focus the targeting of the advertising to more nw related topics. -->

    <!-- Google Ad -->
    <script type="text/javascript"><!--
    google_ad_client = "pub-9488510237149880";
    /* 300x250, created 12/17/09 */
    google_ad_slot = "9563671390";
    google_ad_width = 300;
    google_ad_height = 250;
    //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
    </script>
{/if}

</div><!-- End of attack-player page container div -->
