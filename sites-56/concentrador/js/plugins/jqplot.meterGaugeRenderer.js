/**
 * jqPlot
 * Pure JavaScript plotting plugin using jQuery
 *
 * Version: 1.0.0b2_r1012
 *
 * Copyright (c) 2009-2011 Chris Leonello
 * jqPlot is currently available for use in all personal or commercial projects 
 * under both the MIT (http://www.opensource.org/licenses/mit-license.php) and GPL 
 * version 2.0 (http://www.gnu.org/licenses/gpl-2.0.html) licenses. This means that you can 
 * choose the license that best suits your project and use it accordingly. 
 *
 * Although not required, the author would appreciate an email letting him 
 * know of any substantial use of jqPlot.  You can reach the author at: 
 * chris at jqplot dot com or see http://www.jqplot.com/info.php .
 *
 * If you are feeling kind and generous, consider supporting the project by
 * making a donation at: http://www.jqplot.com/donate.php .
 *
 * sprintf functions contained in jqplot.sprintf.js by Ash Searle:
 *
 *     version 2007.04.27
 *     author Ash Searle
 *     http://hexmen.com/blog/2007/03/printf-sprintf/
 *     http://hexmen.com/js/sprintf.js
 *     The author (Ash Searle) has placed this code in the public domain:
 *     "This code is unrestricted: you are free to use it however you like."
 * 
 */
(function($) {
    /**
     * Class: $.jqplot.MeterGaugeRenderer
     * Plugin renderer to draw a meter gauge chart.
     * 
     * Data consists of a single series with 1 data point to position the gauge needle.
     * 
     * To use this renderer, you need to include the 
     * meter gauge renderer plugin, for example:
     * 
     * > <script type="text/javascript" src="plugins/jqplot.meterGaugeRenderer.js"></script>
     * 
     * Properties described here are passed into the $.jqplot function
     * as options on the series renderer.  For example:
     * 
     * > plot0 = $.jqplot('chart0',[[18]],{
     * >     title: 'Network Speed',
     * >     seriesDefaults: {
     * >         renderer: $.jqplot.MeterGaugeRenderer,
     * >         rendererOptions: {
     * >             label: 'MB/s'
     * >         }
     * >     }
     * > });
     * 
     * A meterGauge plot does not support events.
     */
    $.jqplot.MeterGaugeRenderer = function(){
        $.jqplot.LineRenderer.call(this);
    };
    
    $.jqplot.MeterGaugeRenderer.prototype = new $.jqplot.LineRenderer();
    $.jqplot.MeterGaugeRenderer.prototype.constructor = $.jqplot.MeterGaugeRenderer;
    
    // called with scope of a series
    $.jqplot.MeterGaugeRenderer.prototype.init = function(options) {
        // Group: Properties
        //
        // prop: diameter
        // Outer diameter of the meterGauge, auto computed by default
        this.diameter = null;
        // prop: padding
        // padding between the meterGauge and plot edges, auto
        // calculated by default.
        this.padding = null;
        // prop: shadowOffset
        // offset of the shadow from the gauge ring and 