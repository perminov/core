<?php
class Indi_View_Helper_SiteFooter extends Indi_View_Helper_Abstract{
	public function siteFooter(){
		ob_start();?>
	  </div>
	</td>
	<td width="200">&nbsp;</td>
  </tr>
  <tr height="65">
	<td colspan="4">footer will be here</td>
  </tr>
  </table>
</body>
</html>
		<?return ob_get_clean();
	}
}