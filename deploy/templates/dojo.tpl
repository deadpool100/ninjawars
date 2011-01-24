<h1>Dojo</h1>

<div class="description">
  <div style="margin-bottom: 10px;">
    You walk up the steps to the grandest building in the village. The dojo trains many respected ninja.
  </div>
  <div>
    As you approach, you can hear the sounds of fighting coming from the wooden doors in front of you.
  </div>
</div>

{if !is_logged_in()}
<p>The guards at the door block your way, saying "Stranger, go on your way, you haven't the skill to enter here."</p>
{else}

	{if !$dim_mak_requirement_error}
	<!-- DIMMAK OBTAINING EVENT REQUESTED -->
    {if $dimmak_sequence neq 2}
    A black-robed monk stands near the entrance to the dojo.

    	{if $dimmak_sequence neq 1} {* Link to start the Dim Mak sequence *}
    The black monk approaches you and offers to give you <a href="dojo.php?dimmak_sequence=1">power over life and death,</a> at the cost of some of your memories.
    	{else} {* Strips the link after it's been clicked. *}
    The black monk offers to give you power over life and death, at the cost of some of your memories.
    	{/if}
    <br>
    {/if}

    {if $dimmak_sequence eq 1}
    <form id="Buy_DimMak" action="dojo.php?dimmak_sequence=2" method="post" name="buy_dimmak">
      <div style='margin-top: 10px;margin-bottom: 10px;'>
        Trade your memories of {$dimMakCost|escape} kills for the DimMak Scroll?
        <input id="dimmak_sequence" type="hidden" value="2" name="obtainscroll">
        <input type="submit" value="Obtain Dim Mak" class="formButton">
      </div>
    </form>
    {elseif $dimmak_sequence eq 2}
    <p>The monk meditates for a moment, then passes his hand over your forehead. A black fog passes over your vision and you feel a moment of dizziness.</p>
    <p>For a moment you become aware of the dirt on the walls, the darkness in the room, a <a href='npc.php?victim=spider' class='npc'>Spider</a> crawling across the wall.</p>
    <p>He hands you a scroll that seems to writhe with shadows.</p>
    {/if}

    <hr>
    <!-- END OF DIMMAK OBTAINING EVENT DISPLAY -->
    
	{/if}
	{if !$class_change_requirement_error}
        {if $classChangeSequence neq 2}
        <!-- CLASS CHANGING SPECIAL EVENT DISPLAY -->
        A white-robed monk stands near the entrance to the dojo.

        	{if $classChangeSequence neq 1} {* Link to start the Class Change sequence *}
        <p>The white monk approaches you and offers to give you <a href="dojo.php?classChangeSequence=1">the knowledge of your enemies</a> at the cost of your own memories.</a></p>
        	{else} {* Strips the link after it's been clicked. *}
        <p>The white monk approaches you and offers to give you the knowledge of your enemies at the cost of your own memories.</p>
        	{/if}
        {/if}
        
        {if $class_change_error}
            <p class='ninja-error'>{$class_change_error}</p>
        {/if}

        {if $classChangeSequence eq 1}
          {foreach from=$classes item='class' key='identity'}
        <form id="Buy_classChange" action="dojo.php" method="post" name="changeofclass">
          <div style='margin-top: .3em;margin-bottom: .3em;'>
            Trade your memories of {$classChangeCost|escape} kills to change your skills to those of the <span class='class-name {$class.theme}'>{$class.class_name|escape}</span> ninja?
            <input id='classchangeSequence' name='classChangeSequence' type='hidden' value='2'>
            <input id='current_class' name='current_class' type='hidden' value='{$userClass|escape}'>
            <input id='requested_identity' name='requested_identity' type='hidden' value='{$identity|escape}'>
            <input type="submit" value="Become A {$class.class_name|escape} Ninja" class="formButton">
          </div>
        </form>
          {/foreach}
        {elseif $classChangeSequence eq 2}
        <p>
        The monk tosses white powder in your face. You blink at the pain, and when you open your eyes, everything looks different somehow.</p>
        <p>The white monk grins at you and walks slowly back to the dojo.</p>
        {/if}
        <hr>
        <!-- End of class changing special event display -->
	{/if}

<div style='border-bottom: 1px red dashed'><a href="chart.php">View the scroll of kills needed for each Rank</a></div>

<p>Your current level is {$userLevel|escape}. Your current kills are {$userKills|escape}.</p>
<p>Level {$nextLevel|escape} requires {$required_kills|escape} kills.</p>
<p>Your current class is <span class='class-name {$possibly_changed_class_theme}'>{$possibly_changed_class_name|escape}</span>.</p>

	{if $upgrade_requested}
		{if $userLevel+1 > $max_level}
<div>There are no trainers that can teach you beyond your current skill. You are legendary among the ninja.</div>
		{elseif $userKills < $required_kills}
<div>You do not have enough kills to proceed at this time.</div>
		{else}
		<!-- ************************** GLORIOUS LEVEL UP MESSAGE!  ************************ -->
	<div class='ninja-notice'>
		Your trainer puts you through your paces and you learn a great deal from your bruises. Welcome to Ninja Rank {$userLevel|escape}!
	</div>
		<p>Your strength is now {$char_data.strength}.</p>
		<p>Your speed is now {$char_data.speed}.</p>
		<p>Your stamina is now {$char_data.stamina}.</p>
		<p>Your Karma changed only a little, and is now {$char_data.karma}.</p>
		<p>Your Ki changed only a little, and is now {$char_data.ki}.</p>
		{/if}
	{/if}
	{if $userLevel + 1 gt $max_level}
<div>You enter the dojo as one of the elite ninja. No trainer has anything left to teach you.</div>
	{elseif $userKills lt $required_kills}
<div>Your trainer finds you lacking. You are instructed to prove your might against more ninja before you return.</div>
	{else}
<form id="level_up" action="dojo.php" method="post" name="level_up">
  <div style='margin-top: 10px;margin-bottom: 10px;'>
    <div>Do you wish to upgrade to level {$nextLevel|escape}?</div>
    <input id="upgrade" type="hidden" value="1" name="upgrade">
    <input type="submit" value="Upgrade" class="formButton">
  </div>
</form>
	{/if}
	
<!-- End of logged in display section -->
{/if}
