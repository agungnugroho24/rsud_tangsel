/**
 * CSHtmlArea class file.
 *
 * @author Cláudio César Monteiro dos Santos Júnior <claudiocmsj[at]gmail[dot]com>
 * @version $Revision: 1.0 $Date: 2009-07-08 10:04 
 */
var CSHtmlArea = Class.create();
CSHtmlArea.prototype = {
	/**
	 * @constructor
	 */
	initialize: function (Options) {
		var oFCKeditor = new FCKeditor(Options.ID,Options.Width,Options.Height,Options.ToolbarSet);
		oFCKeditor.BasePath = Options.BasePath;
		oFCKeditor.Config['CustomConfigurationsPath'] = Options.CustomConfigurationsPath+'?'+(new Date * 1);
		oFCKeditor.ReplaceTextarea();
	}
}