{**
 * Shows a table of attacker/target stats for use after combat
 *}
    <table style="border: 0;">
      <tr>
        <th colspan="3">Results of the Attack</th>
      </tr>
      <tr>
        <td>Name</td>
        <td>Total Dmg</td>
        <td>HP</td>
      </tr>

      <tr>
        <td>{$attacker->name()|escape}</td>
        <td style="text-align: center;">{$starting_attacker->health - $attacker->health|escape}</td>
        <td>
{if $attacker->health lt 100} {* Makes your health red if you go below 100 hitpoints. *}
          <span style="color:red;font-weight:bold;">
{else} {* Normal text color for health. *}
          <span style="color:brown;font-weight:normal;">
{/if}

            {$attacker->health|escape}
          </span>
        </td>
      </tr>
      <tr>
        <td>{$target->name()|escape}</td>
        <td style="text-align: center;">{$starting_target->health - $target->health|escape}</td>
        <td>{$target->health}</td>
      </tr>
    </table>
    <hr>
